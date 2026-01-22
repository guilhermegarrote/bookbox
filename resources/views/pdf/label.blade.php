<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Etiquetas | Bookbox</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Urbanist:wght@400;600;700&display=swap');

        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Urbanist", sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 0;
        }

        .sheet {
            background: #bebebe;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-auto-rows: 37.1mm;
        }

        .label-card {
            display: flex;
            background-color: #fff;
            width: 105mm;
            height: 37.1mm;
            overflow: hidden;
            border: 1mm solid #000;
            position: relative;
            page-break-inside: avoid;
        }

        .content {
            display: flex;
            padding: 2.28mm 4.55mm;
            flex: 1;
            position: relative;
            align-items: center;
            gap: 9.1mm;
        }

        .label-title {
            font-weight: 400;
            font-size: 2.4mm;
            color: #888;
            margin-bottom: 0.3mm;
        }

        .left-column,
        .right-column {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 2.75mm;
            color: #333;
            flex-shrink: 0;
            max-width: 45%;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .label-value {
            font-weight: 600;
            font-size: 3.25mm;
            color: #000;
            line-height: 1.1;
            word-break: break-word;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .logo {
            position: absolute;
            right: 4.8mm;
            bottom: 1.9mm;
            font-size: 6mm;
            font-weight: 700;
            opacity: 0.9;
            letter-spacing: -0.095mm;
        }
    </style>
</head>

<body>
    <div class="sheet">
        @foreach ($labels as $label)
            <div class="label-card">
                <div
                    style="background-color: {{ $label['genre_color_hex'] ? '#' . $label['genre_color_hex'] : '#f39c00' }}; width:8.5mm; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                    &nbsp;
                </div>

                <div class="content">

                    <div class="left-column">
                        <div>
                            <div class="label-title">ISBN</div>
                            <div class="label-value">{{ $label['isbn'] }}</div>
                        </div>

                        <div>
                            <div class="label-title">Autor</div>
                            <div class="label-value">{{ $label['author'] }}</div>
                        </div>

                        <div>
                            <div class="label-title">Categoria</div>
                            <div class="label-value">{{ $label['genre_name'] }}</div>
                        </div>
                    </div>

                    <div class="right-column">
                        <div>
                            <div class="label-title">Título</div>
                            <div class="label-value">{{ $label['title'] }}</div>
                        </div>

                        <div>
                            <div class="label-title">Editora</div>
                            <div class="label-value">{{ $label['publisher'] }}</div>
                        </div>

                        <div>
                            <div class="label-title">Exemplar</div>
                            <div class="label-value">{{ $label['number'] }}</div>
                        </div>
                    </div>

                    <div class="logo"
                        style="color: {{ $label['genre_color_hex'] ? '#' . $label['genre_color_hex'] : '#f39c00' }};">
                        bookbox</div>
                </div>
            </div>
        @endforeach
    </div>
</body>

</html>
