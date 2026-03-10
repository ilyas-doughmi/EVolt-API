<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Reservation::all(),200);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
