<?php

namespace App\Console\Commands;

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
        //Dolar: http://www.bancogalicia.com/cotizacion/cotizar?currencyId=02&quoteType=SU&quoteId=999
        //Euro: http://www.bancogalicia.com/cotizacion/cotizar?currencyId=98&quoteType=SU&quoteId=999
        $this->console->error('Hey!');
    }
}
