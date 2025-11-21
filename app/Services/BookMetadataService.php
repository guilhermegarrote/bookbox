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
     * Retrieve merged book metadata using Google Books and OpenLibrary APIs.
     *
     * The method attempts to fetch information from both sources. If at least one
     * source returns data, a normalized merged array is returned—prioritizing Google
     * Books data when available.
     *
     * @param string $isbn The ISBN to search for (ISBN-10 or ISBN-13).
     *
     * @return array<string, mixed>|null Normalized metadata array or null if no data was found.
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

    /**
     * Fetch book metadata from the Google Books API.
     *
     * Attempts to retrieve volume information using the Google Books public API.
     * Returns a normalized array on success or null if no matching book is found.
     *
     * @param string $isbn The ISBN to query.
     *
     * @return array<string, mixed>|null Parsed Google Books metadata or null on failure.
     */
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
                    if ($identifier['type'] === 'ISBN_10') {
                        $isbn10 = $identifier['identifier'];
                    }

                    if ($identifier['type'] === 'ISBN_13') {
                        $isbn13 = $identifier['identifier'];
                    }
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

    /**
     * Fetch book metadata from the OpenLibrary API.
     *
     * Queries OpenLibrary using its ISBN lookup endpoint. Returns a formatted metadata
     * array or null if the book is not found or parsing fails.
     *
     * @param string $isbn The ISBN to query.
     *
     * @return array<string, mixed>|null Parsed OpenLibrary metadata or null on failure.
     */
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
     * Convert an array of author names into a comma-separated string.
     *
     * @param array<int, string> $authors List of author names.
     *
     * @return string|null A formatted string or null if no authors are provided.
     */
    private function formatAuthors(array $authors): ?string
    {
        return !empty($authors) ? implode(', ', $authors) : null;
    }

    /**
     * Return the first category value, typically used as the book's primary genre.
     *
     * @param array<int, string> $categories List of categories.
     *
     * @return string|null First category or null if empty.
     */
    private function firstCategory(array $categories): ?string
    {
        return !empty($categories) ? $categories[0] : null;
    }

    /**
     * Normalize the provided ISBN to a valid ISBN-13 when possible.
     *
     * If the input is an ISBN-10, it is converted to ISBN-13 with a recalculated
     * check digit. If already 13 digits or unconvertible, it is returned as-is.
     *
     * @param string $isbn Raw ISBN input (possibly containing separators or letters).
     *
     * @return string Normalized ISBN-13 or the cleaned input ISBN.
     */
    private function normalizeIsbn(string $isbn): string
    {
        $isbn = preg_replace('/[^0-9Xx]/', '', trim($isbn));

        if (\strlen($isbn) === 10) {
            $isbn13 = '978' . substr($isbn, 0, 9);
            $sum = 0;

            for ($i = 0; $i < 12; ++$i) {
                $digit = (int) $isbn13[$i];
                $sum += ($i % 2 === 0) ? $digit : $digit * 3;
            }

            $checkDigit = (10 - ($sum % 10)) % 10;
            $isbn13 .= $checkDigit;

            return $isbn13;
        }

        return $isbn;
    }
}
