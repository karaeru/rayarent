<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    private function makeOrder(array $attributes = [], string $customerName = 'Pelanggan Uji'): Order
    {
        $vehicle = Vehicle::firstOrCreate(['name' => 'Avanza Test'], ['seat_label' => '7', 'capacity' => 7]);
        $unit = $vehicle->units()->firstOrCreate(['plate_number' => 'AD 1111 TS'], ['status' => 'tersedia']);
        $package = $vehicle->packages()->firstOrCreate(['service' => 'lepas_kunci', 'duration_hours' => 24], ['price' => 400000]);

        return Order::create(array_merge([
            'code' => 'RR-' . random_int(10000, 99999),
            'user_id' => User::factory()->create(['role' => 'pelanggan', 'name' => $customerName])->id,
            'vehicle_unit_id' => $unit->id,
            'rental_package_id' => $package->id,
            'quantity' => 1,
            'start_at' => now()->addDay(),
            'end_at' => now()->addDays(2),
            'total_price' => 400000,
            'pay_mode' => 'lunas',
            'status' => 'selesai',
        ], $attributes));
    }

    public function test_dashboard_renders_with_an_empty_database(): void
    {
        $this->actingAs($this->admin)->get(route('admin.dashboard'))->assertOk()->assertSee('Dashboard');
    }

    public function test_dashboard_lists_overdue_rentals_and_chart_counts_only_accepted_payments(): void
    {
        $late = $this->makeOrder(['status' => 'sedang_disewa', 'start_at' => now()->subDays(3), 'end_at' => now()->subHours(4)], 'Si Telat');
        $late->payments()->create(['amount' => 250000, 'status' => 'diterima', 'verified_at' => now()]);
        $late->payments()->create(['amount' => 999000, 'status' => 'menunggu']);

        $this->actingAs($this->admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Kendaraan telat kembali')
            ->assertSee('Si Telat')
            ->assertSee('Rp250.000')
            ->assertDontSee('Rp999.000</title>', false);
    }

    public function test_report_filters_by_status_and_date_and_ignores_cancelled_in_totals(): void
    {
        $selesai = $this->makeOrder(['code' => 'RR-A1', 'status' => 'selesai', 'start_at' => now()->subDays(10), 'end_at' => now()->subDays(9)]);
        $selesai->payments()->create(['amount' => 400000, 'status' => 'diterima', 'verified_at' => now()]);
        $this->makeOrder(['code' => 'RR-B2', 'status' => 'dibatalkan', 'start_at' => now()->subDays(10), 'end_at' => now()->subDays(9)]);
        $this->makeOrder(['code' => 'RR-C3', 'status' => 'selesai', 'start_at' => now()->subDays(60), 'end_at' => now()->subDays(59)]);

        $this->actingAs($this->admin)
            ->get(route('admin.laporan', ['dari' => now()->subDays(20)->toDateString(), 'sampai' => now()->toDateString()]))
            ->assertOk()
            ->assertSee('RR-A1')->assertSee('RR-B2')->assertDontSee('RR-C3')
            ->assertViewHas('jumlah', 1)
            ->assertViewHas('tagihan', 400000)
            ->assertViewHas('diterima', 400000);

        $this->actingAs($this->admin)->get(route('admin.laporan', ['status' => 'dibatalkan']))
            ->assertSee('RR-B2')->assertDontSee('RR-A1');
    }

    public function test_report_rejects_an_end_date_before_the_start_date(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.laporan', ['dari' => '2026-05-10', 'sampai' => '2026-05-01']))
            ->assertSessionHasErrors('sampai');
    }

    public function test_csv_export_has_headers_and_neutralises_spreadsheet_formulas(): void
    {
        $this->makeOrder(['code' => 'RR-CSV1'], '=HYPERLINK("http://evil")');

        $csv = $this->actingAs($this->admin)->get(route('admin.laporan.unduh'))->assertOk()->streamedContent();

        $this->assertStringContainsString('Kode,Pelanggan,Telepon', $csv);
        $this->assertStringContainsString('RR-CSV1', $csv);
        $this->assertStringContainsString("'=HYPERLINK", $csv);
    }

    public function test_customers_cannot_open_the_report(): void
    {
        $customer = User::factory()->create(['role' => 'pelanggan']);

        $this->actingAs($customer)->get(route('admin.laporan'))->assertForbidden();
        $this->actingAs($customer)->get(route('admin.laporan.unduh'))->assertForbidden();
    }
}
