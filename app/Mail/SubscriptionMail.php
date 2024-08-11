<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class SubscriptionMail extends Mailable
{
    use Queueable, SerializesModels;
    protected string $template;
    protected array $data;
    /**
     * Create a new message instance.
     */
    public function __construct($data, $template)
    {
        $this->template = $template;
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($_ENV['MAIL_FROM_ADDRESS'], Str::ascii($_ENV['MAIL_FROM_NAME'])),
            replyTo: [
                new Address($_ENV['MAIL_FROM_ADDRESS'], Str::ascii($_ENV['MAIL_FROM_NAME']))
            ],
            subject: $this->data['subscription_type'] == 'new' ? $_ENV['APP_NAME'] . ' - Thank you for purchasing IPTV subscription - '. $this->data['duration']. ' Months' :  $_ENV['APP_NAME'] .' - Subscription Renewed - '.$this->data['duration']. ' Months',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: $this->template
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
