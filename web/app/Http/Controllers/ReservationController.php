<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Station;
use Carbon\Carbon;
use App\Jobs\EndReservationJob;


class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $reservations = Reservation::with('station')
                                    ->where('user_id',$user->id)
                                    ->get();

        return response()->json([
            'message' =>  'Historique récupéré',
            'résérvation' => $reservations
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'station_id' => 'required|exists:stations,id', 
            'start_time' => 'required|date|after_or_equal:now', 
            'duration_minutes' => 'required|integer|min:15',
        ]);
        
        $starttime = Carbon::parse($validatedData['start_time']);
        $endtime = $starttime->copy()->addMinutes($validatedData['duration_minutes']);

        $reservation = Reservation::create([
            'user_id' => $request->user()->id,
            'station_id' => $validatedData['station_id'],
            'start_time' => $starttime,
            'duration_minutes' => $validatedData['duration_minutes'],
            'end_time' => $endtime,
            'status' => 'active',
        ]);

       $station = Station::find($validatedData['station_id']);
        $station->update(['status' => 'occupied']);

        EndReservationJob::dispatch($reservation->id)->delay($endtime);

        return response()->json([
            'message' => 'reservation created',
            'reservation' => $reservation
        ],201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->user_id !== $request->user()->id) {
            return response()->json(['message' => 'no permission'], 403);
        }

        $validatedData = $request->validate([
            'start_time' => 'sometimes|required|date|after_or_equal:now',
            'duration_minutes' => 'sometimes|required|integer|min:15',
        ]);

        $startTime = isset($validatedData['start_time']) ? Carbon::parse($validatedData['start_time']) : Carbon::parse($reservation->start_time);
        $duration = $validatedData['duration_minutes'] ?? $reservation->duration_minutes;
        
        $endTime = $startTime->copy()->addMinutes($duration);

        $reservation->update([
            'start_time' => $startTime,
            'duration_minutes' => $duration,
            'end_time' => $endTime,
        ]);

        return response()->json([
            'message' => 'résérvation updated',
            'reservation' => $reservation
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,string $id)
    {
        $reservation = Reservation::findOrFail($id);

        if($reservation->user_id !== $request->user()->id)
        {
            return response()->json(['message' => 'you don\'t have access'],403);
        }

        $reservation->delete();

        return response()->json(['message' => 'reservation cancelled'],200);
    }
}
