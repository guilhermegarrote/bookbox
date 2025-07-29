<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

trait JsonResponseTrait
{
    protected function successResponse(array $data = []): JsonResponse
    {
        return response()->json([
            'data' => $data,
        ], 200);
    }

    protected function createdResponse(array $data = []): JsonResponse
    {
        return response()->json([
            'data' => $data,
        ], 201);
    }

    protected function acceptedResponse(): JsonResponse
    {
        return response()->json(null, 202);
    }

    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, 204);
    }

    protected function resetContentResponse(): JsonResponse
    {
        return response()->json(null, 205);
    }

    protected function badRequestResponse(array $errors): JsonResponse
    {
        return response()->json([
            'error' => $errors,
        ], 400);
    }

    protected function unauthorizedResponse(string $message = 'Não autorizado.'): JsonResponse
    {
        return response()->json([
            'error' => ['message' => $message],
        ], 401);
    }

    protected function forbiddenResponse(string $message = 'Acesso negado.'): JsonResponse
    {
        return response()->json([
            'error' => ['message' => $message],
        ], 403);
    }

    protected function notFoundResponse(string $message = 'Recurso não encontrado.'): JsonResponse
    {
        return response()->json([
            'error' => ['message' => $message],
        ], 404);
    }

    protected function conflictResponse(array $errors): JsonResponse
    {
        return response()->json([
            'error' => $errors,
        ], 409);
    }

    protected function validationErrorResponse(array $errors): JsonResponse
    {
        return response()->json([
            'error' => $errors,
        ], 422);
    }

    protected function tooManyRequestsResponse(string $message = 'Muitas requisições. Tente novamente mais tarde.'): JsonResponse
    {
        return response()->json([
            'error' => ['message' => $message],
        ], 429);
    }

    protected function internalErrorResponse(\Throwable $e, string $message = 'Erro interno.'): JsonResponse
    {
        Log::error($message, ['exception' => $e]);

        return response()->json([
            'error' => $message,
            'details' => config('app.debug') ? iconv('UTF-8', 'UTF-8//IGNORE', $e->getMessage()) : null,
        ], 500);
    }
}
