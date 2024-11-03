<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThrowType extends Model
{
    use HasFactory;

    protected $table = 'throws';

    protected $fillable = [
        'player_profile_id',
        'type',
        'level',
        'confirmed_by',
        'confirmed_at'
    ];

    protected $casts = [
        'confirmed_at' => 'datetime'
    ];

    public function playerProfile()
    {
        return $this->belongsTo(PlayerProfile::class);
    }

    public function confirmedByUser()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}