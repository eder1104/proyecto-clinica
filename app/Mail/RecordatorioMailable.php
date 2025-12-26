<?php

namespace App\Mail;

use App\Models\RecordatorioCita;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecordatorioMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $recordatorio;

    public function __construct(RecordatorioCita $recordatorio)
    {
        $this->recordatorio = $recordatorio;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recordatorio de Cita Médica',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.recordatorio',
        );
    }
}