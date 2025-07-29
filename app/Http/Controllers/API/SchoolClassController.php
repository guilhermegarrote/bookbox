<?php

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolClass\SchoolClassStoreRequest;
use App\Http\Requests\SchoolClass\SchoolClassUpdateRequest;
use App\Models\SchoolClass;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class SchoolClassController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $classes = SchoolClass::orderBy('course')->paginate(10);

            return $this->successResponse($classes->toArray());
        } catch (Exception $e) {
            $this->logError('Erro ao listar turmas.', $e);
            return $this->internalErrorResponse($e, 'Erro interno ao listar as turmas.');
        }
    }

    public function store(SchoolClassStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $classData = array_filter(
                array_intersect_key($data, array_flip(['course', 'regime', 'start_date', 'end_date'])),
                fn($v) => $v !== null && $v !== ''
            );

            $existingClass = SchoolClass::where($classData)->exists();

            if ($existingClass) {
                return $this->conflictResponse(['school_class' => 'Já existe uma turma cadastrada com esses dados.']);
            }

            SchoolClass::create($classData);

            return $this->createdResponse();
        } catch (Exception $e) {
            $this->logError('Erro ao cadastrar turma.', $e, ['data' => $data]);
            return $this->internalErrorResponse($e, 'Erro interno ao cadastrar turma.');
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $schoolClass = SchoolClass::findOrFail($binaryId);

            return $this->successResponse($schoolClass->toArray());
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Turma não encontrada.');
        } catch (\Exception $e) {
            $this->logError('Erro ao buscar turma.', $e, ['school_class_id' => $id]);
            return $this->internalErrorResponse($e, 'Erro interno ao buscar turma.');
        }
    }

    public function update(SchoolClassUpdateRequest $request, string $id): JsonResponse
    {
        $data = $request->validated();

        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $schoolClass = SchoolClass::findOrFail($binaryId);

            $updatedData = array_merge(
                [
                    'course' => $schoolClass->course,
                    'regime' => $schoolClass->regime,
                    'start_date' => $schoolClass->start_date,
                    'end_date' => $schoolClass->end_date,
                ],
                array_filter(
                    array_intersect_key($data, array_flip(['course', 'regime', 'start_date', 'end_date'])),
                    fn($v) => $v !== null && $v !== ''
                )
            );

            $duplicateClassExists = SchoolClass::where($updatedData)
                ->where('id', '!=', $binaryId)
                ->exists();

            if ($duplicateClassExists) {
                return $this->conflictResponse(['school_class' => 'Já existe uma turma cadastrada com esses dados.']);
            }

            $schoolClass->update($updatedData);

            return $this->noContentResponse();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Turma não encontrada.');
        } catch (\Exception $e) {
            $this->logError('Erro ao atualizar turma.', $e, [
                'school_class_id' => $id,
                'data' => $data,
            ]);
            return $this->internalErrorResponse($e, 'Erro interno ao atualizar turma.');
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $schoolClass = SchoolClass::findOrFail($binaryId);

            $hasActiveLoans = DB::table('loans')
                ->join('students', 'loans.student_id', '=', 'students.id')
                ->join('student_school_class', 'student_school_class.student_id', '=', 'students.id')
                ->where('student_school_class.school_class_id', $binaryId)
                ->where('loans.active', true)
                ->exists();

            if ($hasActiveLoans) {
                return $this->validationErrorResponse(['school_class' => 'Não é possível excluir a turma, pois há alunos com empréstimos ativos.']);
            }

            $schoolClass->delete();

            return $this->noContentResponse();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Turma não encontrada.');
        } catch (\Exception $e) {
            $this->logError('Erro ao excluir turma.', $e, ['school_class_id' => $id]);
            return $this->internalErrorResponse($e, 'Erro interno ao excluir a turma.');
        }
    }
}
