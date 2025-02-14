@extends('template.organizertemp')

@section('content')
<div class="container">
    <h2 class="mb-3">All Events</h2>

    <!-- Search Form -->
    <form action="{{  route('organizer.allPublicEvents')  }}" method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="title" class="form-control" placeholder="Search by Title" value="{{ request('title') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="location" class="form-control" placeholder="Search by Location" value="{{ request('location') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="start_time" class="form-control" value="{{ request('start_time') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="end_time" class="form-control" value="{{ request('end_time') }}">
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="{{ route('organizer.allPublicEvents') }}" class="btn btn-secondary">Clear</a>
            </div>
        </div>
    </form>

    <!-- Events Table -->
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Location</th>
             
                <th>Status</th> <!-- Showing only status -->
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
                        @if ($event->status == 'active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Cancelled</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        {{ $events->links('pagination::bootstrap-5') }}
    </nav>
</div>
@endsection
