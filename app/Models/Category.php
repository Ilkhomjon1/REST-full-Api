<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
      'name',
      'description',
      'keywords',
      'slug',
    ];

    public function products(){
      return $this->belongsToMany('App\Models\Product', 'category_products')->withPivot('id');
    }
}
