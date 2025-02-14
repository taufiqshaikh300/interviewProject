<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventBooking;
use App\Models\EventTicket;
use Illuminate\Http\Request;
use Psy\Readline\Hoa\EventBucket;
use Illuminate\Support\Str;

class BookingController extends Controller
{
        // Show the event booking page
        public function showBookingPage($eventId)
        {
            $event = Event::findOrFail($eventId);
            $tickets = EventTicket::where('event_id', $eventId)->get(); // Assuming Ticket is a model that stores ticket info
    
            return view('booking', compact('event', 'tickets'));
        }
    
        // Handle ticket booking and payment
        public function bookTickets(Request $request)
        {
            // Validate the input data
            $request->validate([
                'tickets' => 'required|json',
                'card_holder_name' => 'required|string',
                'card_number' => 'required|string',
                'expiry_date' => 'required|string',
                'cvv' => 'required|string',
                'amount' => 'required|numeric',
            ]);
        
            // Decode the ticket data from JSON
            $tickets = json_decode($request->tickets, true);
        
            // Fetch ticket details for each ticket_id and add extra info
            foreach ($tickets as &$ticket) {
                // Fetch the ticket details from the database (you might already have this info in your Ticket model)
                $ticketDetails = EventTicket::find($ticket['ticket_id']); // Assuming you have a Ticket model
        
                if ($ticketDetails) {
                    // Add ticket details to the ticket object
               
                    $ticket['ticket_type'] = $ticketDetails->ticket_type;
                    $ticket['ticket_price'] = $ticketDetails->price;
                }
            }
        
            // Process payment (assuming payment is successful)
        
            // Create the booking record
            $booking = EventBooking::create([
                'user_id' => session('user_id'),
                'event_id' => $request->event_id,
                'tickets' => json_encode($tickets),
                'total_price' => $request->amount,
                'payment_status' => 'paid', // Assuming payment is successful
                'card_holder_name' => $request->card_holder_name,
                'card_last_four' => substr($request->card_number, -4), // Last 4 digits of the card
                'card_type' => 'Visa', // This should be dynamically determined based on the card type
                'transaction_id' => 'TXN_' . strtoupper(Str::random(10)), // Unique transaction ID
            ]);
        
            // Return success response with booking ID
            return response()->json([
                'success' => true,
                'booking_id' => $booking->id,
            ]);
        }
        

        public function paymentdone(){
            return view('attendee.paymentsuccess');
        }

        public function mypurchase(Request $request)
        {
            // Assuming user is authenticated
            $user = session('user_id'); // Use Laravel's auth helper to fetch the authenticated user
        
            // Fetch all bookings related to the logged-in user
            $bookings = EventBooking::where('user_id', $user)  // User ID from authenticated user
                                    ->with('event')  
                                    ->latest()// Eager load the associated event
                                    ->get();
        
            // Pass the bookings to the view
            return view('attendee.purchases', compact('bookings'));
        }
        
     
        
}
