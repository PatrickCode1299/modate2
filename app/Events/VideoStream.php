<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class VideoStream implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $streamData;
    public $streamId;

    /**
     * Create a new event instance.
     *
     * @param string $streamData
     * @param string $streamId
     * @return void
     */
    public function __construct($streamData, $streamId)
    {
        $this->streamData = $streamData;
        $this->streamId = $streamId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        // Ensure this matches the channel you are listening to on the frontend
        return new Channel('live-stream.' . $this->streamId);
    }

    /**
     * Get the broadcast event name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        // Ensure this matches the event name you are listening to on the frontend
        return 'stream-video';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'streamData' => $this->streamData,
            'streamId' => $this->streamId,
        ];
    }
}