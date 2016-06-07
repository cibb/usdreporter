<?php

namespace App\Console\Commands;

use App\Currency;
use Illuminate\Console\Command;

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
     * @return void
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

        foreach($currencies as $currency)
        {
            $this->line('Working with '.$currency->name);
            $this->processCurrency($currency);
        }


        $euro = json_decode(file_get_contents('http://www.bancogalicia.com/cotizacion/cotizar?currencyId=98&quoteType=SU&quoteId=999'));
        $this->line('Información obtenida!');

        //$this->console->error('Hey!');
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
                if(!isset($data['sell']) || !isset($data['buy']))
                {
                    $this->error('Unexpected response: sell or buy not found');
                }

                $lastSell = $currency->prices(function($q){
                    return $q->where('key','sell')->orWhere('key','buy')->orderBy('created_at','desc')->first();
                });
                break;
            default:
                $this->error('Unexpected currency: '.$currency->name);
                break;
        }
    }
}
