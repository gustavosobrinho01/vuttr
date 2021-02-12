<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'link',
        'description',
        'tags'
    ];

    protected $casts = [
        'tags' => 'array'
    ];

    public function setTagsAttribute($value)
    {
        $this->attributes['tags'] = json_encode($value);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
