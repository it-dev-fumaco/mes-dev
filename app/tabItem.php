<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\tabItemImages;

class tabItem extends Model
{
    protected $connection = 'mysql';
    protected $primaryKey = 'name';
    public $timestamps = false;
    protected $keyType = 'string';
    protected $table = 'tabItem';

    public function images(){
        return $this->hasMany(tabItemImages::class, 'parent', 'name');
    }

    public function defaultImage(){
        return $this->hasOne(tabItemImages::class, 'parent', 'name')->select('image_path', 'parent');
    }
}
