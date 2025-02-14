<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $fillable = [
        'organizer_id', 'title', 'description', 'start_time',
        'end_time', 'location', 'total_tickets', 'ticket_price', 'status'
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function tickets()
    {
        return $this->hasMany(EventTicket::class);
    }
    
    public function bookings()
    {
        return $this->hasMany(EventBooking::class);
    }
}
