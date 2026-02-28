<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\View\SchoolClass;
use Carbon\Carbon;

/**
 * Controller responsible for handling pages and modals related to school classes.
 */
class SchoolClassController extends Controller
{
    /**
     * Display the modal view for creating a new school class.
     *
     * @return string the rendered HTML for the creation modal
     *
     * @see resources/views/pages/school-classes/partials/create-modal.blade.php
     */
    public function createModal(): string
    {
        return view('pages.school-classes.partials.create-modal')->render();
    }

    /**
     * Display the modal view for updating a specific school class.
     *
     * @param string $id the UUID of the school class
     *
     * @return string the rendered HTML for the update modal
     *
     * @see resources/views/pages/school-classes/partials/update-modal.blade.php
     */
    public function updateModal(string $id): string
    {
        $schoolClass = SchoolClass::where('id', Utils::convertUuidToBinary($id))->firstOrFail();

        $schoolClass['start_date'] = Carbon::parse($schoolClass->start_date)->format('d/m/Y');
        $schoolClass['end_date'] = Carbon::parse($schoolClass->end_date)->format('d/m/Y');

        return view('pages.school-classes.partials.update-modal', compact('schoolClass'))->render();
    }
}
