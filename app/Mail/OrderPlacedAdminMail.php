<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderPlacedAdminMail extends Mailable
{
    use SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        $mail = $this->subject('🛒 Нова поръчка: ' . $this->order->order_number)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->to(config('mail.admin_address'))
            ->view('emails.orders.admin')
            ->with(['order' => $this->order]);

        $pdfUrl = $this->waitForPdfUrl($this->order);

        if ($pdfUrl) {
            try {
                $response = Http::get($pdfUrl);
                if ($response->ok()) {
                    $mail->attachData(
                        $response->body(),
                        'econt-label-' . $this->order->order_number . '.pdf',
                        ['mime' => 'application/pdf']
                    );
                    Log::info('📎 Attached Econt PDF for order ' . $this->order->id);
                }
            } catch (\Throwable $e) {
                Log::warning('❌ Failed to attach Econt PDF: ' . $e->getMessage());
            }
        } else {
            Log::warning('⚠️ No PDF available for order ' . $this->order->id);
        }

        return $mail;
    }

    /**
     * Waits briefly for Econt label PDF to appear in DB (up to 2 seconds).
     */
    private function waitForPdfUrl(Order $order): ?string
    {
        $pdfUrl = data_get($order->shipping_payload, 'label.pdfURL');

        if ($pdfUrl) {
            return $pdfUrl;
        }

        // Try again if label might still be saving
        sleep(2);
        $order->refresh();

        return data_get($order->shipping_payload, 'label.pdfURL');
    }
}
