<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frame extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'image_path',
        'slots',
        'is_active',
        'download_count',
    ];

    protected function casts(): array
    {
        return [
            'slots' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
