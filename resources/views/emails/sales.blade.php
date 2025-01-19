<!DOCTYPE html>
<html>
<head>
    <title>Relatório diário de vendas</title>
</head>
    <body>
        <h1>Detalhes da Venda</h1>
        <table style="border-collapse: collapse; width: 100%; border: 1px solid #ddd;">
        <thead>
            <tr>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">ID</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Seller Name</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Seller Email</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Commission Value</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Sale Value</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Sale Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $sale['id'] }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $sale['seller_name'] }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $sale['seller_email'] }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ number_format($sale['commission_value'], 2) }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ number_format($sale['sale_value'], 2) }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $sale['sale_date'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
