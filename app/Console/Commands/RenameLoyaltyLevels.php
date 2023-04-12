<?php

namespace App\Console\Commands;

use App\PremiumStatus;
use Illuminate\Console\Command;

class RenameLoyaltyLevels extends Command
{
    protected $signature = 'configure:rename_loyalty_levels';

    public $premiumStatusesData = [
        1 => [ 'name' => 'LEVEL_1'],
        2 => [ 'name' => 'LEVEL_2'],
        3 => [ 'name' => 'LEVEL_3'],
        4 => [ 'name' => 'LEVEL_4'],
        5 => [ 'name' => 'LEVEL_5'],
        6 => [ 'name' => 'LEVEL_1'],
        7 => [ 'name' => 'LEVEL_2'],
        8 => [ 'name' => 'LEVEL_3'],
        9 => [ 'name' => 'LEVEL_4'],
        10 => [ 'name' => 'LEVEL_5'],
        11 => [ 'name' => 'LEVEL_1'],
        12 => [ 'name' => 'LEVEL_2'],
        13 => [ 'name' => 'LEVEL_3'],
        14 => [ 'name' => 'LEVEL_4'],
        15 => [ 'name' => 'LEVEL_5'],
    ];

    public function updatePremiumStatuses()
    {
        foreach($this->premiumStatusesData as $id => $data) {
            $loyalty = PremiumStatus::where('id', $id)->firstOrFail();
            $loyalty->update($data);
        }
    }

    public function handle()
    {
        $this->updatePremiumStatuses();

        echo "\n--- done ---\n";
    }
}
