<?php

namespace App\Console\Commands;

use App\Currency;
use App\Price;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Xaamin\Whatsapi\Facades\Laravel\Whatsapi;

class CurrencyCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:currency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check currency value and send a message if is requested';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('Getting currencies...');

        $currencies = Currency::all();

        $message = '';
        foreach($currencies as $currency)
        {
            $this->line('Working with '.$currency->name);
            $message .= $this->processCurrency($currency);
        }

        if( $message != '')
            $this->sendWhatsapp($message);
    }

    private function processCurrency($currency)
    {
        $result = file_get_contents($currency->url);

        if(!$result)
        {
            $this->error('I cant connect with '.$currency->url);
            return false;
        }

        $data = json_decode($result);

        if(!$data)
        {
            $this->error('I cant understand the '.$currency->name.' response');
            return false;
        }

        switch ($currency->name)
        {
            case 'USD':
            case 'EUR':
                if(!isset($data->sell) || !isset($data->buy))
                {
                    $this->error('Unexpected response: sell or buy not found');
                    break;
                }

                $data->sell = str_replace(array('.', ','), array('', '.'), $data->sell);

                if(!is_numeric($data->sell))
                {
                    $this->error('Sell property for '.$currency->name.' is not a number');
                    return false;
                }

                $sell = $currency->prices->last();

                if(!$sell)
                {
                    $price = $currency->prices()->create(['value' => $data->sell,'key'=>'sell','concept'=>'venta']);
                    return "Agregamos una nueva moneda: ".$currency->name." valorada en ".$price->value. "  - ";
                }

                if($sell AND $sell->value == $data->sell) {
                    $this->info($currency->name.' se mantiene igual');
                    break;
                }

                $price = $currency->prices()->create(['value' => $data->sell,'key'=>'sell','concept'=>'venta']);

                if( $sell->value > $price->value)
                {
                    return $currency->name." AUMENTÓ a $".$price->value. " - ";
                }

                return $currency->name." BAJÓ a $".$price->value. " - ";
                break;
            default:
                $this->error('Unexpected currency: '.$currency->name);
                break;
        }
    }

    private function sendWhatsapp($message)
    {
        $messages = Whatsapi::send($message, function($send)
        {
            $send->to(env('WHATSAPP_TO'));
        });

    }
}
