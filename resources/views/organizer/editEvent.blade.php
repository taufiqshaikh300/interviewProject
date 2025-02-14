@extends('template.organizertemp')

@push('title')
Edit Event
@endpush

@section('content')

<div class="container mt-5">
    <div id="responseMessage" class="mt-3"></div>
    <div class="card shadow-lg">
        <div class="card-header bg-warning text-white text-center">
            <h4>Edit Event</h4>
        </div>
        <div class="card-body">
            @php
                $eventTimeParts = explode(' ', $event->start_time);
                $eventDate = $eventTimeParts[0] ?? '';
                $timeRange = explode('-', $eventTimeParts[1] ?? '');
                $startTime = $timeRange[0] ?? '';

                $eventTimePartss = explode(' ', $event->end_time);
                $timeRange = explode('-', $eventTimePartss[1] ?? '');
                $endTime = $timeRange[0] ?? '';
            @endphp

            <form id="editEventForm" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" name="event_id" value="{{ $event->id }}">

                <div class="mb-3">
                    <label for="title" class="form-label">Event Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ $event->title }}" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Event Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required>{{ $event->description }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="date" class="form-label">Event Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ $eventDate }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="start_time" class="form-label">Start Time</label>
                    <input type="time" class="form-control" id="start_time" name="start_time" value="{{ $startTime }}" required>
                </div>

                <div class="mb-3">
                    <label for="end_time" class="form-label">End Time</label>
                    <input type="time" class="form-control" id="end_time" name="end_time" value="{{ $endTime }}" required>
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location" value="{{ $event->location }}" required>
                </div>

                <h5>Ticket Types</h5>
                <div id="ticketContainer">
                    @foreach($event->tickets as $ticket)
                    <div class="ticket-type row g-3">


                        <div class="ticket-type row g-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control ticket-type-input" name="ticket_types[]"  value="{{ $ticket->ticket_type }}" placeholder="Ticket Type" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control ticket-price-input" name="ticket_prices[]"  value="{{ $ticket->price }}" placeholder="Price (₹)" required min="1">
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control ticket-quantity-input" name="ticket_quantities[]" value="{{ $ticket->quantity }}" placeholder="Quantity" required min="1">
                            </div>
                            <div class="col-md-2 d-flex align-items-center">
                                <button type="button" class="btn btn-danger remove-ticket">✖</button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <button type="button" class="btn btn-secondary mt-3" id="addTicket">+ Add Ticket Type</button>
                <button type="submit" class="btn btn-primary w-100 mt-4">Update Event</button>
               
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')

<script>
    $(document).ready(function () {
        $("#addTicket").click(function () {
            $("#ticketContainer").append(`
                <div class="ticket-type row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control ticket-type-input" name="ticket_types[]" placeholder="Ticket Type" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control ticket-price-input" name="ticket_prices[]" placeholder="Price (₹)" required min="1">
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control ticket-quantity-input" name="ticket_quantities[]" placeholder="Quantity" required min="1">
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        <button type="button" class="btn btn-danger remove-ticket">✖</button>
                    </div>
                </div>`);
        });
    
        $(document).on("click", ".remove-ticket", function () {
            if ($(".ticket-type").length > 1) {
                $(this).closest(".ticket-type").remove();
            } else {
                alert("At least one ticket type must be present.");
            }
        });
    
        $("#editEventForm").submit(function (event) {
            event.preventDefault();
            $("#responseMessage").html("");
    
            let title = $("#title").val().trim();
            let description = $("#description").val().trim();
            let date = $("#date").val();
            let startTime = $("#start_time").val();
            let endTime = $("#end_time").val();
            let location = $("#location").val().trim();
    
            let ticketTypes = $(".ticket-type-input").map(function () { return $(this).val().trim(); }).get();
            let ticketPrices = $(".ticket-price-input").map(function () { return parseFloat($(this).val()); }).get();
            let ticketQuantities = $(".ticket-quantity-input").map(function () { return parseInt($(this).val()); }).get();
    
            console.log("Ticket Types:", ticketTypes);
            console.log("Ticket Prices:", ticketPrices);
            console.log("Ticket Quantities:", ticketQuantities);
    
            // Validation
            if (!title || !description || !date || !startTime || !endTime || !location) {
                $("#responseMessage").html('<div class="alert alert-danger">All fields are required.</div>');
                return;
            }
    
            if (endTime <= startTime) {
                $("#responseMessage").html('<div class="alert alert-danger">End time must be greater than start time.</div>');
                return;
            }
    
            // Ensure at least one valid ticket exists
            let hasValidTicket = false;
            for (let i = 0; i < ticketTypes.length; i++) {
                if (ticketTypes[i] !== "" && !isNaN(ticketPrices[i]) && ticketPrices[i] > 0 && !isNaN(ticketQuantities[i]) && ticketQuantities[i] > 0) {
                    hasValidTicket = true;
                    break;
                }
            }
    
            if (!hasValidTicket) {
                $("#responseMessage").html('<div class="alert alert-danger">At least one valid ticket type is required with a price and quantity greater than zero.</div>');
                return;
            }
    
            // Submit AJAX Request
            $.ajax({
                url: "{{ route('events.update', $event->id) }}",
                type: "PUT",
                data: $("#editEventForm").serialize(),
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        $("#responseMessage").html('<div class="alert alert-success">Event updated successfully!</div>');
                    } else {
                        $("#responseMessage").html('<div class="alert alert-danger">Error updating event.</div>');
                    }
                }
            });
        });
    });
    </script>
    
    

@endsection
