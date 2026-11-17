<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoginCustomizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Super Admin');
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Super Admin');
    }

    public function test_login_page_renders_with_default_or_configured_image()
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('Sua próxima missão começa aqui.');

        // Configure custom image and texts
        Setting::set('auth_login_image', 'https://images.unsplash.com/photo-custom-test');
        Setting::set('auth_login_title', 'Nova Missão Sport Life');
        Setting::set('auth_login_badge', 'Militar & Tático');

        $responseUpdated = $this->get(route('login'));
        $responseUpdated->assertStatus(200);
        $responseUpdated->assertSee('https://images.unsplash.com/photo-custom-test');
        $responseUpdated->assertSee('Nova Missão Sport Life');
        $responseUpdated->assertSee('Militar & Tático');
    }

    public function test_admin_can_update_login_image_via_url_in_settings()
    {
        $payload = [
            'auth_login_image_url' => 'https://images.unsplash.com/custom-tactical-login.jpg',
            'auth_login_title' => 'Força e Resistência',
            'auth_login_badge' => 'Airsoft Brasil',
            'auth_login_subtitle' => 'Os melhores equipamentos e réplicas para combate.',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.settings.update'), $payload);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('https://images.unsplash.com/custom-tactical-login.jpg', Setting::get('auth_login_image'));
        $this->assertEquals('Força e Resistência', Setting::get('auth_login_title'));
        $this->assertEquals('Airsoft Brasil', Setting::get('auth_login_badge'));
    }

    public function test_admin_can_upload_login_image_file_in_settings()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('login-banner.jpg', 1920, 1080);

        $payload = [
            'auth_login_image' => $file,
            'auth_login_title' => 'Imagem Carregada do Computador',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.settings.update'), $payload);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $savedPath = Setting::get('auth_login_image');
        $this->assertNotNull($savedPath);
        $this->assertStringStartsWith('auth/', $savedPath);
        Storage::disk('public')->assertExists($savedPath);
    }

    public function test_admin_can_update_login_image_in_appearance()
    {
        $payload = [
            'auth_login_image_url' => 'https://images.unsplash.com/appearance-login.jpg',
            'auth_login_title' => 'Aparência Atualizada',
            'auth_login_badge' => 'Elite Squad',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.appearance.update'), $payload);
        $response->assertRedirect(route('admin.appearance.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('https://images.unsplash.com/appearance-login.jpg', Setting::get('auth_login_image'));
        $this->assertEquals('Aparência Atualizada', Setting::get('auth_login_title'));
    }
}
