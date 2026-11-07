<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthPasswordVisibilityTest extends TestCase
{
    public function test_login_page_has_a_password_visibility_toggle(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('type="password"', false)
            ->assertSee('data-password-toggle', false)
            ->assertSee('aria-controls="password"', false)
            ->assertSee('Tampilkan password')
            ->assertSee('passwordInput.type = isVisible ? \'text\' : \'password\';', false);
    }

    public function test_registration_page_has_password_visibility_toggles_for_both_fields(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk()
            ->assertSee('aria-controls="password"', false)
            ->assertSee('aria-controls="password_confirmation"', false)
            ->assertSee('data-password-toggle', false);

        $this->assertSame(2, substr_count($response->getContent(), 'class="password-toggle"'));
    }
}
