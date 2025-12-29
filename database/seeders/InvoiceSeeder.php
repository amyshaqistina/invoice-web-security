<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Team;
use App\Models\Invoice;
use App\Models\Customer;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Invoice::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        $faker = Faker::create('ms_MY'); // Set Malay locale

        // Get all customer IDs
        $customerIds = Customer::pluck('id')->toArray();
        $team = Team::first();
        for ($i = 0; $i < 10; $i++) { // Create 10 fake estimates
            $invoice = Invoice::create([
                'customer_id' => $faker->randomElement($customerIds),
                'date' => $faker->date(),
                'due_date' => $faker->date(),
                'invoice_number' => strtoupper($faker->bothify('INV-####')), // Random invoice number like INV-1234
                'status' => $faker->randomElement(Invoice::liststatus()),
                'discount' => $faker->randomFloat(2, 0, 100), // Random discount between 0 - 100
                'notes' => $faker->sentence(), // Random sentence as notes
                'team_id' => $team->id,
            ]);


            $item = Item::inRandomOrder()->limit($faker->numberBetween(1, 5))->get();

            foreach($item as $items){
                $invoice->items()->attach($items->id, [
                    'quantity' => $faker->numberBetween(1, 10), // Random quantity between 1 - 10
                  
                ]);
            };

            

         
            


        }
    }
}
