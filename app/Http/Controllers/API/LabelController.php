<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Label\LabelGenerateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

/**
 * Controller responsible for generating PDF labels for books and copies.
 */
class LabelController extends Controller
{
    /**
     * Generate PDF labels for the selected books and copies.
     *
     * @param LabelGenerateRequest $request the validated request containing books and copies information
     *
     * @return JsonResponse JSON response with the PDF URL or an error message
     *
     * @see Browsershot Used to render HTML into PDF via Chromium.
     */
    public function generateLabel(LabelGenerateRequest $request): JsonResponse
    {
        $books = $request->input('books', []);
        $isbns = collect($books)->pluck('isbn')->unique();

        $copies = Cache::remember(
            'copies_for_labels_' . md5($isbns->join(',')),
            300,
            function () use ($isbns) {
                return DB::table('copies')
                    ->select('id', 'number', 'isbn', 'title', 'author', 'genre_name', 'genre_color_hex', 'publisher')
                    ->whereIn('isbn', $isbns)
                    ->get()
                ;
            },
        );

        $labels = [];

        foreach ($books as $book) {
            $copyNumbers = $this->parseCopyNumbers((string) ($book['copies'] ?? ''));

            foreach ($copyNumbers as $copyNumber) {
                $label = $copies->firstWhere(fn ($c) => $c->isbn === $book['isbn'] && $c->number === $copyNumber);

                if ($label) {
                    $labels[] = (array) $label;
                }
            }
        }

        if (empty($labels)) {
            return response()->json(['error' => 'Nenhuma etiqueta encontrada para os dados informados.'], 404);
        }

        $html = view('pdf.label', ['etiquetas' => $labels])->render();
        $html = e($html);

        $pdfFilename = 'labels/labels_' . time() . '.pdf';
        $storagePath = storage_path('app/private/' . $pdfFilename);
        $chromiumPath = env('BROWSERSHOT_CHROME_PATH', null);

        if (!$chromiumPath || !file_exists($chromiumPath)) {
            return response()->json([
                'error' => 'Chromium não encontrado. Verifique a instalação e o arquivo .env',
            ], 500);
        }

        try {
            dispatch(function () use ($html, $chromiumPath, $storagePath) {
                Browsershot::html($html)
                    ->setChromePath($chromiumPath)
                    ->noSandbox()
                    ->format('A4')
                    ->pdfOptions(['printBackground' => true, 'preferCSSPageSize' => true, 'scale' => 0.95])
                    ->save($storagePath)
                ;
            });
        } catch (\Throwable $e) {
            Log::error('PDF generation failed.', ['exception' => $e->getMessage()]);

            return response()->json([
                'error' => 'Falha ao gerar etiquetas em PDF.',
                'details' => $e->getMessage(),
            ], 500);
        }

        $publicUrl = Storage::temporaryUrl($pdfFilename, now()->addMinutes(30));

        return response()->json(['url' => $publicUrl], 200);
    }

    /**
     * Parse a string of copy numbers (e.g. "1-3,5") into an array of integers.
     *
     * @param string $input the input string representing copy numbers or ranges
     *
     * @return int[] list of parsed copy numbers
     */
    private function parseCopyNumbers(string $input): array
    {
        $numbers = [];

        foreach (explode(',', $input) as $segment) {
            $segment = trim($segment);

            if (str_contains($segment, '-')) {
                [$start, $end] = array_map('intval', explode('-', $segment));

                if ($start <= $end) {
                    $numbers = array_merge($numbers, range($start, $end));
                }
            } else {
                $numbers[] = \intval($segment);
            }
        }

        return array_unique($numbers);
    }
}
