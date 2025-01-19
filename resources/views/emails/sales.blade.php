<!DOCTYPE html>
<html>
<head>
    <title>Registro de Venda</title>
</head>
<body>
    <h1>Detalhes da Venda</h1>
    <table border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Valor da Venda</th>
                <th>Comissão</th>
                <th>Data da Venda</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $sale['id'] }}</td>
                <td>{{ $sale['name'] }}</td>
                <td>{{ $sale['email'] }}</td>
                <td>{{ number_format($sale['sale_value'], 2, ',', '.') }}</td>
                <td>{{ number_format($sale['sale_comission'], 2, ',', '.') }}</td>
                <td>{{ $sale['sale_date'] }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
