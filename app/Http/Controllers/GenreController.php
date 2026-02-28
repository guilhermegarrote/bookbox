<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\genre;

/**
 * Controller responsible for handling pages and modals related to genre.
 */
class GenreController extends Controller
{
    /**
     * Display the modal view for creating a new genre.
     *
     * @return string the rendered HTML for the creation modal
     *
     * @see resources/views/pages/genres/partials/create-modal.blade.php
     */
    public function createModal(): string
    {
        return view('pages.genres.partials.create-modal')->render();
    }

    /**
     * Display the modal view for updating a specific genre.
     *
     * @param string $id the UUID of the genre
     *
     * @return string the rendered HTML for the update modal
     *
     * @see resources/views/pages/genres/partials/update-modal.blade.php
     */
    public function updateModal(string $id): string
    {
        $genre = genre::where('id', Utils::convertUuidToBinary($id))->firstOrFail();

        return view('pages.genres.partials.update-modal', compact('genre'))->render();
    }
}
