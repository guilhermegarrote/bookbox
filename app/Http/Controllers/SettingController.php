<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Setting;
use App\Models\View\SchoolClass;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Controller responsible for rendering settings pages and their sections.
 *
 * This controller provides partial HTML views consumed by the settings panel
 * via asynchronous (AJAX) requests. Each section may optionally support
 * server-side searching and returns either a full section view or a list-only
 * partial depending on the request type.
 */
class SettingController extends Controller
{
    /**
     * Render the main settings container page.
     *
     * This view acts as the shell for all dynamically loaded
     * settings sections (genres, classes, users, config).
     *
     * @return View
     *
     * @see resources/views/pages/settings/index.blade.php
     */
    public function index(): View
    {
        return view('pages.settings.index');
    }

    /**
     * Render the genres settings section.
     *
     * Supports optional server-side searching by genre name or color hex value.
     * When the request is made via AJAX, only the genres list partial is returned;
     * otherwise, the full section view is rendered.
     *
     * @param  Request  $request  HTTP request containing optional search parameters.
     * @return View
     *
     * @see resources/views/pages/settings/partials/genres.blade.php
     * @see resources/views/pages/settings/partials/genres-list.blade.php
     */
    public function genres(Request $request): View
    {
        $genres = Genre::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%")
                    ->orWhere('color_hex', 'like', "%{$request->search}%");
            })
            ->orderBy('name')
            ->get();

        if ($request->ajax()) {
            return view('pages.settings.partials.genres-list', compact('genres'));
        }

        return view('pages.settings.partials.genres', compact('genres'));
    }

    /**
     * Render the school classes settings section.
     *
     * Supports optional searching by course name. Returns either the
     * full section or a list-only partial when requested via AJAX.
     *
     * @param  Request  $request  HTTP request containing optional search parameters.
     * @return View
     *
     * @see resources/views/pages/settings/partials/classes.blade.php
     * @see resources/views/pages/settings/partials/classes-list.blade.php
     */
    public function classes(Request $request): View
    {
        $classes = SchoolClass::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('course', 'like', "%{$request->search}%");
            })
            ->orderBy('course')
            ->get();

        if ($request->ajax()) {
            return view('pages.settings.partials.classes-list', compact('classes'));
        }

        return view('pages.settings.partials.classes', compact('classes'));
    }

    /**
     * Render the users settings section.
     *
     * Supports optional searching by user name or hashed email value.
     * For AJAX requests, only the users list partial is returned to allow
     * dynamic list updates without reloading the entire section.
     *
     * @param  Request  $request  HTTP request containing optional search parameters.
     * @return View
     *
     * @see resources/views/pages/settings/partials/users.blade.php
     * @see resources/views/pages/settings/partials/users-list.blade.php
     */
    public function users(Request $request): View
    {
        $users = User::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere(
                        'email_hash',
                        'like',
                        '%' . hash('sha256', $request->search, true) . '%'
                    );
            })
            ->orderBy('name')
            ->get();

        if ($request->ajax()) {
            return view('pages.settings.partials.users-list', compact('users'));
        }

        return view('pages.settings.partials.users', compact('users'));
    }

    /**
     * Render the general configuration settings section.
     *
     * Loads application configuration values stored in the settings table.
     * This section does not support searching or AJAX list updates.
     *
     * @return View
     *
     * @see resources/views/pages/settings/partials/configs.blade.php
     */
    public function config(): View
    {
        $config = Setting::pluck('value', 'key')->toArray();

        return view('pages.settings.partials.configs', compact('config'));
    }
}
