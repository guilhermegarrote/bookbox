<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Label\LabelStoreRequest;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class LabelController extends Controller
{
    public function generateLabels(LabelStoreRequest $request)
    {
        $labels = [];

        foreach ($request->input('books') as $book) {
            $isbn = $book['isbn'];
            $copyNumbers = $this->parseCopyNumbers($book['copies']);

            if (empty($copyNumbers)) {
                continue;
            }

            foreach ($copyNumbers as $copyNumber) {
                $label = DB::table('copies')
                    ->select('id', 'number', 'isbn', 'title', 'author', 'genre_name', 'genre_color_hex', 'publisher')
                    ->where('isbn', $isbn)
                    ->where('number', $copyNumber)
                    ->first();

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

        Browsershot::html($html)
            ->setChromePath($chromiumPath)
            ->noSandbox()
            ->format('A4')
            ->save(storage_path('app/public/' . $pdfFilename));

        $publicUrl = Storage::url($pdfFilename);

        return response()->json([
            'url' => $publicUrl
        ], 200);
    }

    /**
     * Converte a string de exemplares (ex: "1-3,5") em array de números inteiros.
     */
    private function parseCopyNumbers(string $input): array
    {
        $numbers = [];

        $segments = explode(',', $input);
        foreach ($segments as $segment) {
            if (strpos($segment, '-') !== false) {
                [$start, $end] = explode('-', $segment);
                $start = intval($start);
                $end = intval($end);

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
