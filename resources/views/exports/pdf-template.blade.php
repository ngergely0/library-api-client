<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 2cm 2cm 3cm 2cm;
        }
        
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10pt;
            color: #333;
        }
        
        .header {
            position: fixed;
            top: -2cm;
            left: 0;
            right: 0;
            height: 2cm;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 15px 20px;
            color: white;
        }
        
        .header-content {
            display: table;
            width: 100%;
        }
        
        .logo {
            display: table-cell;
            vertical-align: middle;
            width: 60px;
        }
        
        .logo img {
            max-height: 50px;
            max-width: 60px;
        }
        
        .header-text {
            display: table-cell;
            vertical-align: middle;
            padding-left: 15px;
        }
        
        .header-text h1 {
            margin: 0;
            font-size: 18pt;
            font-weight: bold;
        }
        
        .header-text p {
            margin: 5px 0 0 0;
            font-size: 9pt;
            opacity: 0.9;
        }
        
        .footer {
            position: fixed;
            bottom: -2.5cm;
            left: 0;
            right: 0;
            height: 2cm;
            border-top: 2px solid #667eea;
            padding: 10px 20px;
            font-size: 8pt;
            color: #666;
        }
        
        .footer-content {
            display: table;
            width: 100%;
        }
        
        .footer-left {
            display: table-cell;
            text-align: left;
        }
        
        .footer-right {
            display: table-cell;
            text-align: right;
        }
        
        .content {
            margin-top: 1cm;
        }
        
        .info-section {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        
        .info-section p {
            margin: 5px 0;
            font-size: 9pt;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        thead {
            background-color: #667eea;
            color: white;
        }
        
        thead th {
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10pt;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tbody tr:hover {
            background-color: #e9ecef;
        }
        
        tbody td {
            padding: 10px 8px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .total-count {
            margin-top: 15px;
            font-weight: bold;
            text-align: right;
            color: #667eea;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            @if($logoPath && file_exists($logoPath))
            <div class="logo">
                <img src="{{ $logoPath }}" alt="Logo">
            </div>
            @endif
            <div class="header-text">
                <h1>Könyvtár Rendszer</h1>
                <p>{{ $title }}</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-content">
            <div class="footer-left">
                <strong>Könyvtár Rendszer</strong><br>
                Exportálva: {{ $exportDate }}
            </div>
            <div class="footer-right">
                Oldal: <span class="pagenum"></span>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="info-section">
            <p><strong>Dokumentum:</strong> {{ $title }}</p>
            <p><strong>Létrehozva:</strong> {{ $exportDate }}</p>
            <p><strong>Elemek száma:</strong> {{ count($data) }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    @foreach($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) }}" style="text-align: center; padding: 20px; font-style: italic;">
                            Nincs megjeleníthető adat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if(count($data) > 0)
            <div class="total-count">
                Összesen: {{ count($data) }} elem
            </div>
        @endif
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Oldal {PAGE_NUM} / {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("DejaVu Sans");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) - 20;
            $y = $pdf->get_height() - 40;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>
