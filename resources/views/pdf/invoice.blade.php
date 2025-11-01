@php
    // ========================
    // COMPANY CONFIG VARIABLES
    // ========================
    $companyName = 'Vieva Services';
    $companyLogo = public_path('images/logoSociete.png');
@endphp

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Facture {{ $invoice->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #000;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            font-size: 8pt;
            color: #333;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 15px;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <table cellspacing="0" cellpadding="1">
        <tr>
            <td>
                <img height="45" width="140" src="{{ $companyLogo }}" alt="">
                <br />
            </td>
            <td style="font-size: 18px; width: 220px; "><br>Facture N° {{ $invoice->id }} -
                {{ \Carbon\Carbon::parse($invoice->created_at)->format('Y') }}
            </td>
            <td>
                <strong>Facture #ID:</strong>
                <br>
                {{ strtoupper(uniqid()) }}-{{ $invoice->id }}
                <br>
                <strong>Date de génération:</strong>
                <br>
                {{ now()->format('Y-m-d') }}
            </td>
        </tr>
    </table>

    <br><br>

    <!-- COMPANY & CLIENT INFO -->
    <table cellspacing="0" cellpadding="5" border="1">
        <tr>
            <td style="background-color:#bfffed;">
                Entreprise:<br /><br /><strong> {{ $companyName }}</strong><br />
            </td>
            <td style="background-color:#bfffed;">
                Client:<br /><br /><strong>{{ $invoice->client->name ?? '' }}</strong><br />
            </td>
            <td style="background-color:#bfffed;">
                ICE:<br /><br /><strong>
                    {{ $invoice->client->ice ?? '' }}</strong><br />
            </td>
        </tr>
    </table>

    <br><br>

    <!-- PRODUCTS TABLE -->
    <table border="1" cellpadding="2" cellspacing="0">
        <thead>
            <tr style="background-color:#00bf63;color:#fff;">
                <td width="20" align="center"><b></b></td>
                <td width="220" align="center"><b>Produit</b></td>
                <td width="72" align="center"><b>Quantité</b></td>
                <td width="100" align="center"> <b>Prix Unité (MAD)</b></td>
                <td width="120" align="center"><b>PrixHT (MAD)</b></td>
            </tr>
        </thead>
        <tbody>
            @php
                $grouped = $invoice->products->groupBy(fn($p) => $p->productCategory?->name ?? 'Autres');
            @endphp
            @foreach ($grouped as $catName => $items)
                <tr style="background-color:#bdbdbd;">
                    <td width="532"><b>{{ $catName }}</b></td>
                </tr>
                @foreach ($items as $key => $product)
                    <tr>
                        <td width="20" align="center">{{ $key + 1 }}</td>
                        <td width="220">{{ $product->name }}</td>
                        <td width="72" align="center">{{ $product->pivot->qty }}</td>
                        <td width="100" align="center">{{ number_format($product->pivot->price, 2, '.', ',') }}</td>
                        <td width="120" align="center">
                            {{ number_format($product->pivot->qty * $product->pivot->price, 2, '.', ',') }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <br><br>

    <table cellspacing="0" cellpadding="5" border="1">
        <tr>
            <td width="532"><b>Modalités de paiement:</b><br></td>
        </tr>
    </table>

    <br> <br>

    <!-- PAYMENT & TOTALS -->
    <table cellspacing="0" cellpadding="5" border="0">
        @php
            $totalHT = $invoice->total;
            $tva = $totalHT * 0.2;
            $totalTTC = $totalHT + $tva;
        @endphp
        <tr>
            <td width="242" style="border: 1px solid black;" rowspan="3"><b>Coordonnées
                    bancaires:</b><br><br><i><u>Banque:</u>&nbsp;&nbsp;&nbsp;BMCE BANK Maroc.</i></td>
            <td width="20" align="center"><b></b></td>
            <td width="140" align="center" style="border: 1px solid black;background-color:#00bf63;color: white;">
                <b>Montant total HT (MAD)</b>
            </td>
            <td width="130" align="center" style="border: 1px solid black;">
                <b><i>{{ number_format($totalHT, 2, '.', ',') }}</i></b>
            </td>
        </tr>
        <tr>
            <td width="20" align="center"><b></b></td>
            <td width="140" align="center" style="border: 1px solid black;background-color:#00bf63;color: white;">
                <b>TVA</b>
            </td>
            <td width="130" align="center" style="border: 1px solid black;">
                <b>{{ number_format($tva, 2, '.', ',') }}</b>
            </td>
        </tr>
        <tr>
            <td width="20" align="center"><b></b></td>
            <td width="140" align="center" style="border: 1px solid black;background-color:#00bf63;color: white;">
                <b>Montant total TTC (MAD)</b>
            </td>
            <td width="130" align="center" style="border: 1px solid black;">
                <b><i>{{ number_format($totalTTC, 2, '.', ',') }}</i></b>
            </td>
        </tr>
    </table>


    <br><br><br>

    <!-- CACHET -->
    <table cellspacing="0" cellpadding="5">
        <tr>
            <td width="242" align="center" style="border: 1px solid black;"><b>Accord client <br> Proposition
                    expirant le :</b><i>&nbsp;{{ \Carbon\Carbon::now()->addDays(29)->toDateString() }}</i><br>Mention
                "Bon pour accord", date, signature</td>
            <td width="20" align="center"><b></b></td>
            <td width="270" style="border: 1px solid black;"><u><i>Remarques et
                        Cachet:<br /></i></u><br><br><br><br><br>*Cadre réservé à ne pas remplir avant la récéption.
            </td>
        </tr>

    </table>
    <br>
</body>

</html>
