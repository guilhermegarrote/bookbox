<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Helpers\Validators;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StudentStoreRequest;
use App\Http\Requests\Student\StudentUpdateRequest;
use App\Models\Loan;
use App\Models\Student;
use App\Models\StudentSchoolClass;
use App\Models\View\SchoolClass;
use App\Models\View\Student as ViewStudent;
use App\Models\View\StudentSchoolClass as ViewStudentSchoolClass;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller responsible for managing students (CRUD operations).
 *
 * Provides endpoints to list, create, retrieve, update, and delete students.
 */
class StudentController extends Controller
{
    /**
     * Retrieve a paginated list of students with optional filters, search, and sorting.
     *
     * @param Request $request the HTTP request containing search, filter, and pagination parameters
     *
     * @return JsonResponse JSON response containing the rendered HTML table, pagination, and filter data,
     *                      or an internal error message in case of failure
     */
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
                'term',
            ])->paginate($perPage)->appends($request->all());

            $html = view('pages.students.partials.table', compact('students'))->render();
            $paginationHtml = view('vendor.pagination.custom', ['paginator' => $students])->render();

            $filterData = ViewStudentSchoolClass::getFilterData($query);

            return response()->json([
                'html' => $html,
                'paginationHtml' => $paginationHtml,
                'filterData' => $filterData,
            ]);
        } catch (\Throwable $e) {
            $this->logError('Erro ao listar alunos.', $e);

            return $this->internalErrorResponse($e, 'Erro interno ao listar os alunos.');
        }
    }

    /**
     * Create a new student and associate them with a school class.
     *
     * @param StudentStoreRequest $request the validated request containing student and class data
     *
     * @return JsonResponse JSON response with a 201 Created status on success,
     *                      or an error message if validation or persistence fails
     */
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
                    fn ($v) => $v !== null && $v !== '',
                );

                $student = Student::create($studentData);

                StudentSchoolClass::create([
                    'student_id' => Utils::convertUuidToBinary($student->id),
                    'school_class_id' => Utils::convertUuidToBinary($class->id),
                ]);
            });

            return $this->createdResponse();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->logError('Erro ao cadastrar aluno.', $e, ['data' => $data]);

            return $this->internalErrorResponse($e, 'Erro interno ao cadastrar aluno.');
        }
    }

    /**
     * Retrieve detailed information for a specific student.
     *
     * @param string $id the UUID of the student in string format
     *
     * @return JsonResponse JSON response containing student data,
     *                      or an error message if the student does not exist
     */
    public function show(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $student = ViewStudent::findOrFail($binaryId);

            return $this->successResponse($student->toArray());
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Aluno não encontrado.');
        } catch (\Throwable $e) {
            $this->logError('Erro ao buscar aluno.', $e, ['student_id' => $id]);

            return $this->internalErrorResponse($e, 'Erro interno ao buscar aluno.');
        }
    }

    /**
     * Retrieve student information by CPF.
     *
     * @param string $cpf the CPF number of the student
     *
     * @return JsonResponse JSON response containing student data if found,
     *                      a 400 error if CPF is invalid, or 404 if not found
     */
    public function findByCpf(string $cpf): JsonResponse
    {
        try {
            if (!Validators::validateCpf($cpf)) {
                return $this->badRequestResponse(['cpf' => 'CPF inválido.']);
            }

            $student = ViewStudentSchoolClass::where('cpf_hash', hash('sha256', $cpf, true))->first();

            if (!$student) {
                return $this->notFoundResponse('Aluno não encontrado.');
            }

            return $this->successResponse($student->toArray());
        } catch (\Throwable $e) {
            $this->logError('Erro ao buscar aluno por CPF.', $e, ['cpf' => $cpf]);

            return $this->internalErrorResponse($e, 'Erro interno ao consultar o aluno.');
        }
    }

    /**
     * Update student data and optionally change their associated school class.
     *
     * @param StudentUpdateRequest $request the validated request with new student data
     * @param string $id the UUID of the student to update
     *
     * @return JsonResponse JSON response with 204 No Content on success,
     *                      or error messages for invalid data, not found, or internal errors
     */
    public function update(StudentUpdateRequest $request, string $id): JsonResponse
    {
        $data = $request->validated();

        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $student = Student::findOrFail($binaryId);

            $studentData = array_filter(
                array_intersect_key($data, array_flip(['name', 'cpf', 'email', 'phone'])),
                fn ($v) => $v !== null && $v !== '',
            );

            $student->update($studentData);

            $schoolClassData = collect($data)->only(['course', 'term', 'period'])->toArray();

            if (\count(array_filter($schoolClassData)) > 0) {
                if (\in_array(null, $schoolClassData, true)) {
                    return $this->badRequestResponse([
                        'school_class' => 'Para alterar a turma, informe curso, regime e período.',
                    ]);
                }

                $schoolClass = SchoolClass::where($schoolClassData)->first();

                if (!$schoolClass) {
                    return $this->notFoundResponse('Turma não encontrada.');
                }

                $studentSchoolClass = StudentSchoolClass::where('student_id', Utils::convertUuidToBinary($student->id))->first();

                if ($studentSchoolClass && $studentSchoolClass->school_class_id !== $schoolClass->id) {
                    $studentSchoolClass->update(['school_class_id' => Utils::convertUuidToBinary($schoolClass->id)]);
                }
            }

            return $this->noContentResponse();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Aluno não encontrado.');
        } catch (\Throwable $e) {
            $this->logError('Erro ao atualizar aluno.', $e, [
                'student_id' => $id,
                'data' => $data,
            ]);

            return $this->internalErrorResponse($e, 'Erro interno ao atualizar aluno.');
        }
    }

    /**
     * Delete a student record if they have no active loans.
     *
     * @param string $id the UUID of the student to delete
     *
     * @return JsonResponse JSON response with 204 No Content on success,
     *                      or 409 Conflict if the student has active loans, or error message on failure
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $student = Student::findOrFail($binaryId);

            $activeLoans = Loan::where('student_id', $binaryId)
                ->where('returned_date', null)
                ->exists()
            ;

            if ($activeLoans) {
                return $this->conflictResponse(['loan' => 'Aluno está com empréstimos ativos.']);
            }

            $student->delete();

            return $this->noContentResponse();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Aluno não encontrado.');
        } catch (\Throwable $e) {
            $this->logError('Erro ao excluir aluno.', $e, ['student_id' => $id]);

            return $this->internalErrorResponse($e, 'Erro interno ao excluir aluno.');
        }
    }

    /**
     * Build the main query for fetching students with filters and sorting.
     *
     * @param Request $request the HTTP request containing filter parameters
     *
     * @return Builder the configured query builder instance
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
     * Apply search filters for students (email, CPF, phone, or name).
     *
     * @param Builder $query the query builder instance
     * @param string $search the search string provided by the user
     *
     * @return Builder the updated query builder
     */
    private function applyStudentSearch(Builder $query, string $search): Builder
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
     * Apply sorting to the student query based on allowed columns.
     *
     * @param Builder $query the query builder instance
     * @param Request $request the HTTP request containing sorting parameters
     *
     * @return Builder the updated query builder
     */
    private function applyStudentSorting(Builder $query, Request $request): Builder
    {
        $sortable = ['name', 'formatted_class_name'];
        $sort = \in_array($request->input('sort'), $sortable, true) ? $request->input('sort') : 'name';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sort, $direction);
    }
}
