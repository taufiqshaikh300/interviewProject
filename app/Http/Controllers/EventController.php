<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventBooking;
use App\Models\EventTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function addEvent(){
        return view('organizer.addEvent');
    }

    public function storeEvent(Request $request)
    {
        $now = Carbon::now();
        $today = $now->toDateString();
        
        // Validate input
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => ['required', 'date', function ($attribute, $value, $fail) use ($today) {
                if ($value < $today) {
                    $fail('The event date must be today or a future date.');
                }
            }],
            'start_time' => 'required|date_format:H:i',
            'end_time' => ['required', 'date_format:H:i', function ($attribute, $value, $fail) use ($request) {
                if ($request->start_time && $value <= $request->start_time) {
                    $fail('The end time must be later than the start time.');
                }
            }],
            'location' => 'required|string|max:255',
            'ticket_types' => 'required|array|min:1',
            'ticket_types.*' => 'required|string|max:255',
            'ticket_prices' => 'required|array|min:1',
            'ticket_prices.*' => 'required|numeric|min:0',
            'ticket_quantities' => 'required|array|min:1',
            'ticket_quantities.*' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Format start and end time
        $startTime = Carbon::parse($request->date . ' ' . $request->start_time);
        $endTime = Carbon::parse($request->date . ' ' . $request->end_time);
        
        // Calculate total tickets
        $totalTickets = array_sum($request->ticket_quantities);

        DB::beginTransaction();

        try {
            // Create event
            $event = Event::create([
                'organizer_id' => session('user_id'),
                'title' => $request->title,
                'description' => $request->description ?? '',
                'start_time' => $startTime,
                'end_time' => $endTime,
                'location' => $request->location,
                'total_tickets' => $totalTickets,
                'status' => 'active'
            ]);

            // Store tickets
            foreach ($request->ticket_types as $index => $ticketType) {
                EventTicket::create([
                    'event_id' => $event->id,
                    'ticket_type' => $ticketType,
                    'price' => $request->ticket_prices[$index],
                    'quantity' => $request->ticket_quantities[$index]
                ]);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Event created successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }



    public function myEvents(Request $request)
    {
        $organizerId = session('user_id'); // Get organizer ID from session
    
        $query = Event::where('organizer_id', $organizerId); // Show only organizer's events
    
        // Filter by event title
        if ($request->has('title') && !empty($request->title)) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
    
        // Filter by location
        if ($request->has('location') && !empty($request->location)) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }
    
        // Filter by start date
        if ($request->has('start_time') && !empty($request->start_time)) {
            $query->whereDate('start_time', '>=', $request->start_time);
        }
    
        // Filter by end date
        if ($request->has('end_time') && !empty($request->end_time)) {
            $query->whereDate('end_time', '<=', $request->end_time);
        }
    
        // Get events and their total booked tickets
        $events = $query->withCount(['bookings as total_booked_tickets' => function ($query) {
            // Modify the json_extract to sum the "quantity" field from the tickets array
            $query->select(DB::raw('SUM(CAST(json_extract(tickets, "$[*].quantity") AS UNSIGNED))'))
                  ->whereNotNull('tickets') // Ensure tickets field is not null
                  ->groupBy('event_id'); // Group by event_id to sum by event
        }])->orderBy('start_time', 'asc')->paginate(5);
    
        return view('organizer.allEvents', compact('events'));
    }
    
    
    

    public function cancelEvent(Request $request)
    {
        // Validate request
        $request->validate([
            'event_id' => 'required|exists:events,id',
        ]);
    
        // Find event
        $event = Event::findOrFail($request->event_id);
    
        // Check if the event is already cancelled
        if ($event->status == 'cancelled') {
            return redirect()->back()->with('error', 'Event is already cancelled.');
        }
    
        // Update event status
        $event->status = 'cancelled';
        $event->save();
    
        return redirect()->back()->with('success', 'Event has been cancelled successfully.');
    }

    public function allPublicEvents(Request $request)
{
    $query = Event::query(); // Fetch all events

    // Filter by event title
    if ($request->has('title') && !empty($request->title)) {
        $query->where('title', 'like', '%' . $request->title . '%');
    }

    // Filter by location
    if ($request->has('location') && !empty($request->location)) {
        $query->where('location', 'like', '%' . $request->location . '%');
    }

    // Filter by start date
    if ($request->has('start_time') && !empty($request->start_time)) {
        $query->whereDate('start_time', '>=', $request->start_time);
    }

    // Filter by end date
    if ($request->has('end_time') && !empty($request->end_time)) {
        $query->whereDate('end_time', '<=', $request->end_time);
    }

    $events = $query->orderBy('start_time', 'desc')->paginate(5);

    return view('organizer.publicEvent', compact('events'));
}

public function edit($id)
{
    $event = Event::findOrFail($id);
  
    return view('organizer.editEvent', compact('event'));
}
public function update(Request $request, Event $event)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'location' => 'required|string|max:255',
            'ticket_types.*' => 'required|string',
            'ticket_prices.*' => 'required|numeric|min:0',
            'ticket_quantities.*' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_time' => $request->date . ' ' . $request->start_time,
            'end_time' => $request->date . ' ' . $request->end_time,
            'location' => $request->location,
        ]);

        EventTicket::where('event_id', $event->id)->delete();

        foreach ($request->ticket_types as $index => $type) {
            EventTicket::create([
                'event_id' => $event->id,
                'ticket_type' => $type,
                'price' => $request->ticket_prices[$index],
                'quantity' => $request->ticket_quantities[$index],
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Event updated successfully']);
    }

    public function viewEvent($id)
    {
        $event = Event::where('id', $id)->where('status', 'active')->first();
        
        if (!$event) {
            return redirect()->route('organizer.allEvents')->with('error', 'Event not found or inactive.');
        }
    
        // Fetch tickets associated with the event
        $tickets = EventTicket::where('event_id', $id)->get();
    
        // Fetch event bookings for the event
        $eventBookings = EventBooking::where('event_id', $id)->get();
    
        // Prepare ticket details (optional, based on your needs)
        $bookedTicketDetails = [];
        foreach ($eventBookings as $booking) {
            $ticketsBooked = json_decode($booking->tickets, true);
            foreach ($ticketsBooked as $ticket) {
                $bookedTicketDetails[$ticket['ticket_id']] = isset($bookedTicketDetails[$ticket['ticket_id']]) ? $bookedTicketDetails[$ticket['ticket_id']] + $ticket['quantity'] : $ticket['quantity'];
            }
        }
    
        return view('organizer.event-details', compact('event', 'tickets', 'eventBookings', 'bookedTicketDetails'));
    }

    public function exportEventDetails($id)
{
    // Fetch the event details
    $event = Event::where('id', $id)->where('status', 'active')->first();
    if (!$event) {
        return redirect()->route('organizer.allEvents')->with('error', 'Event not found or inactive.');
    }

    // Fetch tickets for the event
    $tickets = EventTicket::where('event_id', $id)->get();

    // Fetch event bookings (Users who booked the event tickets)
    $eventBookings = EventBooking::where('event_id', $id)->get();

    // Prepare the CSV content
    $csvContent = [];

    // Add headings to the CSV
    $csvContent[] = ['Ticket Type', 'Quantity', 'Ticket Price', 'User Name', 'User Email', 'Total Price'];

    foreach ($eventBookings as $booking) {
        $ticketsBooked = json_decode($booking->tickets, true); // Decode the JSON data from 'tickets' column

        foreach ($ticketsBooked as $ticket) {
            $totalPrice = $ticket['quantity'] * $ticket['ticket_price'];
            $csvContent[] = [
                $ticket['ticket_type'],
                $ticket['quantity'],
                $ticket['ticket_price'],
                $booking->user->name,  // Assuming the 'user' relationship exists in EventBooking
                $booking->user->email,
                $totalPrice
            ];
        }
    }

    // Create a CSV string
    $csvFile = fopen('php://temp', 'r+');
    foreach ($csvContent as $line) {
        fputcsv($csvFile, $line);
    }
    rewind($csvFile);
    
    // Return the CSV file as a response
    $csvData = stream_get_contents($csvFile);
    fclose($csvFile);

    // Set the headers to download the file
    return response($csvData)
        ->header('Content-Type', 'text/csv')
        ->header('Content-Disposition', 'attachment; filename="event-details.csv"');
}


public function dashboard()
{
    $organizerId = session('user_id'); // Assuming the user_id is stored in session

    // Get the number of events created by the organizer
    $totalEvents = Event::where('organizer_id', $organizerId)->where('status', 'active')->count();

    // Get the total tickets sold for the organizer's events
    $events = Event::where('organizer_id', $organizerId)->pluck('id');
    $totalTicketsSold = EventBooking::whereIn('event_id', $events)
                                    ->get()
                                    ->sum(function($booking) {
                                        $tickets = json_decode($booking->tickets, true);
                                        return array_sum(array_column($tickets, 'quantity'));
                                    });

    // Calculate total earnings based on ticket price and quantity sold
    $totalEarnings = EventBooking::whereIn('event_id', $events)
                                 ->get()
                                 ->sum(function($booking) {
                                     $total = 0;
                                     $tickets = json_decode($booking->tickets, true);
                                     foreach ($tickets as $ticket) {
                                         $total += $ticket['quantity'] * $ticket['ticket_price'];
                                     }
                                     return $total;
                                 });

    return view('organizer.dashboard', compact('totalEvents', 'totalTicketsSold', 'totalEarnings'));
}

    


}
