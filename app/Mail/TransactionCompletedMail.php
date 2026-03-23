<?php

namespace App\Mail;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function envelope(): Envelope
    {
        $orderNumber = $this->order->order_number ?? ('ORD-' . $this->order->id);

        return new Envelope(
            subject: 'Transaction Completed - ' . $orderNumber,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.transaction-completed',
        );
    }

    public function attachments(): array
    {
        $orderNumber = $this->order->order_number ?? ('ORD-' . $this->order->id);

        return [
            Attachment::fromData(
                fn () => Pdf::loadView('emails.receipts.order-receipt-pdf', ['order' => $this->order])->output(),
                'receipt-' . $orderNumber . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
