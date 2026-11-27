<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('ui.print_all_songs') }} - S0ng</title>
    <style>
        @page { size: A4; margin: 10mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #000; background: #e8e8e8; font-family: sans-serif; }
        .print-controls { max-width: 210mm; margin: 1rem auto; padding: 0 1rem; }
        .print-controls button, .print-controls a { font: inherit; margin-right: 1rem; }
        .print-song { width: 190mm; margin: 0 auto 10mm; background: #fff; }
        .print-song h1 { margin: 0 0 2mm; font-size: 18px; line-height: 1.15; overflow-wrap: anywhere; }
        .print-song h2 { margin: 0 0 3mm; font-size: 14px; line-height: 1.15; overflow-wrap: anywhere; }
        .print-lines { font: 15px/1.15 monospace; white-space: pre-wrap; overflow-wrap: anywhere; }
        .print-line { display: block; }
        .print-chord { font-weight: bold; }
        @media screen {
            .print-song { padding: 10mm; width: 210mm; }
        }
        @media print {
            body { background: #fff; }
            .print-controls { display: none; }
            .print-song { margin: 0; break-before: page; page-break-before: always; }
            .print-song:first-child { break-before: auto; page-break-before: auto; }
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <h1>{{ __('ui.print_all_songs') }}</h1>
        <p>{{ __('ui.print_songs_help') }}</p>
        <button type="button" id="print-songs">{{ __('ui.print') }}</button>
        <a href="{{ route('song.index') }}">{{ __('ui.back_to_songs') }}</a>
    </div>
    <main>
        @foreach ($songs as $song)
            <article class="print-song">
                <h1>{{ $song->name }}</h1>
                <h2>{{ $song->artist->name ?? '' }}</h2>
                <div class="print-lines">@foreach ($song->lines() as $line)<span @class(['print-line', 'print-chord' => $line->isChord])>{{ $line->text }}&#8203;</span>@endforeach</div>
            </article>
        @endforeach
    </main>
    <script>
        function fitSongs() {
            const pageHeight = 277 * 96 / 25.4 - 2;
            document.querySelectorAll('.print-song').forEach(function (song) {
                const lines = song.querySelector('.print-lines');
                const style = window.getComputedStyle(song);
                const padding = parseFloat(style.paddingTop) + parseFloat(style.paddingBottom);
                lines.style.whiteSpace = 'pre';
                let min = 9;
                let max = 15;
                for (let i = 0; i < 7; i += 1) {
                    const size = (min + max) / 2;
                    lines.style.fontSize = size + 'px';
                    if (song.scrollHeight - padding <= pageHeight && lines.scrollWidth <= lines.clientWidth) {
                        min = size;
                    } else {
                        max = size;
                    }
                }
                lines.style.fontSize = min + 'px';
                if (lines.scrollWidth > lines.clientWidth) {
                    lines.style.whiteSpace = 'pre-wrap';
                }
            });
        }
        fitSongs();
        window.addEventListener('beforeprint', fitSongs);
        document.getElementById('print-songs').addEventListener('click', function () {
            fitSongs();
            window.print();
        });
    </script>
</body>
</html>
