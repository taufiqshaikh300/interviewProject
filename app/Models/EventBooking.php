<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class EventBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'event_id', 'tickets', 'total_price', 'payment_status',
        'card_holder_name', 'card_last_four', 'card_type', 'transaction_id'
    ];

    protected $casts = [
        'tickets' => 'array', // Automatically cast JSON field to array
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
