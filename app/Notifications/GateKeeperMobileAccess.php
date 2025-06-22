<?php

namespace App\Notifications;

use App\Models\GateKeeper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GateKeeperMobileAccess extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private GateKeeper $gateKeeper
    )
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Akses Mobile App Organizer')
            ->greeting('Halo ' . $this->gateKeeper->nama . '!')
            ->line('Anda telah ditambahkan sebagai gate keeper untuk event "' . $this->gateKeeper->event->nama . '".')
            ->line('Sebagai gate keeper, Anda dapat mengakses pindai tiket dan data kehadiran pada event terkait di mobile app organizer.')
            ->line('Detail event:')
            ->line('• Nama Event: ' . $this->gateKeeper->event->nama)
            ->line('• Tanggal: ' . $this->gateKeeper->event->jadwal_mulai . ' - ' . $this->gateKeeper->event->jadwal_selesai)
            ->line('• Lokasi: ' . $this->gateKeeper->event->lokasi)
            ->action('Akses ke Mobile App', $this->gateKeeper->access_link)
            ->line('Link akses di atas adalah khusus untuk Anda. Jangan bagikan link ini kepada orang lain.')
            ->line('Kode akses Anda: ' . $this->gateKeeper->kode_akses)
            ->line('Terima kasih telah bergabung sebagai kolaborator!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'gate_keeper_id' => $this->gateKeeper->id,
            'event_id' => $this->gateKeeper->event_id,
            'event_name' => $this->gateKeeper->event->nama,
        ];
    }
}
