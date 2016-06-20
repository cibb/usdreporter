<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Xaamin\Whatsapi\Facades\Laravel\Whatsapi;

class Conversation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message', 'received'
    ];

    /**
     * Autoload
     *
     * @var array
     */
    protected $with = [
        'metadata'
    ];

    /**
     * Get User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get metadata
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function metadata()
    {
        return $this->hasMany(Metadata::class);
    }

    /**
     * Process and send a response.
     *
     * @return string
     */
    public function process()
    {
        if (!$this->received) {
            return $this->send();
        }

        $response = false;
        if (strpos($this->message, "USD")) {
            $currency = Currency::where('name', 'USD')->first();
            $response = "La cotización del dolar es " . $currency->getLastPrice()->value . "\r\n";
            $this->metadata()->create(['name' => 'cotizationSent', 'value' => 'USD']);
        }

        if (strpos($this->message, "EUR")) {
            $currency = Currency::where('name', 'EUR')->first();
            $response .= "La cotización del euro es " . $currency->getLastPrice()->value . "\r\n";
            $this->metadata()->create(['name' => 'cotizationSent', 'value' => 'EUR']);
        }

        return $response;
    }

    /**
     * Send message
     *
     * @return mixed
     */
    private function send()
    {
        return Whatsapi::send($this->message, function ($send) {
            $send->to($this->user->number);
        });
    }
}
