<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkflowMailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 30;

    public function __construct(
        private readonly string $subject,
        private readonly string $headline,
        private readonly string $body,
        private readonly ?string $actionUrl = null,
        private readonly string $actionText = 'Buka Detail'
    ) {
        $this->afterCommit();
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $recipientName = $this->resolveRecipientName($notifiable);
        $fromAddress = (string) (config('mail.from.address') ?? '');
        $fromName = trim((string) (config('mail.from.name') ?: 'PASIH'));

        $message = (new MailMessage())
            ->subject($this->subject)
            ->greeting("Halo, {$recipientName}!")
            ->line($this->headline)
            ->line($this->body);

        if (filled($this->actionUrl)) {
            $message->action($this->actionText, (string) $this->actionUrl);
        }

        $message
            ->line('Notifikasi ini dikirim otomatis oleh sistem PASIH.')
            ->salutation("Salam,\nTim PASIH");

        if ($fromAddress !== '') {
            $message->from($fromAddress, $fromName);
        }

        return $message;
    }

    private function resolveRecipientName(object $notifiable): string
    {
        $name = trim((string) ($notifiable->name ?? ''));
        if ($name !== '') {
            return $name;
        }

        return 'Pengguna PASIH';
    }
}
