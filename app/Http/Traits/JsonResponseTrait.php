<?php

declare(strict_types=1);

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Trait JsonResponseTrait.
 *
 * Provides standardized JSON API responses with consistent structure and
 * appropriate HTTP status codes.
 *
 * This trait is intended for use in controllers or services that return
 * JSON-based API responses. It helps to ensure predictable formatting for
 * both success and error responses.
 */
trait JsonResponseTrait
{
    /**
     * Return a 200 OK JSON response.
     *
     * @param array $data response payload
     */
    protected function successResponse(array $data = []): JsonResponse
    {
        return response()->json(['data' => $data], 200);
    }

    /**
     * Return a 201 Created JSON response.
     *
     * @param array $data newly created resource data
     */
    protected function createdResponse(array $data = []): JsonResponse
    {
        return response()->json(['data' => $data], 201);
    }

    /**
     * Return a 202 Accepted JSON response (request accepted for processing).
     */
    protected function acceptedResponse(): JsonResponse
    {
        return response()->json(null, 202);
    }

    /**
     * Return a 204 No Content JSON response.
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Return a 205 Reset Content JSON response.
     */
    protected function resetContentResponse(): JsonResponse
    {
        return response()->json(null, 205);
    }

    /**
     * Return a 400 Bad Request JSON response.
     *
     * @param array $errors validation or request errors
     */
    protected function badRequestResponse(array $errors): JsonResponse
    {
        return response()->json(['error' => $errors], 400);
    }

    /**
     * Return a 401 Unauthorized JSON response.
     *
     * @param string $message optional error message
     */
    protected function unauthorizedResponse(string $message = 'Não autorizado.'): JsonResponse
    {
        return response()->json(['error' => ['message' => $message]], 401);
    }

    /**
     * Return a 403 Forbidden JSON response.
     *
     * @param string $message optional error message
     */
    protected function forbiddenResponse(string $message = 'Acesso negado.'): JsonResponse
    {
        return response()->json(['error' => ['message' => $message]], 403);
    }

    /**
     * Return a 404 Not Found JSON response.
     *
     * @param string $message optional error message
     */
    protected function notFoundResponse(string $message = 'Recurso não encontrado.'): JsonResponse
    {
        return response()->json(['error' => ['message' => $message]], 404);
    }

    /**
     * Return a 409 Conflict JSON response.
     *
     * @param array $errors conflict details
     */
    protected function conflictResponse(array $errors): JsonResponse
    {
        return response()->json(['error' => $errors], 409);
    }

    /**
     * Return a 422 Unprocessable Entity JSON response for validation errors.
     *
     * @param array $errors validation error details
     */
    protected function validationErrorResponse(array $errors): JsonResponse
    {
        return response()->json(['error' => $errors], 422);
    }

    /**
     * Return a 429 Too Many Requests JSON response.
     *
     * @param string $message optional rate limit message
     */
    protected function tooManyRequestsResponse(string $message = 'Muitas requisições. Tente novamente mais tarde.'): JsonResponse
    {
        return response()->json(['error' => ['message' => $message]], 429);
    }

    /**
     * Return a 500 Internal Server Error JSON response.
     *
     * Logs the exception and returns a standardized JSON error message.
     *
     * @param \Throwable $e the thrown exception
     * @param string $message optional user-friendly message
     */
    protected function internalErrorResponse(\Throwable $e, string $message = 'Erro interno.'): JsonResponse
    {
        return response()->json([
            'error' => $message,
            'details' => config('app.debug')
                ? iconv('UTF-8', 'UTF-8//IGNORE', $e->getMessage())
                : null,
        ], 500);
    }
}
