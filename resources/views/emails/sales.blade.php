<!DOCTYPE html>
<html>
<head>
    <title>Relatório diário de vendas</title>
</head>
<body>
    <h1>Detalhess das Vendas</h1>
    @php
        $totalSales = 0;
        foreach ($sales as $sale) {
            $totalSales += $sale['sale_value'];
        }
    @endphp
    <p style="font-weight: bold; font-size: 18px;">Total das Vendas: R$ {{ number_format($totalSales, 2, ',', '.') }}</p>
    <table style="border-collapse: collapse; width: 100%; border: 1px solid #ddd;">
        <thead>
            <tr>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">ID</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Vendedor</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Email</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Comissão</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Valor da Venda</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Data</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $sale['id'] }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $sale['seller_name'] }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $sale['seller_email'] }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">R$ {{ number_format($sale['commission_value'], 2, ',', '.') }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">R$ {{ number_format($sale['sale_value'], 2, ',', '.') }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $sale['sale_date'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
