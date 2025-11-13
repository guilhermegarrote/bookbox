<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BookMetadataService
{
    private string $googleApi = 'https://www.googleapis.com/books/v1/volumes?q=isbn:';
    private string $openLibraryApi = 'https://openlibrary.org/api/books?bibkeys=ISBN:';

    /**
     * Fetch book metadata from Google Books and OpenLibrary.
     *
     * @return null|array<string, mixed> Merged book metadata or null if not found
     */
    public function fetch(string $isbn): ?array
    {
        $google = $this->fetchFromGoogle($isbn);
        $open = $this->fetchFromOpenLibrary($isbn);

        if (!$google && !$open) {
            return null;
        }

        return [
            'title' => $google['title'] ?? $open['title'] ?? null,
            'authors' => $this->formatAuthors($google['authors'] ?? $open['authors'] ?? []),
            'publisher' => $google['publisher'] ?? $open['publisher'] ?? null,
            'genre' => $this->firstCategory($google['categories'] ?? $open['categories'] ?? []),
        ];
    }

    private function fetchFromGoogle(string $isbn): ?array
    {
        try {
            $key = config('services.google_books.key');
            $url = "{$this->googleApi}{$isbn}&langRestrict=pt";

            if ($key) {
                $url .= "&key={$key}";
            }

            $response = Http::get($url)->json();
            $book = $response['items'][0]['volumeInfo'] ?? null;

            if (!$book) {
                return null;
            }

            return [
                'title' => $book['title'] ?? null,
                'authors' => $book['authors'] ?? [],
                'publisher' => $book['publisher'] ?? null,
                'categories' => $book['categories'] ?? [],
            ];
        } catch (\Throwable $e) {
            Log::warning('Google Books API error: ' . $e->getMessage());

            return null;
        }
    }

    private function fetchFromOpenLibrary(string $isbn): ?array
    {
        try {
            $response = Http::get("{$this->openLibraryApi}{$isbn}&jscmd=data&format=json")->json();
            $book = $response["ISBN:{$isbn}"] ?? null;

            if (!$book) {
                return null;
            }

            return [
                'title' => $book['title'] ?? null,
                'authors' => collect($book['authors'] ?? [])->pluck('name')->toArray(),
                'publisher' => collect($book['publishers'] ?? [])->pluck('name')->first(),
                'categories' => collect($book['subjects'] ?? [])->pluck('name')->toArray(),
            ];
        } catch (\Throwable $e) {
            Log::warning('OpenLibrary API error: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Join author names into a single string.
     */
    private function formatAuthors(array $authors): ?string
    {
        return !empty($authors) ? implode(', ', $authors) : null;
    }

    /**
     * Return only the first category (genre).
     */
    private function firstCategory(array $categories): ?string
    {
        return !empty($categories) ? $categories[0] : null;
    }
}
