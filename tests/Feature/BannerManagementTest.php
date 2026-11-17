<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BannerManagementTest extends TestCase
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

    public function test_admin_can_access_banner_create_page_with_mobile_fields()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.banners.create'));

        $response->assertStatus(200);
        $response->assertSee('Imagem Desktop (Principal)');
        $response->assertSee('Imagem Mobile (Celulares)');
        $response->assertSee('mobile_image_file');
        $response->assertSee('mobile_image_url');
    }

    public function test_admin_can_create_banner_with_desktop_and_mobile_images()
    {
        Storage::fake('public');

        $desktopFile = UploadedFile::fake()->image('desktop.jpg', 1920, 1080);
        $mobileFile = UploadedFile::fake()->image('mobile.jpg', 800, 800);

        $response = $this->actingAs($this->admin)->post(route('admin.banners.store'), [
            'title' => 'Banner Teste Mobile e Desktop',
            'media_type' => 'image',
            'image_file' => $desktopFile,
            'mobile_image_file' => $mobileFile,
            'btn_text' => 'Comprar Agora',
            'btn_link' => '/produtos',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.banners.index'));

        $banner = Banner::first();
        $this->assertNotNull($banner);
        $this->assertEquals('Banner Teste Mobile e Desktop', $banner->title);
        $this->assertNotNull($banner->image_file);
        $this->assertNotNull($banner->mobile_image_file);
        $this->assertTrue($banner->hasMobileImage());

        Storage::disk('public')->assertExists($banner->image_file);
        Storage::disk('public')->assertExists($banner->mobile_image_file);
    }

    public function test_admin_can_update_banner_with_mobile_url()
    {
        $banner = Banner::create([
            'title' => 'Banner Existente',
            'media_type' => 'image',
            'image_url' => 'https://example.com/desktop.jpg',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.banners.update', $banner), [
            'title' => 'Banner Atualizado com Mobile URL',
            'media_type' => 'image',
            'image_url' => 'https://example.com/desktop.jpg',
            'mobile_image_url' => 'https://example.com/mobile.jpg',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.banners.index'));

        $banner->refresh();
        $this->assertEquals('https://example.com/mobile.jpg', $banner->mobile_image_url);
        $this->assertEquals('https://example.com/mobile.jpg', $banner->effective_mobile_image_url);
        $this->assertTrue($banner->hasMobileImage());
    }

    public function test_home_page_renders_mobile_picture_source_when_banner_has_mobile_image()
    {
        Banner::create([
            'title' => 'Banner Teste Home',
            'media_type' => 'image',
            'image_url' => 'https://example.com/desktop.jpg',
            'mobile_image_url' => 'https://example.com/mobile.jpg',
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('<picture', false);
        $response->assertSee('media="(max-width: 640px)"', false);
        $response->assertSee('https://example.com/mobile.jpg', false);
    }
}

