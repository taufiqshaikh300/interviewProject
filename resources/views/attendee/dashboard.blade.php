@extends('template.attendeetemp')

@section('content')


@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-center fw-bold text-primary">🎟️ Upcoming Events</h2>

    <!-- Search Form -->
    <form method="GET" action="{{ route('attendee.dashboard') }}" class="mb-4">
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="🔍 Search by Name or Location" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('attendee.dashboard') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </div>
    </form>

    <!-- Events Listing -->
    <div class="row">
        @forelse($events as $event)
        <div class="col-md-4">
            <div class="card mb-4 shadow-lg border-0 event-card">
                <div class="event-header text-white text-center py-3">
                    <h5 class="mb-0 fw-bold">{{ $event->title }}</h5>
                </div>
                <div class="card-body text-center">
                    <p class="card-text text-muted">
                        📅 <strong>{{ date('d M Y', strtotime($event->start_time)) }}</strong> <br>
                        ⏰ <strong>{{ date('h:i A', strtotime($event->start_time)) }} - {{ date('h:i A', strtotime($event->end_time)) }}</strong> <br>
                        📍 <strong>{{ $event->location }}</strong>
                    </p>
                    <p class="card-text">{{ Str::limit($event->description, 80, '...') }}</p>
                    <a href="{{ route('attendee.viewEvent', ['id'=> $event->id]) }}" class="btn btn-primary fw-bold px-3">View Details</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center">
            <p class="text-muted">No events found.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $events->links('pagination::bootstrap-5') }}
    </div>
</div>

<style>
    body {
        background-color: #f8f9fa;
    }
    .event-card {
        border-radius: 12px;
        transition: transform 0.3s ease-in-out;
        overflow: hidden;
    }
    .event-card:hover {
        transform: scale(1.03);
        box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
    }
    .event-header {
        background: linear-gradient(135deg, #007bff, #6610f2);
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    
</style>
@endsection

