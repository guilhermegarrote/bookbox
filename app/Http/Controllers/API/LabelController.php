<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Label\LabelStoreRequest;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;
use Throwable;

class LabelController extends Controller
{
    /**
     * Generate PDF labels for the selected books and copies.
     *
     * @param LabelStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateLabels(LabelStoreRequest $request)
    {
        $books = $request->input('books');

        $isbns = collect($books)->pluck('isbn')->unique();

        $copies = DB::table('copies')
            ->select('id', 'number', 'isbn', 'title', 'author', 'genre_name', 'genre_color_hex', 'publisher')
            ->whereIn('isbn', $isbns)
            ->get();

        $labels = [];

        foreach ($books as $book) {
            $copyNumbers = $this->parseCopyNumbers($book['copies']);

            foreach ($copyNumbers as $copyNumber) {
                $label = $copies->firstWhere(fn($c) => $c->isbn === $book['isbn'] && $c->number == $copyNumber);

                if ($label) {
                    $labels[] = (array) $label;
                }
            }
        }

        if (empty($labels)) {
            return response()->json(['error' => 'Nenhuma etiqueta encontrada para os dados informados.'], 404);
        }

        $html = view('pdf.label', ['etiquetas' => $labels])->render();

        $pdfFilename = 'etiquetas/etiquetas_' . time() . '.pdf';
        $chromiumPath = env('BROWSERSHOT_CHROME_PATH', null);

        if (!$chromiumPath || !file_exists($chromiumPath)) {
            return response()->json([
                'error' => 'Chromium não encontrado. Verifique a instalação e o arquivo .env'
            ], 500);
        }

        try {
            Browsershot::html($html)
                ->setChromePath($chromiumPath)
                ->noSandbox()
                ->format('A4')
                ->save(storage_path('app/public/' . $pdfFilename));
        } catch (Throwable $e) {
            return response()->json([
                'error' => 'Failed to generate PDF labels.',
                'details' => $e->getMessage()
            ], 500);
        }

        $publicUrl = Storage::url($pdfFilename);

        return response()->json(['url' => $publicUrl], 200);
    }

    /**
     * Parse a string of copy numbers (e.g. "1-3,5") into an array of integers.
     *
     * @param string $input
     * @return int[]
     */
    private function parseCopyNumbers(string $input): array
    {
        $numbers = [];

        foreach (explode(',', $input) as $segment) {
            if (str_contains($segment, '-')) {
                [$start, $end] = array_map('intval', explode('-', $segment));
                if ($start <= $end) {
                    $numbers = array_merge($numbers, range($start, $end));
                }
            } else {
                $numbers[] = intval($segment);
            }
        }

        return array_unique($numbers);
    }
}
