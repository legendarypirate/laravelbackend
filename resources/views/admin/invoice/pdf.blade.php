<!DOCTYPE html>
<html lang="mn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Нэхэмжлэл - {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 1cm;
            size: A4 portrait;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        .invoice-container {
            max-width: 21cm;
            margin: 0 auto;
            padding: 0.8cm;
            background: white;
        }
        
        .invoice-header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .invoice-title {
            font-size: 18pt;
            font-weight: bold;
            margin: 0;
            color: #2c3e50;
        }
        
        .invoice-number-date {
            font-size: 10pt;
            color: #7f8c8d;
            margin: 5px 0;
        }
        
        .info-title {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 8px;
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }
        
        .info-container {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .company-info, .customer-info {
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 10px;
        }
        
        .customer-info {
            padding-right: 0;
            padding-left: 15px;
        }
        
        .info-label {
            font-weight: bold;
            min-width: 120px;
        }
        
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        
        .signature-image-container {
            display: table-cell;
            vertical-align: middle;
            padding-right: 20px;
            width: 120px;
        }
        
        .signature-image {
            max-width: 5cm;
            max-height: 80px;
            object-fit: contain;
        }
        
        .signature-text {
            display: table-cell;
            vertical-align: middle;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        th {
            background-color: #34495e;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 9pt;
        }
        
        td {
            padding: 6px 6px;
            border-bottom: 1px solid #ddd;
            font-size: 9pt;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .total-section {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #333;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 10pt;
        }
        
        .grand-total {
            font-size: 11pt;
            font-weight: bold;
            color: #e74c3c;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9pt;
            color: #7f8c8d;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        .break-avoid {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <h1 class="invoice-title">НЭХЭМЖЛЭЛ</h1>
            <div class="invoice-number-date">Дугаар: {{ $invoice->invoice_number ?? 'N/A' }} | Огноо: {{ $invoice->invoice_date ? (is_string($invoice->invoice_date) ? $invoice->invoice_date : $invoice->invoice_date->format('Y-m-d')) : 'N/A' }}</div>
        </div>
        
        <div class="info-container">
            <div class="company-info">
                <div class="info-title">Нэхэмжлэгч</div>
                @php
                    $issuer = $invoice->issuer_profile_snapshot ?? null;
                    $bank = $invoice->issuer_bank_snapshot ?? null;
                @endphp
                <div class="info-row">
                    <span class="info-label">Байгууллагын нэр:</span>
                    <span>{{ $issuer['name'] ?? 'Сонгоогүй' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Хаяг:</span>
                    <span>{{ $issuer['address'] ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Утас:</span>
                    <span>{{ $issuer['phone'] ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Имэйл:</span>
                    <span>{{ $issuer['email'] ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Банкны данс:</span>
                    <span>
                        {{ $bank['bank_name'] ?? '-' }} 
                        @if($bank && isset($bank['account_number']))
                            - {{ $bank['account_number'] }}
                        @endif
                        @if($bank && isset($bank['iban']))
                            ({{ $bank['iban'] }})
                        @endif
                    </span>
                </div>
            </div>
            
            <div class="customer-info">
                <div class="info-title">Төлөгч</div>
                <div class="info-row">
                    <span class="info-label">Байгууллагын нэр:</span>
                    <span>{{ $invoice->customer_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Имэйл:</span>
                    <span>{{ $invoice->customer_email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Утас:</span>
                    <span>{{ $invoice->customer_phone ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Нэхэмжлэл:</span>
                    <span>{{ $invoice->invoice_date ? (is_string($invoice->invoice_date) ? $invoice->invoice_date : $invoice->invoice_date->format('Y-m-d')) : 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Дуусах:</span>
                    <span>{{ $invoice->due_date ? (is_string($invoice->due_date) ? $invoice->due_date : $invoice->due_date->format('Y-m-d')) : 'N/A' }}</span>
                </div>
            </div>
        </div>
        
        <table class="break-avoid">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Бараа/Үйлчилгээ</th>
                    <th class="text-right">Тоо ширхэг</th>
                    <th class="text-right">Нэгж үнэ</th>
                    <th class="text-right">Нийт дүн</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $items = is_array($invoice->items) ? $invoice->items : (is_string($invoice->items) ? json_decode($invoice->items, true) : []);
                    if (!is_array($items)) {
                        $items = [];
                    }
                    $taxPercent = 10; // VAT percentage
                    $taxMultiplier = 1 + ($taxPercent / 100); // 1.1 for 10% VAT
                @endphp
                @if(count($items) > 0)
                    @foreach($items as $index => $item)
                    @php
                        $quantity = floatval($item['quantity'] ?? 0);
                        $lineTotalWithVat = floatval($item['price'] ?? 0); // Total WITH VAT for the line item
                        // Calculate unit price WITHOUT VAT
                        $unitPriceWithoutVat = $quantity > 0 ? ($lineTotalWithVat / $quantity) / $taxMultiplier : 0;
                        // Calculate line total WITHOUT VAT
                        $lineTotalWithoutVat = $lineTotalWithVat / $taxMultiplier;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['description'] ?? '' }}</td>
                        <td class="text-right">{{ number_format($quantity, 0) }}</td>
                        <td class="text-right">{{ number_format($unitPriceWithoutVat, 2) }} ₮</td>
                        <td class="text-right">{{ number_format($lineTotalWithoutVat, 2) }} ₮</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center">Бараа/Үйлчилгээ олдсонгүй</td>
                    </tr>
                @endif
            </tbody>
        </table>
        
        @php
            // Тоог монгол үсгээр хувиргах функц
            if (!function_exists('numberToMongolianWords')) {
            function numberToMongolianWords($num) {
                if ($num == 0) return 'тэг төгрөг';
                
                $ones = ['', 'нэг', 'хоёр', 'гурав', 'дөрөв', 'тав', 'зургаа', 'долоо', 'найм', 'ес'];
                $attributiveOnes = ['', 'нэг', 'хоёр', 'гурван', 'дөрвөн', 'таван', 'зургаан', 'долоон', 'найман', 'есөн'];
                $tensWords = ['', 'арав', 'хорин', 'гучин', 'дөчин', 'тавин', 'жаран', 'далан', 'наян', 'ерэн'];
                $scales = ['', 'мянга', 'сая', 'тэрбум', 'их наяд'];
                
                $integerPart = floor($num);
                $decimalPart = round(($num - $integerPart) * 100);
                
                if ($integerPart == 0) {
                    $result = 'тэг төгрөг';
                } else {
                    $parts = [];
                    $chunkIndex = 0;
                    
                    while ($integerPart > 0) {
                        $chunk = $integerPart % 1000;
                        if ($chunk > 0) {
                            $chunkWords = [];
                            
                            $hundred = floor($chunk / 100);
                            $remainder = $chunk % 100;
                            
                            if ($hundred > 0) {
                                $chunkWords[] = $attributiveOnes[$hundred] . ' зуун';
                            }
                            
                            if ($remainder > 0) {
                                // Төгрөгийн өмнөх тооллын хэлбэр ашиглах (chunkIndex == 0 үед ч)
                                if ($remainder < 10) {
                                    $chunkWords[] = $attributiveOnes[$remainder];
                                } elseif ($remainder == 10) {
                                    $chunkWords[] = $tensWords[1];
                                } elseif ($remainder < 20) {
                                    $chunkWords[] = 'арван ' . $attributiveOnes[$remainder - 10];
                                } else {
                                    $tens = floor($remainder / 10);
                                    $units = $remainder % 10;
                                    $chunkWords[] = $tensWords[$tens] . ($units > 0 ? ' ' . $attributiveOnes[$units] : '');
                                }
                            }
                            
                            $scale = $chunkIndex > 0 && isset($scales[$chunkIndex]) ? ' ' . $scales[$chunkIndex] : '';
                            $parts[] = implode(' ', $chunkWords) . $scale;
                        }
                        $integerPart = floor($integerPart / 1000);
                        $chunkIndex++;
                    }
                    
                    $result = implode(' ', array_reverse($parts)) . ' төгрөг';
                }
                
                // Мөнгө хэсгийг хасаж байна
                
                return $result;
            }
            }
            
            $totalInWords = numberToMongolianWords($invoice->total ?? 0);
        @endphp
        
        <div class="total-section">
            <div class="total-row">
                <span>Нийт дүн:</span>
                <span>{{ number_format($invoice->subtotal ?? 0, 2) }} ₮</span>
            </div>
            <div class="total-row">
                <span>НӨАТ (10%):</span>
                <span>{{ number_format($invoice->tax ?? 0, 2) }} ₮</span>
            </div>
            <div class="total-row grand-total">
                <span>Татвартай нийт дүн:</span>
                <span>{{ number_format($invoice->total ?? 0, 2) }} ₮</span>
            </div>
            <div class="total-row" style="margin-top: 10px; padding-top: 8px; border-top: 1px solid #ddd; font-size: 9pt;">
                <span><strong>Нийт дүн үсгээр:</strong></span>
                <span style="text-transform: capitalize;"><strong>{{ $totalInWords }}</strong></span>
            </div>
        </div>
        
        @if($invoice->notes)
        <div class="break-avoid" style="margin-top: 20px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
            <div class="info-title">ТЭМДЭГЛЭЛ</div>
            <p style="font-size: 9pt; margin: 5px 0;">{{ $invoice->notes }}</p>
        </div>
        @endif
        
        <div class="footer">
            <div class="signature-section">
                <div class="signature-image-container">
                    @php
                        $tamgaPath = public_path('tamga.jpeg');
                        if (file_exists($tamgaPath)) {
                            $tamgaBase64 = base64_encode(file_get_contents($tamgaPath));
                            $tamgaSrc = 'data:image/jpeg;base64,' . $tamgaBase64;
                        } else {
                            $tamgaSrc = '';
                        }
                    @endphp
                    @if($tamgaSrc)
                    <img src="{{ $tamgaSrc }}" alt="Tamga" class="signature-image">
                    @endif
                </div>
                <div class="signature-text">
                    
                    <p style="font-size: 9pt; margin: 3px 0;">Дарга.............................. /....................../</p>
                    <p style="font-size: 9pt; margin: 3px 0;">Хүлээн авсан .......................... /....................../</p>
                    <p style="font-size: 9pt; margin: 3px 0;">Нягтлан бодогч.......................... /......................./</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

