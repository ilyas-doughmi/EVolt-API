<?php

namespace App\Jobs;

use App\Models\Reservation;
use App\Models\Station;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EndReservationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $reservationId;


    /**
     * Create a new job instance.
     */
    public function __construct($reservationId)
    {
        $this->reservationId = $reservationId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $reservation = Reservation::find($this->reservationId);
        
        if ($reservation && $reservation->status === 'active') {
            $reservation->update([
                'status' => 'completed',
                'energy_delivered_kwh' => rand(15, 60) 
            ]);

            $station = Station::find($reservation->station_id);
            if ($station) {
                $station->update(['status' => 'available']);
            }
        }
    }
}
