<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * E-mail com o código de 6 dígitos para redefinir a senha.
 */
class CodigoRedefinicaoSenha extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $usuario,
        public string $codigo,
        public int $validadeMinutos,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Código para redefinir sua senha - '.config('amostraweb.name'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.codigo-redefinicao-senha');
    }
}
