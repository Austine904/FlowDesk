<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0; background: #f9fafb; }
        .container { max-width: 600px; margin: 24px auto; background: #fff; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb; }
        .header { background: #059669; padding: 24px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 20px; }
        .body { padding: 24px; }
        .body p { line-height: 1.6; margin: 0 0 16px; }
        .summary { background: #f9fafb; border-radius: 6px; padding: 16px; margin: 16px 0; }
        .summary table { width: 100%; border-collapse: collapse; }
        .summary td { padding: 6px 8px; font-size: 13px; }
        .summary td:last-child { text-align: right; font-weight: bold; }
        .footer { background: #f3f4f6; padding: 16px 24px; font-size: 12px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payment Confirmed</h1>
        </div>
        <div class="body">
            <p>Dear <strong><?= esc($receipt['customer_name'] ?? 'Valued Customer') ?></strong>,</p>
            <p>We confirm receipt of your payment. Thank you!</p>

            <h3 style="margin: 20px 0 8px; font-size: 15px;">Receipt Summary</h3>
            <div class="summary">
                <table>
                    <tr><td>Receipt No:</td><td><?= esc($receipt['receipt_no']) ?></td></tr>
                    <tr><td>Date:</td><td><?= esc($receipt['receipt_date']) ?></td></tr>
                    <tr><td>Invoice No:</td><td><?= esc($receipt['invoice_no'] ?? 'N/A') ?></td></tr>
                    <?php $symbol = $settings['currency_symbol'] ?? org_setting('currency_symbol', 'KSh'); ?>
                    <tr><td>Amount Paid:</td><td><?= $symbol ?> <?= number_format($receipt['amount_paid'], 2) ?></td></tr>
                    <tr><td>Payment Method:</td><td><?= esc($receipt['payment_method']) ?></td></tr>
                    <tr><td>Balance After:</td><td><?= $symbol ?> <?= number_format($receipt['balance_after'], 2) ?></td></tr>
                </table>
            </div>

            <p>Thank you for your business!</p>
        </div>
        <div class="footer">
            <?= esc($settings['org_name'] ?? org_setting('org_name', 'FlowDesk')) ?><br>
            <?= nl2br(esc($settings['org_address'] ?? org_setting('org_address', ''))) ?><br>
            <?= esc($settings['org_phone'] ?? org_setting('org_phone', '')) ?> |
            <?= esc($settings['org_email'] ?? org_setting('org_email', '')) ?>
        </div>
    </div>
</body>
</html>
