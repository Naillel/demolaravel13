<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    protected $fillable = ['title', 'body'];

    /**
     * Relación polimórfica hacia la tabla likes.
     */
    public function likes()
    {
        // Esto buscará automáticamente las columnas likeable_id y likeable_type
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Registra el 'like' del usuario autenticado en la base de datos.
     */
    public function like()
    {
        $this->likes()->create([
            'user_id' => Auth::id() ?? auth()->user()->id
        ]);
    }
}