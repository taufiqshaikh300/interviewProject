@extends('template.organizertemp')
@section('content')
<div class="container">
    <h2 class="mb-4">Events List</h2>

    <form method="GET" action="{{ route('organizer.allEvents') }}" class="mb-3">
        <div class="row">
            <!-- Event Title -->
            <div class="col-md-3">
                Event Name :
                <input type="text" name="title" class="form-control" placeholder="Search by Event Title"
                       value="{{ request('title') }}">
            </div>
    
            <!-- Location -->
            <div class="col-md-3">
                Event Location
                <input type="text" name="location" class="form-control" placeholder="Search by Location"
                       value="{{ request('location') }}">
            </div>
    
            <!-- Start Date -->
            <div class="col-md-2">
               Start Date : <input type="date" name="start_time" class="form-control" value="{{ request('start_time') }}">
            </div>
    
            <!-- End Date -->
            <div class="col-md-2">
              End Date :  <input type="date" name="end_time" class="form-control" value="{{ request('end_time') }}">
            </div>
    
            <!-- Search Button -->
            <div class="col-md-2">
                <a href="{{ route('organizer.allEvents') }}" class="btn btn-sm btn-secondary w-50">Clear</a>
                <button type="submit" class="btn btn-sm btn-primary w-100 my-1">Search</button>
            </div>
        </div>
    </form>
    

    @if ($events->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Location</th>
                       
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($events as $key => $event)
                        <tr>
                            <td>{{ $loop->iteration + ($events->currentPage() - 1) * $events->perPage() }}</td>
                            <td>{{ $event->title }}</td>
                            <td>{{ date('d M Y, h:i A', strtotime($event->start_time)) }}</td>
                            <td>{{ date('d M Y, h:i A', strtotime($event->end_time)) }}</td>
                            <td>{{ $event->location }}</td>
                        
                            <td>
                                <a href="{{ route('organizer.viewEvent', ['id' => $event->id]) }}" class="btn btn-sm btn-primary">View</a>
                                <a href="{{ route('events.edit', ['id' => $event->id]) }}" class="btn btn-sm btn-warning">Edit</a>
                                @if ($event->status !== 'cancelled')
                                    <button class="btn btn-sm btn-danger cancel-event-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#cancelEventModal"
                                        data-event-id="{{ $event->id }}"
                                        data-event-title="{{ $event->title }}">
                                        Cancel Event
                                    </button>
                                   

                                
                                @else
                                    <span class="badge bg-secondary">Cancelled</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                
                
                
                
            </table>
        </div>



        <!-- Bootstrap 5 Pagination -->
        <nav>
            {{ $events->links('pagination::bootstrap-5') }}
        </nav>


        <!-- Cancel Event Modal -->
<div class="modal fade" id="cancelEventModal" tabindex="-1" aria-labelledby="cancelEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelEventModalLabel">Cancel Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="cancelEventMessage"></p>
                <p class="text-danger"><strong>Note:</strong> Once canceled, this event cannot be reactivated.</p>
            </div>
            <div class="modal-footer">
                <form id="cancelEventForm" method="POST" action="{{ route('organizer.cancel') }}">
                    @csrf
                    <input type="hidden" name="event_id" id="cancelEventId">
                    <button type="submit" class="btn btn-danger">Yes, Cancel Event</button>
                </form>
                
            </div>
        </div>
    </div>
</div>

    @else
        <div class="alert alert-warning">No events found.</div>
    @endif
</div>
@endsection

@section('script')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const cancelEventModal = document.getElementById('cancelEventModal');
        cancelEventModal.addEventListener('show.bs.modal', function (event) {
            let button = event.relatedTarget;
            let eventId = button.getAttribute('data-event-id');
            let eventTitle = button.getAttribute('data-event-title');
            
            // Set modal message
            document.getElementById('cancelEventMessage').innerText =
                `Are you sure you want to cancel the event "${eventTitle}"?`;

            // Set event ID in the form
            document.getElementById('cancelEventId').value = eventId;
        });
    });
</script>


@endsection