<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public Payment $payment;

    public string $schoolName;

    public string $schoolYear;

    /**
     * Create a new message instance.
     */
    public function __construct(Payment $payment, string $schoolName = '', string $schoolYear = '')
    {
        $this->payment = $payment;
        $this->schoolName = $schoolName ?: config('app.name', 'E-Fees');
        $this->schoolYear = $schoolYear;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $ref = $this->payment->reference_number ?? 'REC-'.str_pad($this->payment->id, 8, '0', STR_PAD_LEFT);

        return new Envelope(
            subject: "Payment Receipt - {$ref}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment_receipt',
            with: [
                'payment' => $this->payment,
                'schoolName' => $this->schoolName,
                'schoolYear' => $this->schoolYear,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
