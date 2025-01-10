<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'database_name',
    ];

    /**
     * Relación uno a muchos con usuarios.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}

