<?php

namespace App\Notifications;

use App\Models\Collaborator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use function Illuminate\Log\log;

class CollaboratorInvitation extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Collaborator $collaborator
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        log('Sending collaborator invitation email', [
            'collaborator_id' => $this->collaborator->id,
            'event_id' => $this->collaborator->event_id,
        ]);
        
        return (new MailMessage)
            ->subject('Undangan Kolaborator Event - ' . $this->collaborator->event->nama)
            ->greeting('Halo ' . $this->collaborator->nama . '!')
            ->line('Anda telah ditambahkan sebagai kolaborator untuk event "' . $this->collaborator->event->nama . '".')
            ->line('Sebagai kolaborator, Anda dapat mengakses dashboard event dan melihat data transaksi.')
            ->line('Detail event:')
            ->line('• Nama Event: ' . $this->collaborator->event->nama)
            ->line('• Tanggal: ' . $this->collaborator->event->jadwal_mulai . ' - ' . $this->collaborator->event->jadwal_selesai)
            ->line('• Lokasi: ' . $this->collaborator->event->lokasi)
            ->action('Akses Dashboard Event', $this->collaborator->access_link)
            ->line('Link akses di atas adalah khusus untuk Anda. Jangan bagikan link ini kepada orang lain.')
            ->line('Kode akses Anda: ' . $this->collaborator->kode_akses)
            ->line('Terima kasih telah bergabung sebagai kolaborator!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'collaborator_id' => $this->collaborator->id,
            'event_id' => $this->collaborator->event_id,
            'event_name' => $this->collaborator->event->nama,
        ];
    }
}
