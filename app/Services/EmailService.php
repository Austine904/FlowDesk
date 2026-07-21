<?php

namespace App\Services;

use CodeIgniter\Email\Email;

class EmailService
{
    protected Email $email;

    public function __construct()
    {
        $this->email = \Config\Services::email();
    }

    public function sendInvoice(array $invoice, array $parts, array $tasks, array $sublets, array $settings): bool
    {
        $pdfService = new PdfService();
        $pdfContent = $pdfService->generateFromViewOutput('admin/invoices/pdf', compact('invoice', 'parts', 'tasks', 'sublets', 'settings'));

        $this->email->setFrom(
            $settings['org_email'] ?? org_setting('org_email'),
            $settings['org_name'] ?? org_setting('org_name')
        );
        $this->email->setTo($invoice['customer_email']);
        $this->email->setSubject('Invoice ' . $invoice['invoice_no'] . ' from ' . org_setting('org_name'));
        $this->email->setMessage(view('emails/invoice', compact('invoice', 'settings')));
        $this->email->attachContent($pdfContent, 'Invoice-' . $invoice['invoice_no'] . '.pdf', 'application/pdf');

        return $this->email->send();
    }

    public function sendReceipt(array $receipt, array $settings): bool
    {
        $this->email->setFrom(org_setting('org_email'), org_setting('org_name'));
        $this->email->setTo($receipt['customer_email'] ?? '');
        $this->email->setSubject('Payment Receipt ' . $receipt['receipt_no'] . ' from ' . org_setting('org_name'));
        $this->email->setMessage(view('emails/receipt', compact('receipt', 'settings')));

        return $this->email->send();
    }

    public function getDebugMessage(): string
    {
        return $this->email->printDebugger();
    }
}
