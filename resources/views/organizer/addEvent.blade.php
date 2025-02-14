@extends('template.organizertemp')
@push('title')
Add Event
@endpush
@section('content')
 <!-- Success Message -->
 <div id="successMessage" class="alert alert-success mt-3 d-none">
    Event created successfully!
</div>

<div class="container mt-5">
    <div id="responseMessage" class="mt-3"></div>
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white text-center">
            <h4>Create New Event</h4>
        </div>
        <div class="card-body">
            <form id="eventForm" novalidate>
                @csrf
                <div class="mb-3">
                    <label for="title" class="form-label">Event Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                    <div class="invalid-feedback">Please enter an event title.</div>
                </div>
            
                <div class="mb-3">
                    <label for="description" class="form-label">Event Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    <div class="invalid-feedback">Please enter a description.</div>
                </div>
            
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="date" class="form-label">Event Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                        <div class="invalid-feedback">Please select a date.</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="start_time" class="form-label">Start Time</label>
                    <input type="time" class="form-control" id="start_time" name="start_time" required>
                </div>
                
                <div class="mb-3">
                    <label for="end_time" class="form-label">End Time</label>
                    <input type="time" class="form-control" id="end_time" name="end_time" required>
                </div>
                
            
                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location" required>
                    <div class="invalid-feedback">Please enter a location.</div>
                </div>
            
                <h5>Ticket Types</h5>
                <div id="ticketContainer">
                    <div class="ticket-type row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control ticket-type-input" name="ticket_types[]" placeholder="Ticket Type (e.g., VIP)" required>
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control ticket-price-input" name="ticket_prices[]" placeholder="Price (₹)" required min="0">
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control ticket-quantity-input" name="ticket_quantities[]" placeholder="Quantity" required min="1">
                        </div>
                        <div class="col-md-2 d-flex align-items-center">
                            <button type="button" class="btn btn-danger remove-ticket d-none">✖</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary mt-3" id="addTicket">+ Add Ticket Type</button>
            
                <button type="submit" class="btn btn-success w-100 mt-4">Create Event</button>
            
                <!-- Message Area -->
             
            </form>
            
            

           
        </div>
    </div>
</div>


@endsection

@section('script')

<script>
  $(document).ready(function () {
    function validateForm() {
        let isValid = true;
        let responseMessage = "";

        // Validate required fields
        $("#eventForm input, #eventForm textarea").each(function () {
            if ($(this).val().trim() === "") {
                $(this).addClass("is-invalid");
                isValid = false;
            } else {
                $(this).removeClass("is-invalid");
            }
        });

        // Validate date (must be today or in the future)
        let eventDate = new Date($("#date").val());
        let today = new Date();
        today.setHours(0, 0, 0, 0);

        if (eventDate < today) {
            $("#date").addClass("is-invalid");
            responseMessage += '<div class="alert alert-danger">Event date must be today or in the future.</div>';
            isValid = false;
        } else {
            $("#date").removeClass("is-invalid");
        }

        // Validate time (start time > current time if today, end time > start time)
        let startTime = $("#start_time").val();
        let endTime = $("#end_time").val();
        let currentTime = new Date().toTimeString().split(" ")[0]; // Get current time in HH:MM:SS format

        if (!startTime) {
            $("#start_time").addClass("is-invalid");
            responseMessage += '<div class="alert alert-danger">Start time is required.</div>';
            isValid = false;
        } else {
            $("#start_time").removeClass("is-invalid");
        }

        if (!endTime) {
            $("#end_time").addClass("is-invalid");
            responseMessage += '<div class="alert alert-danger">End time is required.</div>';
            isValid = false;
        } else {
            $("#end_time").removeClass("is-invalid");
        }

        if (eventDate.getTime() === today.getTime() && startTime < currentTime) {
            $("#start_time").addClass("is-invalid");
            responseMessage += '<div class="alert alert-danger">Start time must be greater than the current time.</div>';
            isValid = false;
        }

        if (endTime <= startTime) {
            $("#end_time").addClass("is-invalid");
            responseMessage += '<div class="alert alert-danger">End time must be greater than start time.</div>';
            isValid = false;
        }

        // Ensure at least one ticket type exists
        if ($(".ticket-type").length < 1) {
            responseMessage += '<div class="alert alert-danger">At least one ticket type is required.</div>';
            isValid = false;
        }

        // Show validation messages
        $("#responseMessage").html(responseMessage);
        return isValid;
    }

    // Add new ticket dynamically
    $("#addTicket").click(function () {
        let ticketHtml = `
            <div class="ticket-type row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control ticket-type-input" name="ticket_types[]" placeholder="Ticket Type (e.g., VIP)" required>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control ticket-price-input" name="ticket_prices[]" placeholder="Price (₹)" required min="0">
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control ticket-quantity-input" name="ticket_quantities[]" placeholder="Quantity" required min="1">
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    <button type="button" class="btn btn-danger remove-ticket">✖</button>
                </div>
            </div>`;
        $("#ticketContainer").append(ticketHtml);
        updateRemoveButtons();
    });

    // Remove a ticket type
    $(document).on("click", ".remove-ticket", function () {
        $(this).closest(".ticket-type").remove();
        updateRemoveButtons();
    });

    function updateRemoveButtons() {
        $(".remove-ticket").toggleClass("d-none", $(".ticket-type").length <= 1);
    }
    updateRemoveButtons(); // Initial check

    // Remove validation error on input change
    $("#eventForm").on("input change", "input, textarea", function () {
        if ($(this).val().trim() !== "") {
            $(this).removeClass("is-invalid");
        }
    });

    // Form submission with validation
    $("#eventForm").submit(function (event) {
        event.preventDefault(); // Prevent default form submission

        if (!validateForm()) return; // Stop submission if validation fails

        // AJAX submission
        $.ajax({
            url: "{{route('organizer.storeEvent')}}", // Update with actual Laravel route
            type: "POST",
            data: $("#eventForm").serialize(),
            dataType: "json",
            beforeSend: function () {
                $("#responseMessage").html('<div class="alert alert-info">Processing...</div>');
            },
            success: function (response) {
                if (response.success) {
                    $("#responseMessage").html('<div class="alert alert-success">Event created successfully!</div>');
                    $("#eventForm")[0].reset(); // Reset form
                    $("#ticketContainer").html(`
                        <div class="ticket-type row g-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control ticket-type-input" name="ticket_types[]" placeholder="Ticket Type (e.g., VIP)" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control ticket-price-input" name="ticket_prices[]" placeholder="Price (₹)" required min="0">
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control ticket-quantity-input" name="ticket_quantities[]" placeholder="Quantity" required min="1">
                            </div>
                            <div class="col-md-2 d-flex align-items-center">
                                <button type="button" class="btn btn-danger remove-ticket d-none">✖</button>
                            </div>
                        </div>
                    `);
                    updateRemoveButtons();
                } else {
                    $("#responseMessage").html('<div class="alert alert-danger">Error: ' + response.message + '</div>');
                }
            },
            error: function () {
                $("#responseMessage").html('<div class="alert alert-danger">Failed to create event. Please try again.</div>');
            }
        });
    });
});
</script>
@endsection