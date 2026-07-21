<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0; background: #f9fafb; }
        .container { max-width: 600px; margin: 24px auto; background: #fff; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb; }
        .header { background: #4f46e5; padding: 24px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 20px; }
        .body { padding: 24px; }
        .body p { line-height: 1.6; margin: 0 0 16px; }
        .summary { background: #f9fafb; border-radius: 6px; padding: 16px; margin: 16px 0; }
        .summary table { width: 100%; border-collapse: collapse; }
        .summary td { padding: 6px 8px; font-size: 13px; }
        .summary td:last-child { text-align: right; font-weight: bold; }
        .summary .total td { border-top: 2px solid #e5e7eb; font-size: 15px; color: #4f46e5; }
        .footer { background: #f3f4f6; padding: 16px 24px; font-size: 12px; color: #6b7280; text-align: center; }
        .btn { display: inline-block; background: #4f46e5; color: #fff !important; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-size: 13px; font-weight: 600; margin-top: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?= esc($settings['org_name'] ?? org_setting('org_name', 'FlowDesk')) ?></h1>
        </div>
        <div class="body">
            <p>Dear <strong><?= esc($invoice['customer_name'] ?? 'Valued Customer') ?></strong>,</p>
            <p>Please find your invoice attached to this email.</p>

            <h3 style="margin: 20px 0 8px; font-size: 15px;">Invoice Summary</h3>
            <div class="summary">
                <table>
                    <tr><td>Invoice No:</td><td><?= esc($invoice['invoice_no']) ?></td></tr>
                    <tr><td>Date:</td><td><?= esc($invoice['invoice_date']) ?></td></tr>
                    <tr><td>Due Date:</td><td><?= esc($invoice['due_date']) ?></td></tr>
                    <tr><td>Status:</td><td><?= esc($invoice['status']) ?></td></tr>
                    <?php $symbol = $settings['currency_symbol'] ?? org_setting('currency_symbol', 'KSh'); ?>
                    <tr><td>Grand Total:</td><td><?= $symbol ?> <?= number_format($invoice['grand_total'], 2) ?></td></tr>
                    <tr class="total"><td>Balance Due:</td><td><?= $symbol ?> <?= number_format($invoice['balance_due'], 2) ?></td></tr>
                </table>
            </div>

            <p>If you have any questions about this invoice, please contact us.</p>
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
