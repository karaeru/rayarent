<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_page_offers_a_cancel_link_back_to_vehicle_details(): void
    {
        $customer = User::factory()->create(['role' => 'pelanggan']);
        $vehicle = Vehicle::create([
            'name' => 'Agya Manual',
            'seat_label' => '3-4',
            'capacity' => 4,
        ]);
        $vehicle->packages()->create([
            'service' => 'lepas_kunci',
            'duration_hours' => 12,
            'price' => 250000,
        ]);

        $this->actingAs($customer)
            ->get(route('pelanggan.pesan', $vehicle))
            ->assertOk()
            ->assertSee('Batal')
            ->assertSee('href="'.route('pelanggan.kendaraan', $vehicle).'"', false);
    }
}
