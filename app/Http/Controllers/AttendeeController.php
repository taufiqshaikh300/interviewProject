<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventBooking;
use App\Models\EventTicket;
use Illuminate\Http\Request;

class AttendeeController extends Controller
{
    public function availableEvents(Request $request)
    {
        // Fetch search filters
        $search = $request->input('search');
        $date = $request->input('date');
    
        // Query events with filters
        $events = Event::where('status', 'active')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%$search%")
                      ->orWhere('location', 'like', "%$search%");
                });
            })
            ->when($date, function ($query, $date) {
                return $query->whereDate('start_time', $date);
            })
            ->orderBy('start_time', 'asc')
            ->paginate(6); // Paginate with 6 events per page
    
        return view('attendee.dashboard', compact('events'));
    }

    public function viewEvent($id)
    {
        $event = Event::where('id', $id)->where('status', 'active')->first();
    
        if (!$event) {
            return redirect()->route('attendee.availableEvents')->with('error', 'Event not found or inactive.');
        }
    
        // Fetch tickets associated with the event
        $tickets = EventTicket::where('event_id', $id)->get();
    
        // Initialize an array to track how many tickets of each type have been sold
        $soldTickets = [];
    
        // Fetch all bookings related to this event and extract ticket data from the JSON
        $bookedTicketsData = EventBooking::where('event_id', $id)->get();
    
        // Loop through each booking and decode the JSON to calculate the sold tickets
        foreach ($bookedTicketsData as $booking) {
            $ticketsData = json_decode($booking->tickets, true); // Decode the tickets JSON
    
            // Loop through each ticket in the booking and update the sold quantity
            foreach ($ticketsData as $ticketData) {
                $ticketId = $ticketData['ticket_id'];
                $quantity = $ticketData['quantity'];
    
                // Update sold quantity for each ticket type
                if (isset($soldTickets[$ticketId])) {
                    $soldTickets[$ticketId] += $quantity;
                } else {
                    $soldTickets[$ticketId] = $quantity;
                }
            }
        }
    
        // Update available ticket quantities
        foreach ($tickets as $ticket) {
            // If the ticket has been sold, subtract from the available quantity
            $soldQuantity = isset($soldTickets[$ticket->id]) ? $soldTickets[$ticket->id] : 0;
            $ticket->available_quantity = $ticket->quantity - $soldQuantity;
        }
    
        return view('attendee.event-details', compact('event', 'tickets'));
    }
    
    
    

}
