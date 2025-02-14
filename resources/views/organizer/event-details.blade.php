@extends('template.organizertemp')

@section('content')
<div class="container mt-5">
    <!-- CSV Export Button -->
<div class="d-flex justify-content-end m-3">
    <a href="{{ route('organizer.event.exportCSV', $event->id) }}" class="btn btn-success">
        <i class="fas fa-file-csv"></i> Export to CSV
    </a>
</div>
    <!-- Event Banner -->
    <div class="card shadow-lg border-0">
        <div class="card-header bg-dark text-white text-center">
            <h2 class="fw-bold">{{ $event->title }}</h2>
            <p class="mb-0">{{ date('F j, Y', strtotime($event->start_time)) }} - {{ date('g:i A', strtotime($event->start_time)) }}</p>
        </div>
        <div class="card-body">
            <!-- Event Description -->
            <div class="row">
                <div class="col-md-12 text-center">
                    <h4 class="fw-bold">About the Event</h4>
                    <p class="text-muted">{{ $event->description }}</p>
                    <p><i class="fas fa-map-marker-alt text-danger"></i> <strong>Location:</strong> {{ $event->location }}</p>
                    <p><i class="fas fa-clock text-primary"></i> <strong>Time:</strong> {{ date('g:i A', strtotime($event->start_time)) }} - {{ date('g:i A', strtotime($event->end_time)) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ticket Selection -->
    <div class="card shadow-lg border-0 mt-4">
        <div class="card-header bg-primary text-white">
            <h4 class="fw-bold">Available Tickets</h4>
        </div>
        <div class="card-body">
            <form action="" method="POST">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">

                <table class="table table-hover">
                    <thead>
                        <tr class="bg-light">
                            <th>Ticket Type</th>
                            <th>Price (₹)</th>
                            <th>Total Tickets</th>
                            <th>Booked Tickets</th>
                            <th>Total Price (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandTotal = 0;
                        @endphp
                        @foreach($tickets as $ticket)
                        <tr>
                            <td class="fw-bold">{{ $ticket->ticket_type }}</td>
                            <td>₹{{ number_format($ticket->price, 2) }}</td>
                            <td>{{ $ticket->quantity }}</td>
                            <td>
                                @php
                                    $bookedQuantity = $bookedTicketDetails[$ticket->id] ?? 0;
                                @endphp
                                {{ $bookedQuantity }}
                            </td>
                            <td>
                                @php
                                    $totalPrice = $bookedQuantity * $ticket->price;
                                    $grandTotal += $totalPrice;
                                @endphp
                                ₹{{ number_format($totalPrice, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="text-end">
                    <h5 class="fw-bold">Grand Total: ₹{{ number_format($grandTotal, 2) }}</h5>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Who Booked Tickets -->
    <div class="card shadow-lg border-0 mt-4">
        <div class="card-header bg-success text-white">
            <h4 class="fw-bold">Users Who Booked Tickets</h4>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr class="bg-light">
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Ticket Type</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($eventBookings as $booking)
                        @php
                            $user = \App\Models\User::find($booking->user_id);
                            $tickets = json_decode($booking->tickets, true);
                        @endphp
                        @foreach($tickets as $ticket)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $ticket['ticket_type'] }}</td>
                                <td>{{ $ticket['quantity'] }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    body {
        background-color: #f8f9fa;
    }
    .card {
        border-radius: 12px;
    }
</style>
@endsection

@section('script')
<!-- Include any custom scripts if needed -->
@endsection
