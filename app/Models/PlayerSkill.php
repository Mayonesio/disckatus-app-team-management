<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_profile_id',
        'skill_id',
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

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function confirmedByUser()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}