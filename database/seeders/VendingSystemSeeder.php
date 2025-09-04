<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VendingMachine;
use App\Models\Drug;
use App\Models\VendingMachineInventory;
use Carbon\Carbon;

class VendingSystemSeeder extends Seeder
{
    public function run()
    {
        // Create sample drugs
        $drugs = [
            [
                'name' => 'Paracetamol',
                'generic_name' => 'Acetaminophen',
                'manufacturer' => 'Generic Pharmaceuticals',
                'category' => 'Pain Relief',
                'dosage_form' => 'Tablet',
                'strength' => '500mg',
                'price' => 2.50,
            ],
            [
                'name' => 'Ibuprofen',
                'generic_name' => 'Ibuprofen',
                'manufacturer' => 'Generic Pharmaceuticals',
                'category' => 'Pain Relief',
                'dosage_form' => 'Tablet',
                'strength' => '400mg',
                'price' => 3.00,
            ],
            [
                'name' => 'Acetaminophen',
                'generic_name' => 'Paracetamol',
                'manufacturer' => 'MedyCorp',
                'category' => 'Pain Relief',
                'dosage_form' => 'Tablet',
                'strength' => '325mg',
                'price' => 2.25,
            ],
            [
                'name' => 'Naproxen',
                'generic_name' => 'Naproxen Sodium',
                'manufacturer' => 'HealthPharma',
                'category' => 'Pain Relief',
                'dosage_form' => 'Tablet',
                'strength' => '220mg',
                'price' => 4.50,
            ],
            [
                'name' => 'Benadryl',
                'generic_name' => 'Diphenhydramine',
                'manufacturer' => 'AllergyMed',
                'category' => 'Antihistamine',
                'dosage_form' => 'Tablet',
                'strength' => '25mg',
                'price' => 5.00,
            ],
        ];

        foreach ($drugs as $drugData) {
            Drug::create($drugData);
        }

        // Create sample vending machines
        $machines = [
            [
                'machine_id' => 'VM001',
                'machine_num' => 1,
                'machine_name' => 'Main Hospital Lobby',
                'machine_lat' => 40.7128,
                'machine_long' => -74.0060,
                'machine_type' => 'Standard',
                'machine_max_rows' => 5,
                'machine_max_column' => 3,
                'machine_is_active' => true,
                'machine_ip' => '192.168.1.100',
                'doa' => Carbon::now()->subDays(30),
                'machine_last_ping' => Carbon::now()->subMinutes(2),
            ],
            [
                'machine_id' => 'VM002',
                'machine_num' => 2,
                'machine_name' => 'Emergency Department',
                'machine_lat' => 40.7589,
                'machine_long' => -73.9851,
                'machine_type' => 'Compact',
                'machine_max_rows' => 3,
                'machine_max_column' => 3,
                'machine_is_active' => true,
                'machine_ip' => '192.168.1.101',
                'doa' => Carbon::now()->subDays(15),
                'machine_last_ping' => Carbon::now()->subMinutes(1),
            ],
        ];

        foreach ($machines as $machineData) {
            VendingMachine::create($machineData);
        }

        // Create sample inventory
        $machine1 = VendingMachine::where('machine_num', 1)->first();
        $paracetamol = Drug::where('name', 'Paracetamol')->first();
        $ibuprofen = Drug::where('name', 'Ibuprofen')->first();
        
        if ($machine1 && $paracetamol) {
            VendingMachineInventory::create([
                'vending_machine_id' => $machine1->id,
                'drug_id' => $paracetamol->id,
                'slot_row' => 1,
                'slot_column' => 1,
                'stock_quantity' => 33,
                'threshold_quantity' => 10,
                'expiry_date' => Carbon::now()->addMonths(12),
                'batch_number' => 'BTH123456',
                'last_restocked_at' => Carbon::now()->subDays(5),
            ]);
        }

        if ($machine1 && $ibuprofen) {
            VendingMachineInventory::create([
                'vending_machine_id' => $machine1->id,
                'drug_id' => $ibuprofen->id,
                'slot_row' => 1,
                'slot_column' => 2,
                'stock_quantity' => 25,
                'threshold_quantity' => 10,
                'expiry_date' => Carbon::now()->addMonths(18),
                'batch_number' => 'BTH789012',
                'last_restocked_at' => Carbon::now()->subDays(3),
            ]);
        }

        $this->command->info('Vending system sample data seeded successfully!');
    }
}
