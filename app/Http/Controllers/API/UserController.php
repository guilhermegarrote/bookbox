<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

/**
 * Controller responsible for managing users (CRUD operations).
 *
 * This controller provides endpoints for listing, creating,
 * retrieving, updating, and deleting users in a paginated manner.
 */
class UserController extends Controller
{
    /**
     * Retrieve a paginated list of users ordered by name.
     *
     * @return JsonResponse JSON response containing paginated user data
     */
    public function index(): JsonResponse
    {
        try {
            $users = User::orderBy('name')->paginate(10);

            return $this->successResponse($users->toArray());
        } catch (\Throwable $e) {
            $this->logError('Erro ao listar usuários.', $e);

            return $this->internalErrorResponse($e, 'Erro interno ao listar os usuários.');
        }
    }

    /**
     * Store a new user in the database.
     *
     * @param UserStoreRequest $request the validated request containing user data
     *
     * @return JsonResponse JSON response confirming user creation
     */
    public function store(UserStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $userData = array_filter(
                array_intersect_key($data, array_flip(['name', 'email', 'password'])),
                fn (mixed $v): bool => $v !== null && $v !== '',
            );

            User::create($userData);

            return $this->createdResponse();
        } catch (\Throwable $e) {
            $this->logError('Erro ao cadastrar usuário.', $e, ['data' => $data]);

            return $this->internalErrorResponse($e, 'Erro interno ao cadastrar usuário.');
        }
    }

    /**
     * Display details for a specific user.
     *
     * @param string $id the UUID (string) of the user
     *
     * @return JsonResponse JSON response containing user details
     */
    public function show(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $user = User::findOrFail($binaryId);

            return $this->successResponse($user->toArray());
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Usuário não encontrado.');
        } catch (\Throwable $e) {
            $this->logError('Erro ao buscar usuário.', $e, ['user_id' => $id]);

            return $this->internalErrorResponse($e, 'Erro interno ao buscar usuário.');
        }
    }

    /**
     * Update an existing user's information.
     *
     * @param UserUpdateRequest $request the validated request containing updated user data
     * @param string $id the UUID (string) of the user to update
     *
     * @return JsonResponse JSON response with no content upon success
     */
    public function update(UserUpdateRequest $request, string $id): JsonResponse
    {
        $data = $request->validated();

        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $user = User::findOrFail($binaryId);

            $userData = array_filter(
                array_intersect_key($data, array_flip(['name', 'email', 'password'])),
                fn (mixed $v): bool => $v !== null && $v !== '',
            );

            $user->update($userData);

            return $this->noContentResponse();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Usuário não encontrado.');
        } catch (\Throwable $e) {
            $this->logError('Erro ao atualizar usuário.', $e, [
                'user_id' => $id,
                'data' => $data,
            ]);

            return $this->internalErrorResponse($e, 'Erro interno ao atualizar usuário.');
        }
    }

    /**
     * Delete a user by ID.
     *
     * @param string $id the UUID (string) of the user to delete
     *
     * @return JsonResponse JSON response with no content upon successful deletion
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $user = User::findOrFail($binaryId);

            $user->delete();

            return $this->noContentResponse();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Usuário não encontrado.');
        } catch (\Throwable $e) {
            $this->logError('Erro ao excluir usuário.', $e, ['user_id' => $id]);

            return $this->internalErrorResponse($e, 'Erro interno ao excluir usuário.');
        }
    }
}
