<!DOCTYPE html>
<html>

<head>
    <title>Invoice</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }
    </style>
</head>

<body>
    <h1>Invoice #{{ $invoice->invoice_number ?? $invoice->id }}</h1>
    <p>Total: ${{ number_format($total, 2) }}</p>
</html>
