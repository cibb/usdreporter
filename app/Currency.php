<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','url'
    ];

    /**
     * Autoload the currency information always.
     *
     * @var array
     */
    protected $with = [
        'prices',
    ];

    /**
     * Get prices
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prices()
    {
        return $this->hasMany(Price::class);
    }
}
