<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoMapTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_uploading_a_photo_updates_the_map_and_photo_survives_a_reset(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $v = Vehicle::create(['name' => 'Yaris Manual', 'seat_label' => '4', 'capacity' => 4, 'is_active' => true]);

        $this->actingAs($admin)->post(route('admin.foto.simpan', $v), ['photo' => UploadedFile::fake()->image('yaris.jpg', 1200, 800)])
            ->assertSessionHas('ok');
        $path = $v->fresh()->photo;
        Storage::disk('local')->assertExists(Vehicle::PHOTO_MAP);

        // seperti migrate:fresh --seed: kendaraan dibuat ulang tanpa foto, id berubah
        $v->delete();
        $baru = Vehicle::create(['name' => 'Yaris Manual', 'seat_label' => '4', 'capacity' => 4, 'is_active' => true]);

        $this->artisan('armada:foto')->assertSuccessful();
        $this->assertSame($path, $baru->fresh()->photo);
    }

    public function test_missing_file_is_not_restored(): void
    {
        Storage::disk('local')->put(Vehicle::PHOTO_MAP, json_encode(['Brio Matic' => 'vehicles/tidak-ada.jpg']));
        $v = Vehicle::create(['name' => 'Brio Matic', 'seat_label' => '4', 'capacity' => 4, 'is_active' => true]);

        $this->assertSame(0, Vehicle::restorePhotoMap());
        $this->assertNull($v->fresh()->photo);
    }

    public function test_save_option_writes_map_from_database(): void
    {
        Storage::disk('local')->put('vehicles/a.jpg', 'x');
        Vehicle::create(['name' => 'Alphard', 'seat_label' => '6', 'capacity' => 6, 'is_active' => true, 'photo' => 'vehicles/a.jpg']);

        $this->artisan('armada:foto', ['--simpan' => true])->assertSuccessful();

        $this->assertSame(['Alphard' => 'vehicles/a.jpg'], json_decode(Storage::disk('local')->get(Vehicle::PHOTO_MAP), true));
    }
}
