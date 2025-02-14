@extends('template.attendeetemp')

@section('content')
<div class="container mt-5">
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
                            <th>Available</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->ticket_type }}</td>
                            <td>₹{{ number_format($ticket->price, 2) }}</td>
                            <td>{{ $ticket->available_quantity }}</td> <!-- Available quantity -->
                
                            <td>
                                @if($ticket->available_quantity > 0)
                                    <!-- Show input field if tickets are available -->
                                    <input 
                                        type="number" 
                                        name="ticket_quantity[{{ $ticket->id }}]" 
                                        class="form-control ticket-input" 
                                        min="0" 
                                        max="{{ $ticket->available_quantity }}" 
                                        value="0"
                                    >
                                @else
                                    <!-- Show "Sold Out" sticker if no tickets are available -->
                                    <span class="sold-out">Sold Out</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <!-- Add the "Sold Out" styling -->
                <style>
                    .sold-out {
                        color: red;
                        font-weight: bold;
                        background-color: #f8d7da;
                        padding: 5px;
                        border-radius: 3px;
                    }
                </style>
                

                <div class="text-end">
                    <button type="button" id="bookNowBtn" class="btn btn-success fw-bold px-4">Book Now</button>

                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payment Details Modal -->
<!-- Payment Details Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Enter Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <div class="mb-3">
                        <label for="card_holder_name" class="form-label">Card Holder Name</label>
                        <input type="text" class="form-control" id="card_holder_name" name="card_holder_name" required>
                        <input type="hidden" name="event_id" value="{{ $event->id }}">

                    </div>
                    <div class="mb-3">
                        <label for="card_number" class="form-label">Card Number</label>
                        <input type="text" class="form-control" id="card_number" name="card_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="expiry_date" class="form-label">Expiry Date</label>
                        <input type="text" class="form-control" id="expiry_date" name="expiry_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="cvv" class="form-label">CVV</label>
                        <input type="text" class="form-control" id="cvv" name="cvv" required>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Total Amount</label>
                        <input type="text" class="form-control" id="amount" name="amount" readonly>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Pay Now</button>
                    </div>
                </form>
            </div>
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
<script>
    $('#bookNowBtn').on('click', function() {
     let totalAmount = 0;
     let tickets = [];
     let isValid = true;
 
     // Loop through each ticket input and calculate the total price
     $('.ticket-input').each(function() {
         const ticketId = $(this).attr('name').replace('ticket_quantity[', '').replace(']', '');
         const quantity = parseInt($(this).val()) || 0;
 
         if (quantity > 0) {
             const ticketRow = $(this).closest('tr');
             const ticketPrice = parseFloat(ticketRow.find('td:nth-child(2)').text().replace('₹', '').trim());
             const availableQuantity = parseInt(ticketRow.find('td:nth-child(3)').text().trim());
 
             // Validation: Ensure quantity does not exceed available tickets
             if (quantity > availableQuantity) {
                 alert('Quantity cannot exceed available tickets for ' + ticketRow.find('td:nth-child(1)').text());
                 isValid = false;
                 return; // Stop further execution
             }
 
             totalAmount += ticketPrice * quantity;
 
             tickets.push({
                 ticket_id: ticketId,
                 quantity: quantity
             });
         }
     });
 
     if (isValid && totalAmount > 0) {
         // Populate the modal with the total amount
         $('#amount').val(totalAmount.toFixed(2));
 
         // Store the tickets data in a hidden input to be sent when submitting the payment form
         $('#paymentForm').append('<input type="hidden" name="tickets" value=\'' + JSON.stringify(tickets) + '\'>' );
 
         // Open the modal
         $('#paymentModal').modal('show');
     } else if (isValid) {
         alert('Please select at least one ticket.');
     }
 });
 
 $('#paymentForm').on('submit', function(event) {
     event.preventDefault();
 
     const formData = $(this).serialize();
     console.log(formData);
 
     // Send the form data to the server (including payment and ticket data)
     $.ajax({
         url: '{{ route("event.bookTickets") }}', // Route to book the tickets
         method: 'POST',
         data: formData,
         headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
         success: function(data) {
             if (data.success) {
                 // Redirect to the success page
                 window.location.href = '/event/paymentsuccess'; 
             } else {
                 alert('There was an issue with the payment.');
             }
         },
         error: function(xhr, status, error) {
             console.error('Error:', error);
             alert('There was an error processing your payment.');
         }
     });
 });
 </script>
 

@endsection
