<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt <?= esc($receipt['receipt_no']) ?></title>
    <link rel="stylesheet" href="<?= base_url('public/assets/css/tailwind.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; }
        }
        body { font-family: 'Inter', sans-serif; background: white; }
    </style>
</head>
<body class="bg-white p-8 max-w-2xl mx-auto">

    <div class="no-print flex gap-3 mb-6">
        <button onclick="window.print()" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Print Receipt
        </button>
        <button onclick="window.close()" 
                class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Close
        </button>
    </div>

    <div class="border-2 border-gray-800 rounded-xl p-8">

        <div class="flex justify-between items-start mb-8">
            <div>
                <?php if ($receipt['org_logo']): ?>
                <img src="<?= base_url('uploads/org/' . $receipt['org_logo']) ?>" 
                     alt="Logo" class="h-16 mb-3 object-contain">
                <?php endif; ?>
                <h2 class="text-lg font-bold text-gray-900"><?= esc($receipt['org_name']) ?></h2>
                <?php if ($receipt['org_address']): ?>
                <p class="text-sm text-gray-600"><?= nl2br(esc($receipt['org_address'])) ?></p>
                <?php endif; ?>
                <?php if ($receipt['org_phone']): ?>
                <p class="text-sm text-gray-600">Tel: <?= esc($receipt['org_phone']) ?></p>
                <?php endif; ?>
                <?php if ($receipt['org_email']): ?>
                <p class="text-sm text-gray-600"><?= esc($receipt['org_email']) ?></p>
                <?php endif; ?>
            </div>
            <div class="text-right">
                <h1 class="text-3xl font-bold text-indigo-600 tracking-wider">RECEIPT</h1>
                <p class="text-sm text-gray-600 mt-2">Receipt No: <span class="font-semibold text-gray-900"><?= esc($receipt['receipt_no']) ?></span></p>
                <p class="text-sm text-gray-600">Date: <span class="font-semibold text-gray-900"><?= date('d M Y', strtotime($receipt['receipt_date'])) ?></span></p>
                <p class="text-sm text-gray-600">Invoice: <span class="font-semibold text-gray-900"><?= esc($receipt['invoice_no'] ?? '') ?></span></p>
                <p class="text-sm text-gray-600">Job: <span class="font-semibold text-gray-900"><?= esc($receipt['job_no'] ?? '') ?></span></p>
            </div>
        </div>

        <hr class="border-gray-300 mb-6">

        <div class="mb-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Received From</p>
            <p class="text-lg font-semibold text-gray-900"><?= esc($receipt['customer_name']) ?></p>
            <p class="text-sm text-gray-600"><?= esc($receipt['customer_phone']) ?></p>
            <?php if (!empty($receipt['registration_number'])): ?>
            <p class="text-sm text-gray-600">Vehicle: <?= esc($receipt['registration_number']) ?></p>
            <?php endif; ?>
        </div>

        <div class="bg-gray-50 rounded-xl p-6 mb-6">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Payment Method</p>
                    <p class="text-sm font-semibold text-gray-900"><?= esc($receipt['payment_method']) ?></p>
                </div>
                <?php if ($receipt['reference_no']): ?>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Reference</p>
                    <p class="text-sm font-semibold text-gray-900"><?= esc($receipt['reference_no']) ?></p>
                </div>
                <?php endif; ?>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Amount Paid</p>
                <p class="text-4xl font-bold text-indigo-600"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($receipt['amount_paid'], 2) ?></p>
                <p class="text-sm text-gray-600 mt-1 italic"><?= esc($receipt['amount_in_words']) ?></p>
            </div>
        </div>

        <div class="mb-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Invoice Summary</p>
            <table class="w-full text-sm">
                <tr class="border-b border-gray-100">
                    <td class="py-1.5 text-gray-600">Parts Total</td>
                    <td class="py-1.5 text-right font-medium"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($receipt['parts_total'], 2) ?></td>
                </tr>
                <tr class="border-b border-gray-100">
                    <td class="py-1.5 text-gray-600">Labor Total</td>
                    <td class="py-1.5 text-right font-medium"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($receipt['labor_total'], 2) ?></td>
                </tr>
                <?php if ($receipt['sublet_total'] > 0): ?>
                <tr class="border-b border-gray-100">
                    <td class="py-1.5 text-gray-600">Sublets Total</td>
                    <td class="py-1.5 text-right font-medium"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($receipt['sublet_total'], 2) ?></td>
                </tr>
                <?php endif; ?>
                <tr class="border-b border-gray-100">
                    <td class="py-1.5 text-gray-600">Subtotal</td>
                    <td class="py-1.5 text-right font-medium"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($receipt['subtotal'], 2) ?></td>
                </tr>
                <tr class="border-b border-gray-100">
                    <td class="py-1.5 text-gray-600">VAT (<?= $receipt['vat_rate'] ?>%)</td>
                    <td class="py-1.5 text-right font-medium"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($receipt['vat_amount'], 2) ?></td>
                </tr>
                <tr class="border-b border-gray-200 font-semibold">
                    <td class="py-2 text-gray-900">Grand Total</td>
                    <td class="py-2 text-right text-gray-900"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($receipt['grand_total'], 2) ?></td>
                </tr>
                <tr class="border-b border-gray-100 text-emerald-600">
                    <td class="py-1.5">Amount Paid</td>
                    <td class="py-1.5 text-right font-semibold"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($receipt['amount_paid'], 2) ?></td>
                </tr>
                <tr class="<?= $receipt['balance_after'] > 0 ? 'text-red-600' : 'text-emerald-600' ?> font-bold">
                    <td class="py-2">Balance Remaining</td>
                    <td class="py-2 text-right"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($receipt['balance_after'], 2) ?></td>
                </tr>
            </table>
        </div>

        <hr class="border-gray-300 mb-4">
        <div class="flex justify-between items-end">
            <div>
                <p class="text-xs text-gray-500">Received by</p>
                <p class="text-sm font-semibold text-gray-900"><?= esc($receipt['received_by_name']) ?></p>
                <p class="text-xs text-gray-400 mt-1">Generated: <?= date('d M Y H:i', strtotime($receipt['created_at'])) ?></p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500 italic">Thank you for your business.</p>
                <p class="text-xs text-gray-400">This is a computer-generated receipt.</p>
            </div>
        </div>

    </div>

</body>
</html>
