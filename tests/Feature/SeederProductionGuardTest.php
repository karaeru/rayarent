<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VehicleUnit;
use App\Models\Vehicle;
use Database\Seeders\UserSeeder;
use Database\Seeders\VehicleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederProductionGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_accounts_are_not_created_in_production(): void
    {
        $this->app['env'] = 'production';

        $this->seed(UserSeeder::class);

        $this->assertSame(0, User::count());
    }

    public function test_random_units_are_not_created_in_production_but_vehicles_and_packages_are(): void
    {
        $this->app['env'] = 'production';

        $this->seed(VehicleSeeder::class);

        $this->assertSame(28, Vehicle::count());
        $this->assertSame(0, VehicleUnit::count());
    }

    public function test_demo_accounts_and_units_are_created_outside_production(): void
    {
        $this->seed([UserSeeder::class, VehicleSeeder::class]);

        $this->assertSame(2, User::count());
        $this->assertGreaterThan(0, VehicleUnit::count());
    }
}
