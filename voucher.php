<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>INVOICE</h2>
        <address>
            AGAMiLabs Ltd.<br>
            CU Road #1, Hathazari<br>
            Chattogram-4330<br>
            +880 1312257899<br>
            info@agamilabs.com<br>
            agamilabs.com
        </address>
        <p>Invoice Number: WM-######<br>
            Date: <?= date('Y-m-d')?></p>
        <h3>Bill To:</h3>
        <p>[Client's Name]<br>
            [Client's Address]<br>
            [City, State, Zip Code]</p>
        <table>
            <thead>
                <tr>
                    <th>Description of Services</th>
                    <th>Hours Worked</th>
                    <th>Rate</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Technical Support</td>
                    <td>107</td>
                    <td>£2.5/hr</td>
                    <td>£267.50</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">Subtotal:</td>
                    <td>£267.50</td>
                </tr>
                <tr>
                    <td colspan="3">VAT (if applicable):</td>
                    <td>[Insert VAT Amount]</td>
                </tr>
                <tr>
                    <td colspan="3">Total:</td>
                    <td>£267.50</td>
                </tr>
            </tfoot>
        </table>
        <p>Payment Terms: [Insert Payment Terms]</p>
        <p>Please make bank transfer or checks payable to AGAMiLabs Ltd.</p>
        <p>Thank you for your business with us!</p>
    </div>
</body>

</html>