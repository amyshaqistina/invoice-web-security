<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Customer;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Payment::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Faker::create('ms_MY'); //  id_ID

        $customers = Customer::pluck('id')->toArray(); // Get all customer IDs
        $invoices = Invoice::pluck('id')->toArray(); // Get all invoice IDs

        $paymentModes = ['cash', 'check', 'credit_card', 'bank_transfer'];
        $team = Team::first();
        foreach (range(1, 10) as $index) { // Generate 10 fake payments
            Payment::create([
                'date' => $faker->date(),
                'payment_number' => strtoupper($faker->bothify('PAY-#######')), // e.g., PAY-1234567
                'customer_id' => $faker->randomElement($customers),
                'invoice_id' => $faker->optional()->randomElement($invoices), // Nullable invoice
                'amount' => $faker->randomFloat(2, 50, 5000), // Random amount between 50 and 5000
                'payment_mode' => $faker->optional()->randomElement($paymentModes), // Nullable payment mode
                'notes' => $faker->optional()->sentence(),
                'team_id' => $team->id,
            ]);
        }
    }
}
