<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolClass\SchoolClassStoreRequest;
use App\Http\Requests\SchoolClass\SchoolClassUpdateRequest;
use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

/**
 * Controller responsible for managing school classes (CRUD operations).
 *
 * Provides endpoints to listing, creation, updating, retrieval, and deletion of school classes.
 */
class SchoolClassController extends Controller
{
    /**
     * Display a paginated list of school classes.
     *
     * @return JsonResponse JSON containing paginated school classes
     */
    public function index(): JsonResponse
    {
        try {
            $classes = SchoolClass::orderBy('course')
                ->paginate(10)
            ;

            return $this->successResponse($classes->toArray());
        } catch (\Throwable $e) {
            $this->logError('Erro ao listar turmas.', $e);

            return $this->internalErrorResponse($e, 'Erro interno ao listar as turmas.');
        }
    }

    /**
     * Store a new school class in the database.
     *
     * @param SchoolClassStoreRequest $request Validated request data
     *
     * @return JsonResponse JSON response indicating creation status
     */
    public function store(SchoolClassStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            SchoolClass::create($data);

            return $this->createdResponse();
        } catch (\Throwable $e) {
            $this->logError('Erro ao cadastrar turma.', $e, ['data' => $data]);

            return $this->internalErrorResponse($e, 'Erro interno ao cadastrar turma.');
        }
    }

    /**
     * Display the specified school class.
     *
     * @param string $id UUID of the school class
     *
     * @return JsonResponse JSON response with school class data
     */
    public function show(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $schoolClass = SchoolClass::findOrFail($binaryId);

            return $this->successResponse($schoolClass->toArray());
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Turma não encontrada.');
        } catch (\Throwable $e) {
            $this->logError('Erro ao buscar turma.', $e, ['school_class_id' => $id]);

            return $this->internalErrorResponse($e, 'Erro interno ao buscar turma.');
        }
    }

    /**
     * Update the specified school class in the database.
     *
     * Prevents duplication by checking existing classes with same data.
     *
     * @param SchoolClassUpdateRequest $request Validated request data
     * @param string $id UUID of the school class to update
     *
     * @return JsonResponse JSON response indicating update status
     */
    public function update(SchoolClassUpdateRequest $request, string $id): JsonResponse
    {
        $data = $request->validated();

        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $schoolClass = SchoolClass::findOrFail($binaryId);

            $updatedData = array_merge(
                [
                    'course' => $schoolClass->course,
                    'term' => $schoolClass->term,
                    'start_date' => $schoolClass->start_date,
                    'end_date' => $schoolClass->end_date,
                ],
                array_filter(
                    array_intersect_key($data, array_flip(['course', 'term', 'start_date', 'end_date'])),
                    fn ($v) => $v !== null && $v !== '',
                ),
            );

            $duplicateClassExists = SchoolClass::where($updatedData)
                ->where('id', '!=', $binaryId)
                ->exists()
            ;

            if ($duplicateClassExists) {
                return $this->conflictResponse(['school_class' => 'Já existe uma turma cadastrada com esses dados.']);
            }

            $schoolClass->update($updatedData);

            return $this->noContentResponse();
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Turma não encontrada.');
        } catch (\Throwable $e) {
            $this->logError('Erro ao atualizar turma.', $e, [
                'school_class_id' => $id,
                'data' => $data,
            ]);

            return $this->internalErrorResponse($e, 'Erro interno ao atualizar turma.');
        }
    }

    /**
     * Remove the specified school class from the database.
     *
     * Cannot delete if there are students with active loans.
     *
     * @param string $id UUID of the school class to delete
     *
     * @return JsonResponse JSON response indicating deletion status
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $schoolClass = SchoolClass::findOrFail($binaryId);

            $hasActiveLoans = $schoolClass->students()
                ->whereHas('loans', fn ($q) => $q->whereNull('returned_date'))
                ->exists()
            ;

            if ($hasActiveLoans) {
                return $this->validationErrorResponse([
                    'school_class' => 'Não é possível excluir a turma, pois há alunos com empréstimos ativos.',
                ]);
            }

            $schoolClass->delete();

            return $this->noContentResponse();
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Turma não encontrada.');
        } catch (\Throwable $e) {
            $this->logError('Erro ao excluir turma.', $e, ['school_class_id' => $id]);

            return $this->internalErrorResponse($e, 'Erro interno ao excluir a turma.');
        }
    }
}
