<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'position',
        'jersey_number',
        'experience_years',
        'emergency_contact',
        'emergency_phone',
        'speed_rating',
        'endurance_rating',
        'is_active',
        'throws_notes'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'speed_rating' => 'integer',
        'endurance_rating' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skills()
    {
        return $this->hasMany(PlayerSkill::class, 'player_profile_id');
    }

    public function throws()
    {
        return $this->hasMany(ThrowType::class, 'player_profile_id');
    }

    public function getThrowLevel($throwType)
    {
        return $this->throws()
            ->where('type', $throwType->value)
            ->first()?->level;
    }

    public function getActiveThrows()
    {
        return $this->throws()
            ->where('level', '!=', 'null')
            ->get()
            ->map(function($throw) {
                return [
                    'type' => $throw->type,
                    'level' => $throw->level,
                    'confirmed_at' => $throw->confirmed_at
                ];
            });
    }
}