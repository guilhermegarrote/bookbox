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
            $query = ViewStudentSchoolClass::query();

            $query->when($request->filled('course'), fn($q) => $q->where('course', $request->course));
            $query->when($request->filled('period'), fn($q) => $q->where('period', $request->period));
            $query->when($request->filled('term'), fn($q) => $q->where('term', $request->term));
            $query->when($request->filled('can_borrow'), fn($q) => $q->where('can_borrow', $request->can_borrow));

            if ($request->filled('search')) {
                $search = trim($request->search);
                $numericSearch = preg_replace('/\D/', '', $search);

                $searchIsEmail = Validators::validateEmail($search);
                $searchIsCpf = Validators::validateCpf($numericSearch);
                $searchIsPhone = Validators::validatePhoneNumber($numericSearch);

                $query->when($searchIsEmail, fn($q) => $q->where('email_hash', hash('sha256', $search, true)))
                    ->when($searchIsCpf, fn($q) => $q->where('cpf_hash', hash('sha256', $numericSearch, true)))
                    ->when($searchIsPhone, fn($q) => $q->where('phone_hash', hash('sha256', $numericSearch, true)))
                    ->unless(
                        $searchIsEmail || $searchIsCpf || $searchIsPhone,
                        fn($q) => $q->where('name', 'like', "%{$search}%")
                    );
            }

            $sortable = ['name', 'formatted_class_name'];
            $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'name';
            $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
            $perPage = max(5, min((int) $request->input('perPage', 10), 250));

            $students = $query
                ->select([
                    'student_id',
                    'name',
                    'email',
                    'phone',
                    'can_borrow',
                    'formatted_class_name'
                ])
                ->orderBy($sort, $direction)
                ->paginate($perPage)
                ->appends($request->all());

            $html = view('pages.students.partials.table', compact('students'))->render();
            $paginationHtml = view('vendor.pagination.custom', ['paginator' => $students])->render();

            return response()->json([
                'html' => $html,
                'paginationHtml' => $paginationHtml,
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

            DB::beginTransaction();

            $studentData = array_filter(
                array_intersect_key($data, array_flip(['name', 'cpf', 'email', 'phone'])),
                fn($v) => $v !== null && $v !== ''
            );

            $student = Student::create($studentData);

            StudentSchoolClass::create([
                'student_id' => Utils::convertUuidToBinary($student->id),
                'school_class_id' => Utils::convertUuidToBinary($class->id),
            ]);

            DB::commit();

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

            $updatedData = array_merge(
                [
                    'name' => $student->name,
                    'cpf' => $student->cpf,
                    'email' => $student->email,
                    'phone' => $student->phone,
                ],
                array_filter(
                    array_intersect_key($data, array_flip(['name', 'cpf', 'email', 'phone'])),
                    fn($v) => $v !== null && $v !== ''
                )
            );

            $student->update($updatedData);

            $classData = collect($data)->only(['course', 'term', 'period'])->toArray();

            if (count(array_filter($classData)) > 0) {
                if (in_array(null, $classData, true)) {
                    return $this->badRequestResponse(['school_class' => 'Para alterar a turma, informe curso, regime e período.']);
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
                ->where('active', true)
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
}
