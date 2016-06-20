<?php

namespace App\Console\Commands;

use App\Conversation;
use App\User;
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

        if (is_null($messages)) {
            $this->info("No new messages");
            return true;
        }

        foreach ($messages as $message) {
            $user = User::firstOrNew(['phone' => $this->getNumber($message->number)]);

            if (!$user->exists) {
                $user->name = $message->notify;
                $user->save();
            }

            $conversation = $user->conversations()->create(['message' => $message->body->data, 'received' => true]);

            if ($response = $conversation->process()) {
                Whatsapi::send($response, function ($send) use ($user) {
                    $send->to($user->number);
                });
            }
        }

        return true;
    }

    /**
     * Get number from whatsapp username
     *
     * @param $cadena
     * @return mixed
     */
    private function getNumber($cadena)
    {
        $result = explode("@", $cadena);
        return $result[0];
    }
}
