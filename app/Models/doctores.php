<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctores extends Model
{
    use HasFactory;

    protected $table = 'doctores';

    protected $fillable = [
        'user_id',
        'documento',
        'telefono',
        'especializacion',
        'estado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
