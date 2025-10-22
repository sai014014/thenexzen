<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BusinessStatusChanged extends Notification
{
    use Queueable;

    protected $oldStatus;
    protected $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct($oldStatus, $newStatus)
    {
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusMessage = $this->getStatusMessage();
        
        return (new MailMessage)
                    ->subject('Business Status Changed')
                    ->line("Your business status has been changed from {$this->oldStatus} to {$this->newStatus}.")
                    ->line($statusMessage)
                    ->action('View Dashboard', url('/business/dashboard'))
                    ->line('Thank you for using our platform!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'business_status_changed',
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => $this->getStatusMessage(),
            'business_id' => $notifiable->business_id ?? null,
        ];
    }

    private function getStatusMessage()
    {
        switch ($this->newStatus) {
            case 'active':
                return 'Your business account is now active. You can access all features.';
            case 'inactive':
                return 'Your business account is now inactive. You will not be able to log in until it is reactivated.';
            case 'suspended':
                return 'Your business account has been suspended. Please contact support for assistance.';
            default:
                return 'Your business status has been updated.';
        }
    }
}
