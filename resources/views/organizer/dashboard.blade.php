@extends('template.organizertemp')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="fw-bold">Total Events Created</h4>
                </div>
                <div class="card-body text-center">
                    <h3 class="fw-bold">{{ $totalEvents }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="fw-bold">Total Tickets Sold</h4>
                </div>
                <div class="card-body text-center">
                    <h3 class="fw-bold">{{ $totalTicketsSold }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-warning text-white text-center">
                    <h4 class="fw-bold">Total Earnings</h4>
                </div>
                <div class="card-body text-center">
                    <h3 class="fw-bold">₹{{ number_format($totalEarnings, 2) }}</h3>
                </div>
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
