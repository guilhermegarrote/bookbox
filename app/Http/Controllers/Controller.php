<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Traits\ErrorLoggerTrait;
use App\Http\Traits\JsonResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Base Controller for all application controllers.
 *
 * This class serves as the foundation for all controllers in the system.
 * It includes essential Laravel traits and custom traits that standardize
 * error handling, JSON response formatting, and authorization logic.
 *
 * Traits:
 * - AuthorizesRequests: Provides authorization logic for controller actions.
 * - DispatchesJobs: Enables dispatching of queued jobs.
 * - ValidatesRequests: Adds request validation utilities.
 * - JsonResponseTrait: Custom helper for consistent JSON API responses.
 * - ErrorLoggerTrait: Centralized error logging for exceptions and failures.
 */
class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;
    use JsonResponseTrait;
    use ErrorLoggerTrait;
}
