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

        $finalIsbn = $this->normalizeIsbn($isbn);

        return [
            'isbn' => $finalIsbn,
            'title' => $google['title'] ?? $open['title'] ?? null,
            'author' => $this->formatAuthors($google['authors'] ?? $open['authors'] ?? []),
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

            $isbn10 = null;
            $isbn13 = null;
            if (!empty($book['industryIdentifiers'])) {
                foreach ($book['industryIdentifiers'] as $identifier) {
                    if ($identifier['type'] === 'ISBN_10') $isbn10 = $identifier['identifier'];
                    if ($identifier['type'] === 'ISBN_13') $isbn13 = $identifier['identifier'];
                }
            }

            return [
                'title' => $book['title'] ?? null,
                'authors' => $book['authors'] ?? [],
                'publisher' => $book['publisher'] ?? null,
                'categories' => $book['categories'] ?? [],
                'isbn10' => $isbn10,
                'isbn13' => $isbn13,
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

    /**
     * Normalize ISBN to 13 digits if possible, otherwise keep 10.
     */
    private function normalizeIsbn(string $isbn): string
    {
        $isbn = preg_replace('/[^0-9Xx]/', '', trim($isbn));

        if (strlen($isbn) === 10) {
            $isbn13 = '978' . substr($isbn, 0, 9);
            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $digit = (int)$isbn13[$i];
                $sum += ($i % 2 === 0) ? $digit : $digit * 3;
            }
            $checkDigit = (10 - ($sum % 10)) % 10;
            $isbn13 .= $checkDigit;
            return $isbn13;
        }

        return $isbn;
    }
}
