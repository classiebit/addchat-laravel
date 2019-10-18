<?php

use Illuminate\Database\Seeder;
use Classiebit\Addchat\Traits\Seedable;

class AddchatDatabaseSeeder extends Seeder
{
    use Seedable;

    protected $seedersPath = __DIR__.'/';

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->seed('AcSettingsTableSeeder');
    }
}
