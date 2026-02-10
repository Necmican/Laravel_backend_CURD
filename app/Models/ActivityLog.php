<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HasFactory;

class ActivityLog extends Model
{
    
    protected $fillable = [
        'user_id',
        'action',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
