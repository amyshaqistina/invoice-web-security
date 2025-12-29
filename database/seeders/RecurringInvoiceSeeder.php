<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Team;
use App\Models\Invoice;
use App\Models\Customer;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use App\Models\RecurringInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RecurringInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        RecurringInvoice::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Faker::create('ms_MY'); //  id_ID
        $frequencies = ['weekly', 'monthly', 'yearly', 'custom'];
        $statuses = Invoice::liststatus();
        $customerIds = Customer::pluck('id')->toArray();
        $team = Team::first();
        foreach (range(1, 10) as $index) { // Create 10 fake recurring invoices
            $frequency = $faker->randomElement($frequencies);
            $startDate = $faker->date();
          

            $ri = RecurringInvoice::create([
                'customer_id' => $faker->randomElement($customerIds),
                'start_date' => $startDate,
                'next_invoice_date' => $startDate,
                'invoice_number' => strtoupper($faker->bothify('RINV-####')), // e.g., RINV-1234
                'frequency' => $frequency,
                'limit_by' => $faker->numberBetween(1, 10), // Example cron if 'custom'
                'status' => $faker->randomElement($statuses),
                'notes' => $faker->optional()->sentence(),
                'team_id' => $team->id,
            ]);

             $item = Item::inRandomOrder()->limit($faker->numberBetween(1, 5))->get();

            foreach($item as $items){
                $ri->items()->attach($items->id, [
                    'quantity' => $faker->numberBetween(1, 10), // Random quantity between 1 - 10
                  
                ]);
            };

        }
    }
}
