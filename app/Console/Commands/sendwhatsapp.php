<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Xaamin\Whatsapi\Facades\Laravel\Whatsapi;

class sendwhatsapp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:send {number : Number (or numbers) to send message} {message?* : Message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send whatsapp message to number';

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
        $message = count($this->argument("message"))>0 ? $this->argument("message") : $this->ask('What is the message?');
        $number = $this->argument("number");

        $send = Whatsapi::send($message, function($send) use ($number)
        {
            $send->to($number);
        });

        dd($send);
    }
}
