<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice <?= esc($invoice['invoice_no']) ?></title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; margin: 0; padding: 24px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 24px; }
        .org-section { max-width: 50%; }
        .org-name { font-size: 18px; font-weight: bold; color: #111827; }
        .org-details { font-size: 10px; color: #6b7280; margin-top: 4px; }
        .invoice-title-section { text-align: right; }
        .invoice-title { font-size: 28px; font-weight: bold; color: #4f46e5; }
        .invoice-meta { font-size: 10px; color: #6b7280; margin-top: 4px; }
        .invoice-meta strong { color: #111827; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 9999px; font-size: 9px; font-weight: 600; border: 1px solid #e5e7eb; }
        .bill-section { margin-bottom: 20px; }
        .bill-section h3 { font-size: 10px; font-weight: bold; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 4px; }
        .bill-section .name { font-weight: bold; font-size: 12px; }
        .bill-section .detail { font-size: 10px; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th { background: #f9fafb; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; color: #6b7280; border-bottom: 2px solid #e5e7eb; }
        td { padding: 6px 8px; border-bottom: 1px solid #f3f4f6; font-size: 10px; }
        .total-table td { padding: 4px 8px; }
        .total-row td { font-weight: bold; border-top: 2px solid #e5e7eb; }
        .grand-total td { font-size: 14px; font-weight: bold; color: #4f46e5; }
        .text-right { text-align: right; }
        .section-title { font-size: 10px; font-weight: bold; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin: 16px 0 6px; border-bottom: 1px solid #e5e7eb; padding-bottom: 3px; }
        .footer { margin-top: 32px; border-top: 1px solid #e5e7eb; padding-top: 12px; font-size: 9px; color: #6b7280; text-align: center; }
        .no-data { text-align: center; color: #9ca3af; padding: 12px; font-size: 10px; }
        .amount-in-words { font-size: 10px; color: #6b7280; margin-top: 8px; font-style: italic; }
        .grid-2 { display: flex; gap: 24px; margin-bottom: 16px; }
        .grid-2 > div { flex: 1; }
    </style>
</head>
<body>

    <div class="header">
        <div class="org-section">
            <?php $settings = $settings ?? []; ?>
            <div class="org-name"><?= esc($settings['org_name'] ?? org_setting('org_name', 'FlowDesk')) ?></div>
            <div class="org-details">
                <?= nl2br(esc($settings['org_address'] ?? org_setting('org_address', ''))) ?><br>
                <?= esc($settings['org_phone'] ?? org_setting('org_phone', '')) ?><br>
                <?= esc($settings['org_email'] ?? org_setting('org_email', '')) ?>
            </div>
        </div>
        <div class="invoice-title-section">
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-meta">
                <strong>Invoice No:</strong> <?= esc($invoice['invoice_no']) ?><br>
                <strong>Date:</strong> <?= esc($invoice['invoice_date']) ?><br>
                <strong>Due Date:</strong> <?= esc($invoice['due_date']) ?><br>
                <span class="badge"><?= esc($invoice['status']) ?></span>
            </div>
        </div>
    </div>

    <div class="bill-section">
        <h3>Bill To:</h3>
        <div class="name"><?= esc($invoice['customer_name'] ?? 'N/A') ?></div>
        <div class="detail"><?= esc($invoice['customer_phone'] ?? '') ?></div>
        <div class="detail"><?= esc($invoice['customer_email'] ?? '') ?></div>
    </div>

    <div class="section-title">Parts</div>
    <?php if (!empty($parts)): ?>
    <table>
        <thead>
            <tr>
                <th>Part Name</th>
                <th>Part No</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $symbol = $settings['currency_symbol'] ?? org_setting('currency_symbol', 'KSh'); ?>
            <?php foreach ($parts as $part): ?>
            <tr>
                <td><?= esc($part['name'] ?? 'N/A') ?></td>
                <td><?= esc($part['part_number'] ?? '—') ?></td>
                <td class="text-right"><?= (int) ($part['quantity_required'] ?? 0) ?></td>
                <td class="text-right"><?= $symbol ?> <?= number_format($part['unit_price_at_estimate'] ?? 0, 2) ?></td>
                <td class="text-right"><?= $symbol ?> <?= number_format(($part['quantity_required'] ?? 0) * ($part['unit_price_at_estimate'] ?? 0), 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="no-data">No parts recorded.</div>
    <?php endif; ?>

    <div class="section-title">Labor Tasks</div>
    <?php if (!empty($tasks)): ?>
    <table>
        <thead>
            <tr>
                <th>Task</th>
                <th class="text-right">Hours</th>
                <th class="text-right">Rate/hr</th>
                <th class="text-right">Cost</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?= esc($task['task_name'] ?? 'N/A') ?></td>
                <td class="text-right"><?= number_format($task['estimated_hours'] ?? 0, 2) ?></td>
                <td class="text-right"><?= $symbol ?> <?= number_format($task['rate_per_hour'] ?? 0, 2) ?></td>
                <td class="text-right"><?= $symbol ?> <?= number_format($task['labor_cost'] ?? 0, 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="no-data">No labor tasks recorded.</div>
    <?php endif; ?>

    <div class="section-title">Sublets</div>
    <?php if (!empty($sublets)): ?>
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Supplier</th>
                <th class="text-right">Cost</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sublets as $sublet): ?>
            <tr>
                <td><?= esc($sublet['description'] ?? 'N/A') ?></td>
                <td><?= esc($sublet['provider_name'] ?? 'N/A') ?></td>
                <td class="text-right"><?= $symbol ?> <?= number_format($sublet['cost'] ?? 0, 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="no-data">No sublets recorded.</div>
    <?php endif; ?>

    <table class="total-table" style="width: 50%; margin-left: auto;">
        <tbody>
            <tr>
                <td style="text-align: right; font-size: 10px; color: #6b7280;">Parts Total</td>
                <td class="text-right" style="font-size: 10px;"><?= $symbol ?> <?= number_format($invoice['parts_total'], 2) ?></td>
            </tr>
            <tr>
                <td style="text-align: right; font-size: 10px; color: #6b7280;">Labor Total</td>
                <td class="text-right" style="font-size: 10px;"><?= $symbol ?> <?= number_format($invoice['labor_total'], 2) ?></td>
            </tr>
            <tr>
                <td style="text-align: right; font-size: 10px; color: #6b7280;">Sublets Total</td>
                <td class="text-right" style="font-size: 10px;"><?= $symbol ?> <?= number_format($invoice['sublet_total'], 2) ?></td>
            </tr>
            <?php if ((float)($invoice['lpo_parts_total'] ?? 0) > 0): ?>
            <tr>
                <td style="text-align: right; font-size: 10px; color: #6b7280;">LPO Parts Total</td>
                <td class="text-right" style="font-size: 10px;"><?= $symbol ?> <?= number_format($invoice['lpo_parts_total'], 2) ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td style="text-align: right; font-size: 10px; color: #6b7280;">Subtotal</td>
                <td class="text-right" style="font-size: 10px;"><?= $symbol ?> <?= number_format($invoice['subtotal'], 2) ?></td>
            </tr>
            <tr>
                <td style="text-align: right; font-size: 10px; color: #6b7280;">VAT (<?= number_format($invoice['vat_rate'], 2) ?>%)</td>
                <td class="text-right" style="font-size: 10px;"><?= $symbol ?> <?= number_format($invoice['vat_amount'], 2) ?></td>
            </tr>
            <?php if ((float)($invoice['other_charges'] ?? 0) > 0): ?>
            <tr>
                <td style="text-align: right; font-size: 10px; color: #6b7280;">Other Charges <?= !empty($invoice['other_charges_description']) ? '(' . esc($invoice['other_charges_description']) . ')' : '' ?></td>
                <td class="text-right" style="font-size: 10px;"><?= $symbol ?> <?= number_format($invoice['other_charges'], 2) ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td style="text-align: right; font-size: 10px; color: #6b7280;">Discount</td>
                <td class="text-right" style="font-size: 10px;">- <?= $symbol ?> <?= number_format($invoice['discount'], 2) ?></td>
            </tr>
            <tr class="grand-total">
                <td style="text-align: right;">GRAND TOTAL</td>
                <td class="text-right"><?= $symbol ?> <?= number_format($invoice['grand_total'], 2) ?></td>
            </tr>
            <tr>
                <td style="text-align: right; font-size: 10px; color: #6b7280;">Amount Paid</td>
                <td class="text-right" style="font-size: 10px;"><?= $symbol ?> <?= number_format($invoice['amount_paid'], 2) ?></td>
            </tr>
            <tr>
                <td style="text-align: right; font-size: 11px; font-weight: bold; color: <?= $invoice['balance_due'] == 0 ? '#059669' : '#dc2626' ?>;">BALANCE DUE</td>
                <td class="text-right" style="font-size: 11px; font-weight: bold; color: <?= $invoice['balance_due'] == 0 ? '#059669' : '#dc2626' ?>;"><?= $symbol ?> <?= number_format($invoice['balance_due'], 2) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <?= esc($settings['org_name'] ?? org_setting('org_name', 'FlowDesk')) ?> —
        <?= esc($settings['org_phone'] ?? org_setting('org_phone', '')) ?> —
        <?= esc($settings['org_email'] ?? org_setting('org_email', '')) ?><br>
        <small>Generated on <?= date('Y-m-d H:i:s') ?></small>
    </div>

</body>
</html>
