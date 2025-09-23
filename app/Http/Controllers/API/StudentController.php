<?php

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Helpers\Validators;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StudentStoreRequest;
use App\Http\Requests\Student\StudentUpdateRequest;
use App\Models\Student;
use App\Models\Loan;
use App\Models\StudentSchoolClass;
use App\Models\View\SchoolClass;
use App\Models\View\Student as ViewStudent;
use App\Models\View\StudentSchoolClass as ViewStudentSchoolClass;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = $this->buildStudentQuery($request);

            $perPage = max(5, min((int) $request->input('perPage', 10), 250));

            $students = $query->select([
                'student_id',
                'name',
                'email',
                'phone',
                'can_borrow',
                'formatted_class_name',
                'course',
                'period',
                'term'
            ])->paginate($perPage)->appends($request->all());

            $html = view('pages.students.partials.table', compact('students'))->render();
            $paginationHtml = view('vendor.pagination.custom', ['paginator' => $students])->render();

            $filterData = ViewStudentSchoolClass::getFilterData($query);

            return response()->json([
                'html' => $html,
                'paginationHtml' => $paginationHtml,
                'filterData' => $filterData,
            ]);
        } catch (Throwable $e) {
            $this->logError('Erro ao listar alunos.', $e);
            return $this->internalErrorResponse($e, 'Erro interno ao listar os alunos.');
        }
    }

    public function store(StudentStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $classCriteria = array_intersect_key($data, array_flip(['course', 'term', 'period']));
            $class = SchoolClass::where($classCriteria)->first();

            if (!$class) {
                return $this->notFoundResponse('Turma não encontrada.');
            }

            DB::transaction(function () use ($data, $class, &$student) {
                $studentData = array_filter(
                    array_intersect_key($data, array_flip(['name', 'cpf', 'email', 'phone'])),
                    fn($v) => $v !== null && $v !== ''
                );

                $student = Student::create($studentData);

                StudentSchoolClass::create([
                    'student_id' => Utils::convertUuidToBinary($student->id),
                    'school_class_id' => Utils::convertUuidToBinary($class->id),
                ]);
            });

            return $this->createdResponse();
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('Erro ao cadastrar aluno.', $e, ['data' => $data]);
            return $this->internalErrorResponse($e, 'Erro interno ao cadastrar aluno.');
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $student = ViewStudent::findOrFail($binaryId);

            return $this->successResponse($student->toArray());
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Aluno não encontrado.');
        } catch (Throwable $e) {
            $this->logError('Erro ao buscar aluno.', $e, ['student_id' => $id]);
            return $this->internalErrorResponse($e, 'Erro interno ao buscar aluno.');
        }
    }

    public function update(StudentUpdateRequest $request, string $id): JsonResponse
    {
        $data = $request->validated();

        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $student = Student::findOrFail($binaryId);

            $studentData = array_filter(
                array_intersect_key($data, array_flip(['name', 'cpf', 'email', 'phone'])),
                fn($v) => $v !== null && $v !== ''
            );

            $student->update($studentData);

            $classData = collect($data)->only(['course', 'term', 'period'])->toArray();

            if (count(array_filter($classData)) > 0) {
                if (in_array(null, $classData, true)) {
                    return $this->badRequestResponse([
                        'school_class' => 'Para alterar a turma, informe curso, regime e período.'
                    ]);
                }

                $class = SchoolClass::where($classData)->first();

                if (!$class) {
                    return $this->notFoundResponse('Turma não encontrada.');
                }

                $studentSchoolClass = StudentSchoolClass::where('student_id', $student->id)->first();

                if ($studentSchoolClass && $studentSchoolClass->class_id !== $class->id) {
                    $studentSchoolClass->update(['class_id' => $class->id]);
                }
            }

            return $this->noContentResponse();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Aluno não encontrado.');
        } catch (Throwable $e) {
            $this->logError('Erro ao atualizar aluno.', $e, [
                'student_id' => $id,
                'data' => $data,
            ]);
            return $this->internalErrorResponse($e, 'Erro interno ao atualizar aluno.');
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $student = Student::findOrFail($binaryId);

            $activeLoans = Loan::where('student_id', $binaryId)
                ->where('returned_date', null)
                ->exists();

            if ($activeLoans) {
                return $this->conflictResponse(['loan' => 'Aluno está com empréstimos ativos.']);
            }

            $student->delete();

            return $this->noContentResponse();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Aluno não encontrado.');
        } catch (Throwable $e) {
            $this->logError('Erro ao excluir aluno.', $e, ['student_id' => $id]);
            return $this->internalErrorResponse($e, 'Erro interno ao excluir aluno.');
        }
    }

    /**
     * Build student query with search, filters, and sorting.
     *
     * @param Request $request
     * @return Builder
     */
    private function buildStudentQuery(Request $request): Builder
    {
        $query = ViewStudentSchoolClass::query();

        if ($request->filled('search')) {
            $query = $this->applyStudentSearch($query, $request->search);
        }

        if ($request->filled('course')) {
            $query->where('course', $request->course);
        }
        if ($request->filled('period')) {
            $query->where('period', $request->period);
        }
        if ($request->filled('term')) {
            $query->where('term', $request->term);
        }
        if ($request->filled('can_borrow')) {
            $query->where('can_borrow', $request->can_borrow);
        }

        return $this->applyStudentSorting($query, $request);
    }

    /**
     * Apply search filters for students by email, CPF, phone, or name.
     *
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    private function applyStudentSearch($query, string $search): Builder
    {
        $search = trim($search);
        $numericSearch = preg_replace('/\D/', '', $search);

        $isEmail = Validators::validateEmail($search);
        $isCpf = Validators::validateCpf($numericSearch);
        $isPhone = Validators::validatePhoneNumber($numericSearch);

        if ($isEmail) {
            $query->where('email_hash', hash('sha256', $search, true));
        } elseif ($isCpf) {
            $query->where('cpf_hash', hash('sha256', $numericSearch, true));
        } elseif ($isPhone) {
            $query->where('phone_hash', hash('sha256', $numericSearch, true));
        } else {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query;
    }

    /**
     * Apply sorting based on allowed columns.
     *
     * @param Builder $query
     * @param Request $request
     * @return Builder
     */
    private function applyStudentSorting($query, Request $request): Builder
    {
        $sortable = ['name', 'formatted_class_name'];
        $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'name';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sort, $direction);
    }
}
