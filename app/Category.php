<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['title'];
    protected $table = "categories" ;
    
    
    public function rbts()
    {
        return $this->hasMany('App\Rbt');
    }
}
