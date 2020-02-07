<?php

use App\Models\Provider;
use Illuminate\Database\Seeder;

class ProvidersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $providers = config('providers');

        collect($providers)->map(function ($provider, $id) {
            Provider::create([
                'id'   => $id,
                'name' => $provider
            ]);
        });
    }
}
