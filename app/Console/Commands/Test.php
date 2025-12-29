<?php

namespace App\Console\Commands;

use App\Mail\TestMail;
use App\Models\RecurringInvoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->test();
        // Mail::to('test@example.com')->queue(new TestMail());
    }


    function test(){
        RecurringInvoice::generate_invoice(2);
    }
}
