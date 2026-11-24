<?php

namespace Tests\Feature;

use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicVehiclePageTest extends TestCase
{
    use RefreshDatabase;

    private function vehicle(array $attr = []): Vehicle
    {
        $v = Vehicle::create($attr + ['name' => 'Innova Zenix', 'seat_label' => '7', 'capacity' => 7, 'is_active' => true]);
        $v->packages()->create(['service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 900000]);

        return $v;
    }

    public function test_guest_can_open_vehicle_page_by_slug(): void
    {
        $this->vehicle();

        $this->get('/armada/innova-zenix')
            ->assertOk()
            ->assertSee('Innova Zenix')
            ->assertSee('Rp900.000')
            ->assertSee('application/ld+json', false);
    }

    public function test_landing_links_to_vehicle_page(): void
    {
        $this->vehicle();

        $this->get('/')->assertOk()->assertSee(route('armada', 'innova-zenix'));
    }

    public function test_inactive_or_unknown_vehicle_returns_404(): void
    {
        $this->vehicle(['is_active' => false]);

        $this->get('/armada/innova-zenix')->assertNotFound();
        $this->get('/armada/tidak-ada')->assertNotFound();
    }
}
