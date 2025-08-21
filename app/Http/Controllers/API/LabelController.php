<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class LabelController extends Controller
{
    public function generateLabels(Request $request)
    {
        $request->validate([
            'isbn' => 'required|string',
            'exemplares' => 'required|string',
        ]);

        // Não valida se é um ISBN válido
        // Só esta recendo um ISBN, pode ser que mais de um livro seja selecionado

        $isbn = $request->input('isbn');
        $copiesString = $request->input('exemplares');

        $copyIds = $this->parseCopyIds($copiesString);

        if (empty($copyIds)) {
            return response()->json(['error' => 'Nenhum exemplar válido informado.'], 400);
        }

        $labels = [];

        // Pesquisa esta errada, o que é passado é o número do exemplar, não id
        foreach ($copyIds as $copyId) {
            $label = DB::table('copies')
                ->select('id', 'number', 'isbn', 'title', 'author', 'genre_name', 'genre_color_hex', 'publisher')
                ->where('isbn', $isbn)
                // O where tem que ter todos os números dos exemplares que quer gerar etiqueta pois então retornara os dados de todos
                ->where('id', $copyId)
                ->first();

            if ($label) {
                $labels[] = (array) $label;
            }
        }

        if (empty($labels)) {
            return response()->json(['error' => 'Nenhuma etiqueta encontrada para os dados informados.'], 404);
        }

        $html = view('pdf.label', ['etiquetas' => $labels])->render();

        $pdfFilename = 'etiquetas/etiquetas_' . time() . '.pdf';

        $chromiumPath = env('BROWSERSHOT_CHROME_PATH', null);

        if (!$chromiumPath || !file_exists($chromiumPath)) {
            $exception = new RuntimeException(
                'Chromium não encontrado em: ' . ($chromiumPath ?? 'variável BROWSERSHOT_CHROME_PATH não definida')
            );

            $this->logError(
                'Chromium não encontrado.',
                $exception,
                ['chromium_path' => $chromiumPath ?? 'variável BROWSERSHOT_CHROME_PATH não definida']
            );

            return $this->internalErrorResponse($exception, 'Chromium não encontrado. Verifique a instalação e o arquivo .env');
        }

        Browsershot::html($html)
            ->setChromePath($chromiumPath)
            ->noSandbox()
            ->format('A4')
            ->save(storage_path('app/public/' . $pdfFilename));

        $publicUrl = Storage::url($pdfFilename);

        return response()->json([
            'url' => $publicUrl,
            'quantidade' => count($labels),
            'etiquetas' => $labels,
        ], 200);
    }

    private function parseCopyIds(string $input): array
    {
        $ids = [];

        $segments = explode(',', $input);
        foreach ($segments as $segment) {
            if (strpos($segment, '-') !== false) {
                [$start, $end] = explode('-', $segment);
                $start = intval($start);
                $end = intval($end);

                if ($start <= $end) {
                    $ids = array_merge($ids, range($start, $end));
                }
            } else {
                $ids[] = intval($segment);
            }
        }

        return array_unique($ids);
    }
}
