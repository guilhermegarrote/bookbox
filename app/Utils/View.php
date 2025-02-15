<?php

namespace App\Utils;

class View {
    /**
     * Método responsável por retornar o conteúdo de uma view
     * @param sring $view
     * @return string
     */
    private static function getConteudoView($view) {
        $file = __DIR__ . '/../../resources/view/' . $view . '.php';
        return file_exists($file) ? file_get_contents($file) : '';
    }

    /**
     * Método responsável por retornar o conteúdo renderizado de uma view
     * @param string $view
     * @return string
     */
    public static function renderizar($view) {
        $conteudoView = self::getConteudoView($view);
        return $conteudoView;
    }
}