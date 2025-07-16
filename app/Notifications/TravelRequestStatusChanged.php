<?php

namespace App\Notifications;

use App\Models\TravelRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TravelRequestStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public TravelRequest $travelRequest,
        public string $previousStatus,
        public string $newStatus
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Travel Request Status Update')
            ->line($this->getStatusMessage())
            ->line('Travel Details:')
            ->line('Destination: ' . $this->travelRequest->destination)
            ->line('Departure: ' . $this->travelRequest->departure_date->format('d/m/Y'))
            ->line('Return: ' . $this->travelRequest->return_date->format('d/m/Y'))
            ->when($this->travelRequest->rejection_reason, function ($message) {
                return $message->line('Reason: ' . $this->travelRequest->rejection_reason);
            })
            ->action('View Travel Request', url('/travel-requests/' . $this->travelRequest->id))
            ->line('Thank you for using our travel management system!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'travel_request_id' => $this->travelRequest->id,
            'previous_status' => $this->previousStatus,
            'new_status' => $this->newStatus,
            'destination' => $this->travelRequest->destination,
            'departure_date' => $this->travelRequest->departure_date,
            'return_date' => $this->travelRequest->return_date,
            'message' => $this->getStatusMessage(),
        ];
    }

    /**
     * Get the status change message
     */
    private function getStatusMessage(): string
    {
        return match ($this->newStatus) {
            TravelRequest::STATUS_APPROVED => 'Your travel request has been approved!',
            TravelRequest::STATUS_CANCELLED => 'Your travel request has been cancelled.',
            TravelRequest::STATUS_REJECTED => 'Your travel request has been rejected.',
            default => 'Your travel request status has been updated.',
        };
    }
}
