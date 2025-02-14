@extends('template.attendeetemp')

@section('content')

<div class="container mt-5">
    <h2 class="text-center mb-4">My Purchases</h2>

    @foreach ($bookings as $booking)
        <div class="card shadow-lg mb-4">
            <div class="card-body">
                <!-- Event Details Section -->
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="card-title font-weight-bold">{{ $booking->event->title }}</h5>
                    <span class="badge bg-primary">{{ ucfirst($booking->event->type) }}</span>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Event Date:</strong> {{ \Carbon\Carbon::parse($booking->event->start_time)->format('F j, Y') }}</p>
                        <p><strong>Event Time:</strong> {{ \Carbon\Carbon::parse($booking->event->start_time)->format('h:i A') }}</p>
                        <p><strong>Venue:</strong> {{ $booking->event->location }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Description:</strong></p>
                        <p>{{ \Str::limit($booking->event->description, 150, '...') }}</p>
                    </div>
                </div>

                <p class="card-text">
                    <strong>Booking ID:</strong> {{ $booking->id }}
                </p>

                <p class="card-text">
                    <strong>Total Price:</strong> ₹{{ number_format($booking->total_price, 2) }}
                </p>

                <!-- Tickets Section -->
                <h6 class="font-weight-bold mt-3">Tickets:</h6>
                <ul class="list-group">
                    @foreach (json_decode($booking->tickets) as $ticket)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <span>
                                    <strong>({{ $ticket->ticket_type }})</strong> 
                                </span>
                                <span class="text-success">
                                    ₹{{ number_format($ticket->ticket_price, 2) }}
                                </span>
                            </div>
                            <p>Quantity: <strong>{{ $ticket->quantity }}</strong></p>
                        </li>
                    @endforeach
                </ul>

                <p class="mt-3">
                    <strong>Status:</strong> 
                    <span class="badge bg-{{ $booking->payment_status == 'paid' ? 'success' : 'danger' }}">
                        {{ ucfirst($booking->payment_status) }}
                    </span>
                </p>
            </div>
        </div>
    @endforeach
</div>

@endsection
