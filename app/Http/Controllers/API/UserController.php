<?php

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Throwable;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $users = User::orderBy('name')->paginate(10);

            return $this->successResponse($users->toArray());
        } catch (Throwable $e) {
            $this->logError('Erro ao listar usuários.', $e);
            return $this->internalErrorResponse($e, 'Erro interno ao listar os usuários.');
        }
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $userData = array_filter(
                array_intersect_key($data, array_flip(['name', 'email', 'password'])),
                fn($v) => $v !== null && $v !== ''
            );

            User::create($userData);

            return $this->createdResponse();
        } catch (Throwable $e) {
            $this->logError('Erro ao cadastrar usuário.', $e, ['data' => $data]);
            return $this->internalErrorResponse($e, 'Erro interno ao cadastrar usuário.');
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $user = User::findOrFail($binaryId);

            return $this->successResponse($user->toArray());
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Usuário não encontrado.');
        } catch (Throwable $e) {
            $this->logError('Erro ao buscar usuário.', $e, ['user_id' => $id]);
            return $this->internalErrorResponse($e, 'Erro interno ao buscar usuário.');
        }
    }

    public function update(UserUpdateRequest $request, string $id): JsonResponse
    {
        $data = $request->validated();

        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $user = User::findOrFail($binaryId);

            $userData = array_filter(
                array_intersect_key($data, array_flip(['name', 'email', 'password'])),
                fn($v) => $v !== null && $v !== ''
            );

            $user->update($userData);

            return $this->noContentResponse();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Usuário não encontrado.');
        } catch (Throwable $e) {
            $this->logError('Erro ao atualizar usuário.', $e, [
                'user_id' => $id,
                'data' => $data,
            ]);
            return $this->internalErrorResponse($e, 'Erro interno ao atualizar usuário.');
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $user = User::findOrFail($binaryId);

            $user->delete();

            return $this->noContentResponse();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Usuário não encontrado.');
        } catch (Throwable $e) {
            $this->logError('Erro ao excluir usuário.', $e, ['user_id' => $id]);
            return $this->internalErrorResponse($e, 'Erro interno ao excluir usuário.');
        }
    }
}
