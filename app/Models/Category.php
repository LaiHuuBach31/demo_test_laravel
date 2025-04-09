<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];
    // protected $fillable = ['title', 'views'];

    public function categories()
    {
        return $this->hasMany(CategoryPost::class, 'category_id');
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'category_posts', 'category_id', 'post_id');
    }
}
