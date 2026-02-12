<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\User;
use App\Models\View\SchoolClass;
use Illuminate\Contracts\View\View;

/**
 * Controller responsible for handling pages and modals related to user.
 */
class UserController extends Controller
{
    /**
     * Display the modal view for creating a new user.
     *
     * @return string The rendered HTML for the creation modal.
     *
     * @see resources/views/pages/users/partials/create-modal.blade.php
     */
    public function createModal(): string
    {
        return view('pages.users.partials.create-modal')->render();
    }

    /**
     * Display the modal view for updating a specific user.
     *
     * @param string $id The UUID of the user.
     *
     * @return string The rendered HTML for the update modal.
     *
     * @see resources/views/pages/users/partials/update-modal.blade.php
     */
    public function updateModal(string $id): string
    {
        $user = User::where('id', Utils::convertUuidToBinary($id))->firstOrFail();

        return view('pages.users.partials.update-modal', compact('user'))->render();
    }

    /**
     * Display the modal view for deleting a specific user.
     *
     * @return string The rendered HTML for the delete modal.
     *
     * @see resources/views/pages/users/partials/delete-modal.blade.php
     */
    public function deleteModal(): string
    {
        return view('pages.users.partials.delete-modal')->render();
    }
}
