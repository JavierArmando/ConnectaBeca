<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beca extends Model
{
    use HasFactory;

    /**
     * Relación many-to-many con User para favoritos
     */
    public function usuariosFavoritos()
    {
        return $this->belongsToMany(User::class, 'beca_user');
    }
}
