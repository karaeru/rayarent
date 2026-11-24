<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutContactPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_about_and_contact(): void
    {
        config(['rayarent.alamat' => 'Jl. Slamet Riyadi 1, Solo', 'rayarent.whatsapp' => '6281234567890']);

        $this->get('/tentang')->assertOk()->assertSee('Lokasi')->assertSee('google.com/maps', false);
        $this->get('/kontak')->assertOk()->assertSee('Jl. Slamet Riyadi 1, Solo')->assertSee('wa.me/6281234567890', false);
    }

    public function test_contact_without_location_shows_hint(): void
    {
        config(['rayarent.alamat' => null, 'rayarent.maps' => null]);

        $this->get('/kontak')->assertOk()->assertSee('Lokasi belum diatur');
    }
}
