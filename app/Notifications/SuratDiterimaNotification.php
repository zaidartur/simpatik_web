<?php

namespace App\Notifications;

use App\Models\Inbox;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class SuratDiterimaNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    public Inbox $inbox;
    public User $sender;
    public string $actionType;
    public ?string $notes;

    /**
     * Create a new notification instance.
     */
    public function __construct(Inbox $inbox, User $sender, string $actionType = 'disposisi', ?string $notes = null)
    {
        $this->inbox = $inbox;
        $this->sender = $sender;
        $this->actionType = $actionType;
        $this->notes = $notes;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification for database.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'surat_uuid'    => $this->inbox->uuid,
            'no_surat'      => $this->inbox->no_surat,
            'no_agenda'     => $this->inbox->no_agenda,
            'year'          => $this->inbox->year,
            'perihal'       => $this->inbox->perihal,
            'dari'          => $this->inbox->dari,
            'pengirim_nama' => $this->sender->nama_lengkap,
            'pengirim_uuid' => $this->sender->uuid,
            'action_type'   => $this->actionType,
            'notes'         => $this->notes,
            'title'         => $this->actionType === 'disposisi' ? 'Disposisi Surat Masuk' : 'Surat Masuk Baru',
            'message'       => "Surat dari '{$this->inbox->dari}' dengan perihal '{$this->inbox->perihal}' diteruskan oleh {$this->sender->nama_lengkap}.",
            'time'          => now()->translatedFormat('d M Y H:i'),
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     * Strictly sent ONLY to the target recipient user private channel.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id'            => $this->id,
            'surat_uuid'    => $this->inbox->uuid,
            'no_surat'      => $this->inbox->no_surat,
            'no_agenda'     => $this->inbox->no_agenda,
            'perihal'       => $this->inbox->perihal,
            'dari'          => $this->inbox->dari,
            'pengirim_nama' => $this->sender->nama_lengkap,
            'action_type'   => $this->actionType,
            'notes'         => $this->notes,
            'title'         => $this->actionType === 'disposisi' ? 'Disposisi Surat Masuk' : 'Surat Masuk Baru',
            'message'       => "Surat dari '{$this->inbox->dari}' dengan perihal '{$this->inbox->perihal}' diteruskan oleh {$this->sender->nama_lengkap}.",
            'time'          => now()->translatedFormat('d M Y H:i'),
        ]);
    }
}
