<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Ulasan pelanggan: hanya pemilik pesanan selesai, sekali per pesanan, admin bisa menyembunyikan. */
class ReviewTest extends TestCase
{
    use RefreshDatabase;

    private function order(User $user, string $status = 'selesai'): Order
    {
        $v = Vehicle::firstOrCreate(['name' => 'Innova Zenix'], ['seat_label' => '7', 'capacity' => 7, 'is_active' => true]);
        $unit = $v->units()->firstOrCreate(['plate_number' => 'AB 1000 RA'], ['status' => 'tersedia']);
        $pkg = $v->packages()->firstOrCreate(['service' => 'lepas_kunci', 'duration_hours' => 24], ['price' => 900000]);

        return Order::create([
            'code' => 'RR-'.random_int(1000, 9999), 'user_id' => $user->id, 'vehicle_unit_id' => $unit->id, 'rental_package_id' => $pkg->id,
            'quantity' => 1, 'start_at' => now()->subDays(3), 'end_at' => now()->subDays(2), 'returned_at' => now()->subDays(2),
            'total_price' => 900000, 'pay_mode' => 'lunas', 'status' => $status,
        ]);
    }

    public function test_customer_can_review_a_finished_order_once(): void
    {
        $budi = User::factory()->create(['role' => 'pelanggan', 'name' => 'Budi Santoso']);
        $o = $this->order($budi);

        $this->actingAs($budi)->get(route('pelanggan.pesanan.show', $o))->assertOk()->assertSee('Kirim ulasan');
        $this->post(route('pelanggan.ulasan.simpan', $o), ['rating' => 5, 'comment' => 'Mobil bersih.'])
            ->assertRedirect(route('pelanggan.pesanan.show', $o));
        $this->assertDatabaseHas('reviews', ['order_id' => $o->id, 'rating' => 5, 'vehicle_id' => $o->unit->vehicle_id]);

        $this->post(route('pelanggan.ulasan.simpan', $o), ['rating' => 1])->assertSessionHas('error');
        $this->assertSame(1, Review::count());
        $this->get(route('pelanggan.pesanan.show', $o))->assertSee('Ulasan Anda')->assertDontSee('Kirim ulasan');
    }

    public function test_unfinished_or_other_peoples_orders_cannot_be_reviewed(): void
    {
        $budi = User::factory()->create(['role' => 'pelanggan']);
        $siti = User::factory()->create(['role' => 'pelanggan']);

        $this->actingAs($budi)->post(route('pelanggan.ulasan.simpan', $this->order($budi, 'sedang_disewa')), ['rating' => 5])->assertForbidden();
        $this->actingAs($budi)->post(route('pelanggan.ulasan.simpan', $this->order($siti)), ['rating' => 5])->assertNotFound();
        $this->actingAs($budi)->post(route('pelanggan.ulasan.simpan', $this->order($budi)), ['rating' => 9])->assertSessionHasErrors('rating');
        $this->assertSame(0, Review::count());
    }

    public function test_visible_reviews_show_on_landing_and_vehicle_page_with_short_names(): void
    {
        $budi = User::factory()->create(['role' => 'pelanggan', 'name' => 'Budi Santoso']);
        $o = $this->order($budi);
        Review::create(['order_id' => $o->id, 'user_id' => $budi->id, 'vehicle_id' => $o->unit->vehicle_id, 'rating' => 4, 'comment' => 'Serah terima tepat waktu.']);

        $this->get('/')->assertOk()->assertSee('Kata pelanggan setelah sewa')->assertSee('Serah terima tepat waktu.')->assertSee('Budi S.')->assertDontSee('Budi Santoso');
        $this->get('/armada/innova-zenix')->assertOk()->assertSee('Ulasan penyewa')->assertSee('AggregateRating');
    }

    public function test_admin_can_hide_a_review_from_public_pages(): void
    {
        $budi = User::factory()->create(['role' => 'pelanggan']);
        $o = $this->order($budi);
        $r = Review::create(['order_id' => $o->id, 'user_id' => $budi->id, 'vehicle_id' => $o->unit->vehicle_id, 'rating' => 1, 'comment' => 'Komentar tidak pantas']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('admin.ulasan'))->assertOk()->assertSee('Komentar tidak pantas')->assertSee('data-confirm=', false);
        $this->actingAs($admin)->patch(route('admin.ulasan.toggle', $r))->assertRedirect();
        $this->assertFalse($r->fresh()->is_visible);

        auth()->logout();
        $this->get('/')->assertOk()->assertDontSee('Komentar tidak pantas');
        $this->actingAs($budi)->patch(route('admin.ulasan.toggle', $r))->assertForbidden();
    }

    public function test_public_pages_open_with_the_blue_hero(): void
    {
        $v = Vehicle::create(['name' => 'Brio Matic', 'seat_label' => '4', 'capacity' => 4, 'is_active' => true]);
        $v->packages()->create(['service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 300000]);

        foreach (['/tentang', '/kontak', '/syarat-ketentuan', '/armada/'.$v->slug] as $url) {
            $html = $this->get($url)->assertOk()->assertSee('rr-under-header', false)->getContent();
            $this->assertSame(1, preg_match_all('/<h1\b/i', $html), "{$url}: tepat satu h1");
        }
    }
}
