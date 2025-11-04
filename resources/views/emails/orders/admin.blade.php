<!DOCTYPE html>
<html lang="bg">

<head>
    <meta charset="UTF-8">
    <title>Нова поръчка №{{ $order->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #222;
        }

        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table td {
            padding: 6px 0;
            border-bottom: 1px solid #eee;
            font-size: 14px
        }
    </style>
</head>

<body>
    <div class="container">

        <h2>Нова поръчка №{{ $order->id }}</h2>

        <p><strong>Клиент:</strong> {{ $order->first_name }} {{ $order->last_name }}<br>
            <strong>Телефон:</strong> {{ $order->phone }}<br>
            <strong>Имейл:</strong> {{ $order->email }}
        </p>

        <h3>Продукти:</h3>
        <table class="table">
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->name }} × {{ $item->quantity }}</td>
                    <td style="text-align:right">{{ number_format($item->price * $item->quantity, 2) }} лв</td>
                </tr>
            @endforeach
            <tr>
                <td><strong>Общо:</strong></td>
                <td style="text-align:right"><strong>{{ number_format($order->total, 2) }} лв</strong></td>
            </tr>
        </table>


    </div>
</body>

</html>
