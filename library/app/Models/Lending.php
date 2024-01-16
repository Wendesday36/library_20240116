<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lending extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'copy_id',
        'start',
        'end',
        'extension',
        'notice'
    ];

    protected function setKeysForSaveQuery($query)
    {
        $query
            ->where('user_id', '=', $this->getAttribute('user_id'))
            ->where('copy_id', '=', $this->getAttribute('copy_id'))
            ->where('start', '=', $this->getAttribute('start'));
        return $query;
    }

    public function user()
        //kapcsolat, osztály, ott hogy hívják, itt hogy hívják
    {    return $this->belongsTo(User::class, 'id', 'user_id');   }

    public function userHas()
        //kapcsolat, osztály, ott hogy hívják, itt hogy hívják
    {    return $this->hasOne(User::class, 'id', 'user_id');   }

}
