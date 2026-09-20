<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>PO {{ $poStock->unique_id }}</title>
    <style>
        @page {
            margin: 12mm 10mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
        }
        .header-container {
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #000;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
            border: none;
            padding: 0;
        }
        .logo-cell {
            width: 18%;
            text-align: left;
        }
        .office-cell {
            width: 40%;
            padding-left: 5px;
            font-size: 7.5pt;
            line-height: 1.35;
        }
        .other-offices-cell {
            width: 42%;
            padding-left: 10px;
            font-size: 7.5pt;
            line-height: 1.35;
        }
        .logo {
            max-width: 100px;
            height: auto;
        }
        .company-name {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .office-label {
            font-weight: bold;
        }
        .title {
            clear: both;
            text-align: center;
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
            padding: 7px 0;
            margin: 8px 0 10px 0;
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 4px;
        }
        .po-info {
            margin-bottom: 10px;
            font-size: 9pt;
            line-height: 1.6;
        }
        .po-info-line {
            margin-bottom: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #92D050;
            border: 1px solid #000;
            padding: 5px 3px;
            font-size: 8pt;
            text-align: center;
            font-weight: bold;
        }
        td {
            border: 1px solid #000;
            padding: 4px 3px;
            font-size: 8pt;
            vertical-align: middle;
        }
        .col-no {
            width: 4%;
            text-align: center;
            background-color: #FFF2CC;
        }
        .col-item {
            width: 36%;
        }
        .col-dimension {
            width: 15%;
            text-align: center;
        }
        .col-unit {
            width: 8%;
            text-align: center;
        }
        .col-price, .col-total {
            text-align: right;
            padding-right: 5px;
        }
        .col-qty {
            width: 8%;
            text-align: center;
        }
        .total-row td {
            background-color: #FFF2CC;
            font-weight: bold;
            border-top: 2px solid #000;
        }
        .note {
            margin-top: 10px;
            font-size: 8pt;
        }
        .footer {
            margin-top: 30px;
            font-size: 9pt;
        }
        .signature-space {
            margin-top: 60px;
            margin-bottom: 5px;
        }
        .signature-name {
            text-decoration: underline;
            min-width: 200px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
<body>
    <!-- Company Header - Match Excel Template Exactly -->
    <div class="header-container">
        <table class="header-table">
            <tr>
                <!-- Logo Column -->
                <td class="logo-cell">
                    @if(file_exists(public_path('assets/images/logo-arlion2.png')))
                        <img src="{{ public_path('assets/images/logo-arlion2.png') }}" alt="Logo" class="logo">
                    @elseif(file_exists(public_path('images/logo-arlion2.png')))
                        <img src="{{ public_path('images/logo-arlion2.png') }}" alt="Logo" class="logo">
                    @endif
                </td>
                
                <!-- Office Information Column (Middle) -->
                <td class="office-cell">
                    <div class="office-label">Office</div>
                    <div>Kawasan Grage City, Biz Center Oasis AVII/9</div>
                    <div>Jl Grage Utama, Cirebon, West Java - Indonesia</div>
                    <div>Tel. +62 231 8802888, 8802788</div>
                    <div>Email: sales@arlion.co.id</div>
                    <div>https://arlion.co.id</div>
                    <div class="office-label" style="margin-top:3px;">Factory:</div>
                    <div>Lengkong Wetan, Rajagaluh, Majalengka</div>
                </td>
                
                <!-- Other Offices Column (Right) -->
                <td class="other-offices-cell">
                    <div><span class="office-label">Jakarta Office:</span></div>
                    <div>Jl Raya Alternatif Cibubur-Cileungsi KM 4,</div>
                    <div>Grand Cibubur No C2, Cibubur</div>
                    <div><span class="office-label">Yogyakarta Office:</span></div>
                    <div>Jl Raga Sleman-Turi Km 4, Sleman, Yogyakarta</div>
                    <div><span class="office-label">Bali Office</span></div>
                    <div>Benoa Square 3rd Floor, Jl. Bypass Ngurah Rai</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Title -->
    <div class="title">PURCHASE ORDER</div>

    <!-- PO Info -->
    <div class="po-info">
        <div class="po-info-line">Po Number : <strong>{{ $poStock->unique_id }}</strong></div>
        <div class="po-info-line">To : <strong>{{ $poStock->supplier->supplier_name ?? '-' }}</strong></div>
        <div class="po-info-line">Attn : {{ $poStock->supplier->contact_name ?? $poStock->supplier->contact_person ?? $poStock->supplier->pic ?? '' }}</div>
    </div>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-item">Item<sup>**)</sup></th>
                <th class="col-dimension">Dimention or<br>Diameter<br>(L × W × T)</th>
                <th class="col-unit">Unit</th>
                <th class="col-price">Price (IDR)<br>(per pc)</th>
                <th class="col-qty">Qty<br>(pcs)</th>
                <th class="col-total">Total Price<br>(IDR)</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($poStock->poStockDetail as $index => $detail)
            @php
                $product = $detail->product;
                $productName = $product->product_name ?? $product->name ?? $product->skuFormat() ?? '';
                $extraDesc = $detail->description ?? ($product->description ?? '');
                $itemDescription = trim($productName . ($extraDesc ? ' | ' . $extraDesc : ''));
                
                $length = $product->length ?? $product->length_cm ?? $product->l ?? $product->dim_l ?? null;
                $width = $product->width ?? $product->width_cm ?? $product->w ?? $product->dim_w ?? null;
                $thickness = $product->height ?? $product->height_cm ?? $product->thickness ?? $product->t ?? $product->dim_t ?? null;
                
                // Get unit name from relationship (Kg, Cm, Pcs, etc)
                $unitName = $detail->unit->unit_name ?? ($detail->dimension_unit ?? ($product->dimension_unit ?? 'Cm'));
                
                $itemTotal = ($detail->price ?? 0) * ($detail->qty ?? 0);
                $grandTotal += $itemTotal;
            @endphp
            <tr>
                <td class="col-no">{{ $index + 1 }}</td>
                <td class="col-item">{{ $product->skuFormat() ?? ($itemDescription ?: '-') }}</td>
                <td class="col-dimension">
                    @if($length || $width || $thickness)
                        @if($length){{ $length }}@endif
                        @if($length && ($width || $thickness)) x @endif
                        @if($width){{ $width }}@endif
                        @if($width && $thickness) x @endif
                        @if($thickness){{ $thickness }}@endif
                    @endif
                </td>
                <td class="col-unit">{{ $unitName }}</td>
                <td class="col-price">{{ number_format($detail->price ?? 0, 0, ',', '.') }}</td>
                <td class="col-qty">{{ number_format($detail->qty ?? 0, 0, ',', '.') }}</td>
                <td class="col-total">{{ number_format($itemTotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6" style="text-align:center;">Grand Total</td>
                <td class="col-total">{{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Note -->
    @if($poStock->note)
    <div class="note">
        <div><strong>NOTE :</strong></div>
        <div style="margin-left:10px;">{{ $poStock->note }}</div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div>Yogyakarta, {{ $formattedDate }}</div>
        <div>Kind Regards,</div>
        <div class="signature-space"></div>
        <div class="signature-name">Visi Arlion Internasional</div>
    </div>
</body>
</html>
