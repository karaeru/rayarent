<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\VehicleUnit;
use Database\Seeders\DemoOrderSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\VehicleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoOrderSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([UserSeeder::class, VehicleSeeder::class, DemoOrderSeeder::class]);
    }

    public function test_it_creates_orders_in_every_status(): void
    {
        $this->assertSame(25, Order::count());

        foreach (['menunggu_konfirmasi', 'dikonfirmasi', 'sedang_disewa', 'selesai', 'dibatalkan'] as $status) {
            $this->assertTrue(Order::where('status', $status)->exists(), "Tidak ada pesanan berstatus {$status}");
        }
    }

    public function test_finished_orders_are_fully_paid_and_numbers_are_consistent(): void
    {
        Order::with('payments')->where('status', 'selesai')->get()->each(function (Order $o) {
            $this->assertSame(0, $o->due_amount, "{$o->code} belum lunas");
            $this->assertNotNull($o->returned_at);
        });

        $this->assertSame(25, Order::where('code', 'like', 'RR-%')->count());
    }

    public function test_active_orders_never_double_book_a_unit(): void
    {
        $aktif = Order::whereIn('status', VehicleUnit::AKTIF)->get();
        $bentrok = [];

        foreach ($aktif as $a) {
            foreach ($aktif as $b) {
                if ($a->id < $b->id && $a->vehicle_unit_id === $b->vehicle_unit_id
                    && $a->start_at < $b->end_at && $a->end_at > $b->start_at) {
                    $bentrok[] = "{$a->code} bentrok dengan {$b->code}";
                }
            }
        }

        $this->assertGreaterThan(0, $aktif->count());
        $this->assertSame([], $bentrok);
    }

    public function test_rented_orders_mark_their_units_as_rented_and_one_is_overdue(): void
    {
        Order::where('status', 'sedang_disewa')->get()->each(
            fn (Order $o) => $this->assertSame('disewa', $o->unit->status)
        );

        $this->assertTrue(Order::where('status', 'sedang_disewa')->whereNull('returned_at')->where('end_at', '<', now())->exists());
    }

    public function test_running_it_twice_does_not_duplicate_orders(): void
    {
        $this->seed(DemoOrderSeeder::class);

        $this->assertSame(25, Order::count());
    }

    public function test_waiting_orders_show_a_deadline_and_are_not_auto_cancelled_right_away(): void
    {
        $this->assertSame(0, Order::cancelExpired());
        $this->assertSame(3, Order::where('status', 'menunggu_konfirmasi')->count());
        $this->assertFalse(Order::where('created_at', '>', now())->exists(), 'Ada pesanan dengan tanggal pesan di masa depan');
        $this->assertSame(25, Order::whereNotNull('terms_accepted_at')->count());
    }
}
