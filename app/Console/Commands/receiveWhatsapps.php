<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Xaamin\Whatsapi\Facades\Laravel\Whatsapi;

class receiveWhatsapps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get and process whatsapp messages';

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
        $messages = Whatsapi::getNewMessages();

        if(is_null($messages))
        {
            $this->info("No new messages");
            return true;
        }

        foreach($messages as $message)
        {
            print_r($message);
        }

        return true;
    }
}
