<!DOCTYPE html>
<html lang="bg">

<head>
    <meta charset="UTF-8">
    <title>Поръчка №{{ $order->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #222;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            background: #000;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px
        }

        .table tr td {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            font-size: 14px
        }

        .total {
            font-weight: bold;
            font-size: 16px
        }
    </style>
</head>

<body>
    <div class="container">

        <h2>Благодарим за поръчката!</h2>

        <p>Здравей, {{ $order->first_name }},</p>
        <p>Вашата поръчка № <strong>{{ $order->id }}</strong> е приета успешно.</p>

        <h3>Детайли за поръчката:</h3>

        <table class="table">
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->name }} × {{ $item->quantity }}</td>
                    <td style="text-align:right">{{ number_format($item->price * $item->quantity, 2) }} лв</td>
                </tr>
            @endforeach
            <tr class="total">
                <td>Общо:</td>
                <td style="text-align:right">{{ number_format($order->total, 2) }} лв</td>
            </tr>
        </table>


        <p style="margin-top:30px;font-size:12px;color:#777">
            Ако имате въпроси, просто отговорете на този имейл — ние сме насреща.
        </p>

    </div>
</body>

</html>
