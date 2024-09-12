<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->invoice_id }}</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <style>
        body {
            background-color: #f5f5f5;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
        }

        .invoice-container {
            max-width: 750px;
            margin: 40px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            page-break-after: avoid;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eeeeee;
        }

        .invoice-header img {
            max-height: 150px;
        }

        .organization-details {
            text-align: right;
            font-size: 0.9rem;
            color: #000000;
        }

        .invoice-title {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
            font-size: 1.5rem;
            font-weight: bold;
            color: #000000;
        }

        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .invoice-details-left {
            flex: 1;
            text-align: left;
        }

        .invoice-details-right {
            flex: 1;
            text-align: right;
        }

        .customer-details {
            color: #2c3e50;
            font-weight: bold;
        }

        .invoice-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .invoice-table th,
        .invoice-table td {
            padding: 10px;
            border-bottom: 1px solid #dddddd;
            text-align: left;
        }

        .invoice-table th {
            background-color: #2c3e50;
            color: #ffffff;
        }

        .invoice-table td {
            background-color: #f9f9f9;
        }

        .invoice-summary {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 20px;
        }

        .status-section {
            flex: 1;
            padding-right: 20px;
        }

        .total-section {
            flex: 1;
            text-align: right;
        }

        .total {
            font-size: 1.25rem;
            font-weight: bold;
        }

        .status-chip {
            padding: 8px 16px;
            border-radius: 16px;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .status-chip.green {
            background-color: #4caf50;
        }

        .status-chip.red {
            background-color: #f44336;
        }

        .status-chip.orange {
            background-color: #ff9800;
        }

        .status-chip.purple {
            background-color: #9900ff;
        }

        .status-chip.blue {
            background-color: #008cff;
        }

        .signature-section {
            text-align: right;
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-section img {
            max-width: 120px;
            margin-bottom: 5px;
        }

        .btn-print {
            background-color: #00796b;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            page-break-inside: avoid;
        }

        .btn-print:hover {
            background-color: #004d40;
        }

        .invoice-footer {
            display: flex;
            justify-content: center;
            gap: 10px;
            /* Adds spacing between buttons */
            margin-top: 20px;
        }

        .invoice-footer button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #00796b;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            min-width: 150px;
            /* Ensures both buttons have the same width */
        }

        .invoice-footer button:hover {
            background-color: #004d40;
        }

        .invoice-footer button i {
            margin-right: 5px;
            /* Adds some space between the icon and text */
        }


        /* Print styles */
        @media print {
            body {
                background-color: #ffffff;
            }

            .invoice-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
                border-radius: 0;
            }

            .btn-print {
                display: none;
            }

            .invoice-footer {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="invoice-container" id="invoice">
        <div class="invoice-header">
            <div>
                <img src="{{ asset('storage/' . auth()->user()->organization->logo) }}" alt="Logo">
            </div>
            <div class="organization-details">
                <h5><i class="material-icons right">business</i>{{ auth()->user()->organization->name }}</h5>
                <p><i class="material-icons right">location_city</i>{{ auth()->user()->organization->address }}</p>
                <p><i class="material-icons right">phone</i>Phone:
                    +91-{{ auth()->user()->organization->contact_number }}
                </p>
                <p><i class="material-icons right">email</i>Email: {{ auth()->user()->organization->email }}</p>
            </div>
        </div>

        <div class="invoice-title">Invoice</div>

        <div class="invoice-details">
            <div class="invoice-details-left">
                <p><i class="material-icons left">receipt</i>Invoice ID: <b>{{ $order->invoice_id }}</b></p>
                <p><i class="material-icons left">calendar_today</i>Date:
                    {{ \Carbon\Carbon::parse(now())->format('d M, Y') }}</p>
            </div>
            <div class="invoice-details-right">
                <p><i class="material-icons right">person</i><b>{{ $order->customer }}</b></p>
                <p><i class="material-icons right">phone</i>Phone: +91-{{ $order->phone }}</p>
                <p><i class="material-icons right">location_on</i>{{ $order->contact }}</p>
            </div>

        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="center-align">Quantity</th>
                    <th class="center-align">Price</th>
                    <th class="right-align">Total Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderdish as $dish)
                    <?php
                    // Calculate price without GST
                    $priceWithoutGST = $dish->price / 1.18;
                    ?>
                    <tr>
                        <td>{{ $dish->dish->name }}</td>
                        <td class="center-align">{{ $dish->quantity }} Nos.</td>
                        <td class="center-align">₹.{{ number_format($priceWithoutGST / $dish->quantity, 2) }}</td>
                        <td class="right-align">₹.{{ number_format($priceWithoutGST, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="invoice-summary">
            <div class="status-section">
                <p><strong>GSTIN:</strong> <b>{{ auth()->user()->organization->gst }}</b></p>
                <p><strong>PAN:</strong> <b>{{ auth()->user()->organization->pan }}</b></p>
                <p style="margin-top: 60px">Status:
                    <span
                        class="status-chip {{ $order->status === 'delivered' ? 'green' : ($order->status === 'cancelled' ? 'red' : ($order->status === 'in_progress' ? 'blue' : 'purple')) }}">
                        {{ ucwords(str_replace('_', ' ', $order->status)) }}
                    </span>
                </p>

            </div>
            <div class="total-section">
                <?php
                // Calculate total and GST portions
                $totalWithGST = $order->orderdish->sum('price');
                $subTotal = $totalWithGST / 1.18;
                $gst = $totalWithGST - $subTotal;
                $cgst = $gst / 2;
                $ugst = $gst / 2;
                ?>
                <p><strong>Sub Total:</strong> ₹.{{ number_format($subTotal, 2) }}</p>
                <p><strong>CGST (9%):</strong> ₹.{{ number_format($cgst, 2) }}</p>
                <p><strong>UGST (9%):</strong> ₹.{{ number_format($ugst, 2) }}</p>
                <p class="total"><strong>Total Amount:</strong>
                    ₹.{{ number_format($totalWithGST, 2) }}</p>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <img src="{{ asset('storage/' . auth()->user()->organization->signature) }}" alt="Authorized Signature">
            <p>Authorized Signature</p>
        </div>

        <div class="invoice-footer">
            <button onclick="window.print()" class="btn-print">
                <i class="material-icons left">print</i> Print Invoice
            </button>

            <button onclick="exportPDF()" class="btn-print">
                <i class="material-icons left">picture_as_pdf</i> Export as PDF
            </button>
        </div>
    </div>

    <script>
        function exportPDF() {
            document.querySelector('.invoice-footer').style.display = 'none';

            const invoiceElement = document.getElementById('invoice');

            const options = {
                margin: 0.5,
                filename: 'invoice-{{ $order->invoice_id }}.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2
                },
                jsPDF: {
                    unit: 'in',
                    format: 'A4',
                    orientation: 'portrait'
                },
                pagebreak: {
                    mode: 'avoid-all'
                }
            };

            html2pdf()
                .from(invoice)
                .save('Invoice-' + '{{ $order->invoice_id }}' + '.pdf')
                .then(() => {
                    // Show the buttons again after the PDF is generated
                    document.querySelector('.invoice-footer').style.display = 'flex';
                });
        }
    </script>



</body>

</html>
