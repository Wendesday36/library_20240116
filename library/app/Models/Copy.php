<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Copy extends Model
{
    use HasFactory;

    protected  $primaryKey = 'copy_id';

    protected $fillable = [
        'hardcovered',
        'status',
        'publication',
        'book_id'
    ];

    public function book()
        //kapcsolat, osztály, ott hogy hívják, itt hogy hívják
    {    return $this->belongsTo(Book::class, 'book_id', 'book_id');   }

    public function lending()
        //kapcsolat, osztály, ott hogy hívják, itt hogy hívják
    {    return $this->hasMany(Lending::class, 'copy_id', 'copy_id');   }
}
