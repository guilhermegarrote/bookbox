<?php   

namespace app\Controllers\Pages;

use \app\Utils\View;

class PainelController {

    /**
     * Método responsável por retornar o conteúdo (view) do painel
     * @return string
     */
    public static function getPainel() {
        return View::renderizar('pages/painel');
    }
}