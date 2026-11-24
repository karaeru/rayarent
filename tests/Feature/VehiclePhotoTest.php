<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VehiclePhotoTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    private function vehicle(string $name = 'Avanza Test'): Vehicle
    {
        return Vehicle::create(['name' => $name, 'seat_label' => '7', 'capacity' => 7]);
    }

    private function upload(Vehicle $vehicle, UploadedFile $file, array $extra = [])
    {
        return $this->actingAs($this->admin)->post(route('admin.foto.simpan', $vehicle), ['photo' => $file] + $extra);
    }

    public function test_upload_is_reencoded_as_jpeg_resized_and_served(): void
    {
        $vehicle = $this->vehicle();

        $this->upload($vehicle, UploadedFile::fake()->image('mobil.png', 3200, 2000))->assertSessionHas('ok');

        $path = $vehicle->fresh()->photo;
        $this->assertStringStartsWith('vehicles/', $path);
        $this->assertStringEndsWith('.jpg', $path);
        Storage::disk('local')->assertExists($path);

        [$width] = getimagesizefromstring(Storage::disk('local')->get($path));
        $this->assertSame(1600, $width);

        $this->get(route('foto.kendaraan', $vehicle))->assertOk()->assertHeader('Content-Type', 'image/jpeg');
    }

    public function test_invalid_files_are_rejected(): void
    {
        $vehicle = $this->vehicle();

        $this->upload($vehicle, UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'))->assertSessionHasErrors('photo');
        $this->upload($vehicle, UploadedFile::fake()->image('kecil.jpg', 300, 200))->assertSessionHasErrors('photo');
        $this->upload($vehicle, UploadedFile::fake()->createWithContent('x.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'))
            ->assertSessionHasErrors('photo');
        $this->upload($vehicle, UploadedFile::fake()->image('besar.jpg', 1200, 800)->size(6000))->assertSessionHasErrors('photo');

        $this->assertNull($vehicle->fresh()->photo);
    }

    public function test_replacing_a_photo_deletes_the_old_file(): void
    {
        $vehicle = $this->vehicle();
        $this->upload($vehicle, UploadedFile::fake()->image('a.jpg', 1200, 800));
        $old = $vehicle->fresh()->photo;

        $this->upload($vehicle, UploadedFile::fake()->image('b.jpg', 1200, 800));

        $this->assertNotSame($old, $vehicle->fresh()->photo);
        Storage::disk('local')->assertMissing($old);
        Storage::disk('local')->assertExists($vehicle->fresh()->photo);
    }

    public function test_deleting_a_photo_removes_the_file_and_the_photo_route_returns_404(): void
    {
        $vehicle = $this->vehicle();
        $this->upload($vehicle, UploadedFile::fake()->image('a.jpg', 1200, 800));
        $path = $vehicle->fresh()->photo;

        $this->actingAs($this->admin)->delete(route('admin.foto.hapus', $vehicle))->assertSessionHas('ok');

        $this->assertNull($vehicle->fresh()->photo);
        Storage::disk('local')->assertMissing($path);
        $this->get(route('foto.kendaraan', $vehicle))->assertNotFound();
    }

    public function test_photo_can_be_shared_with_matic_and_manual_variants_only(): void
    {
        $matic = $this->vehicle('Brio Matic');
        $manual = $this->vehicle('Brio Manual');
        $lain = $this->vehicle('Brio Sport');
        $yaris = $this->vehicle('Yaris Matic');

        $this->upload($matic, UploadedFile::fake()->image('brio.jpg', 1200, 800), ['apply_variants' => 1]);

        $this->assertNotNull($manual->fresh()->photo);
        $this->assertNull($lain->fresh()->photo);
        $this->assertNull($yaris->fresh()->photo);
        // masing-masing memegang file sendiri
        $this->assertNotSame($matic->fresh()->photo, $manual->fresh()->photo);
        Storage::disk('local')->assertExists($manual->fresh()->photo);

        // menghapus foto satu varian tidak merusak varian lain
        $this->actingAs($this->admin)->delete(route('admin.foto.hapus', $matic));
        Storage::disk('local')->assertExists($manual->fresh()->photo);
    }

    public function test_without_the_variant_option_only_the_chosen_vehicle_changes(): void
    {
        $matic = $this->vehicle('Brio Matic');
        $manual = $this->vehicle('Brio Manual');

        $this->upload($matic, UploadedFile::fake()->image('brio.jpg', 1200, 800));

        $this->assertNotNull($matic->fresh()->photo);
        $this->assertNull($manual->fresh()->photo);
    }

    public function test_customers_cannot_upload_or_delete_photos(): void
    {
        $vehicle = $this->vehicle();
        $customer = User::factory()->create(['role' => 'pelanggan']);

        $this->actingAs($customer)->post(route('admin.foto.simpan', $vehicle), ['photo' => UploadedFile::fake()->image('a.jpg', 1200, 800)])->assertForbidden();
        $this->actingAs($customer)->delete(route('admin.foto.hapus', $vehicle))->assertForbidden();
    }

    public function test_catalog_shows_the_photo_when_present_and_the_icon_otherwise(): void
    {
        $customer = User::factory()->create(['role' => 'pelanggan']);
        $dengan = $this->vehicle('Mobil Berfoto');
        $tanpa = $this->vehicle('Mobil Polos');
        foreach ([$dengan, $tanpa] as $v) {
            $v->packages()->create(['service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 400000]);
        }
        $this->upload($dengan, UploadedFile::fake()->image('a.jpg', 1200, 800));

        $this->actingAs($customer)->get(route('pelanggan.katalog'))
            ->assertOk()
            ->assertSee('/foto/' . $dengan->id, false)
            ->assertDontSee('/foto/' . $tanpa->id, false);
    }

    public function test_admin_form_shows_the_variant_checkbox(): void
    {
        $this->vehicle('Brio Manual');
        $matic = $this->vehicle('Brio Matic');

        $this->actingAs($this->admin)->get(route('admin.kendaraan.ubah', $matic))
            ->assertOk()
            ->assertSee('Pakai juga untuk varian lain: Brio Manual');
    }
}
