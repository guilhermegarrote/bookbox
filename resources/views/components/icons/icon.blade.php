@php
    $path = resource_path("svg/{$name}.svg");

    if (file_exists($path)) {
        $svg = file_get_contents($path);
        if (!empty($attributes['class'])) {
            $svg = str_replace('<svg', '<svg class="' . e($attributes['class']) . '"', $svg);
        }
    }
@endphp

{!! $svg ?? "<!-- Ícone \"{$name}\" não encontrado -->" !!}
