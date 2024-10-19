<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\tabItem;

class tabItemImages extends Model
{
    protected $connection = 'mysql';
    protected $primaryKey = 'name';
    public $timestamps = false;
    protected $keyType = 'string';

    protected $table = 'tabItem Images';

    public function item(){
        return $this->belongsTo(tabItem::class, 'parent', 'name');
    }
}
