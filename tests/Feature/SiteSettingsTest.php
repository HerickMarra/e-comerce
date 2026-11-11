<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
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

    public function test_settings_page_renders_site_info_tab()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings.index'));

        $response->assertStatus(200);
        $response->assertSee('Informações do Site');
        $response->assertSee('WhatsApp de Atendimento');
        $response->assertSee('Instagram da Loja');
        $response->assertSee('CNPJ ou CPF da Empresa');
        $response->assertSee('Razão Social Oficial');
        $response->assertSee('Endereço da Sede');
    }

    public function test_admin_can_update_site_information_including_social_and_company_data()
    {
        $payload = [
            'store_name' => 'Denner Tênis Oficial',
            'store_company_name' => 'Denner Calçados e Artigos Esportivos LTDA',
            'store_document' => '12.345.678/0001-99',
            'store_state_registration' => '10.987.654-3',
            'store_phone' => '(61) 3333-4444',
            'store_whatsapp' => '(61) 98522-0100',
            'store_whatsapp_message' => 'Olá! Gostaria de tirar uma dúvida sobre um tênis.',
            'store_email' => 'suporte@dennertenis.com.br',
            'store_opening_hours' => 'Segunda a Sexta: 08h às 19h',
            'store_instagram' => '@dennertenis_oficial',
            'store_facebook' => 'https://facebook.com/dennertenis',
            'store_tiktok' => 'https://tiktok.com/@dennertenis',
            'store_youtube' => 'https://youtube.com/@dennertenis',
            'store_footer_description' => 'A maior loja de calçados de alta performance do Centro-Oeste.',
            'store_street' => 'Avenida Goiás',
            'store_number' => '1500',
            'store_complement' => 'Sala 302',
            'store_neighborhood' => 'Setor Central',
            'store_city' => 'Goiânia',
            'store_state' => 'GO',
            'store_zip' => '74000-000',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.settings.update'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('Denner Tênis Oficial', Setting::get('store_name'));
        $this->assertEquals('Denner Calçados e Artigos Esportivos LTDA', Setting::get('store_company_name'));
        $this->assertEquals('12.345.678/0001-99', Setting::get('store_document'));
        $this->assertEquals('(61) 98522-0100', Setting::get('store_whatsapp'));
        $this->assertEquals('@dennertenis_oficial', Setting::get('store_instagram'));
        $this->assertEquals('Avenida Goiás', Setting::get('store_street'));
        $this->assertEquals('Goiânia', Setting::get('store_city'));
    }

    public function test_storefront_footer_and_whatsapp_button_render_configured_information()
    {
        Setting::set('store_name', 'Denner Tênis Teste');
        Setting::set('store_company_name', 'Denner Tênis LTDA');
        Setting::set('store_document', '11.222.333/0001-44');
        Setting::set('store_whatsapp', '61985220100');
        Setting::set('store_whatsapp_message', 'Olá! Quero um cupom de desconto.');
        Setting::set('store_instagram', '@denner_tenis');
        Setting::set('store_street', 'Rua das Palmeiras');
        Setting::set('store_number', '100');
        Setting::set('store_city', 'Brasília');
        Setting::set('store_state', 'DF');
        Setting::set('store_zip', '70000-000');

        $response = $this->get('/');

        $response->assertStatus(200);

        // Check footer elements
        $response->assertSee('Denner Tênis LTDA');
        $response->assertSee('11.222.333/0001-44');
        $response->assertSee('Rua das Palmeiras, 100');
        $response->assertSee('Brasília/DF');
        $response->assertSee('https://instagram.com/denner_tenis');

        // Check floating WhatsApp button has configured phone & message
        $response->assertSee('https://wa.me/5561985220100?text=Ol%C3%A1%21+Quero+um+cupom+de+desconto.');
    }

    public function test_settings_page_renders_melhorenvio_balance_card_when_token_is_present()
    {
        Setting::set('melhorenvio_token', 'test-bearer-token', true);

        \Illuminate\Support\Facades\Http::fake([
            '*/api/v2/me/balance' => \Illuminate\Support\Facades\Http::response([
                'balance' => 9840.46,
                'reserved' => 0.0,
                'debts' => 30.0,
            ], 200),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.settings.index'));

        $response->assertStatus(200);
        $response->assertSee('Saldo da Melhor Carteira');
        $response->assertSee('Saldo Disponível');
    }

    public function test_melhorenvio_balance_api_endpoint_returns_json()
    {
        Setting::set('melhorenvio_token', 'test-bearer-token', true);

        \Illuminate\Support\Facades\Http::fake([
            '*/api/v2/me/balance' => \Illuminate\Support\Facades\Http::response([
                'balance' => 1500.50,
                'reserved' => 50.0,
                'debts' => 0.0,
            ], 200),
        ]);

        $response = $this->actingAs($this->admin)->getJson(route('admin.settings.melhorenvio-balance', ['refresh' => 1]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'balance' => 1500.50,
            'reserved' => 50.0,
            'debts' => 0.0,
        ]);
    }

    public function test_settings_page_renders_menu_and_footer_color_customizers()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings.index'));

        $response->assertStatus(200);
        $response->assertSee('Cores do Menu (Cabeçalho Superior)');
        $response->assertSee('Cor de Fundo do Menu');
        $response->assertSee('Cor dos Textos e Ícones do Menu');
        $response->assertSee('Cores do Rodapé (Footer)');
        $response->assertSee('Cor de Fundo do Rodapé');
        $response->assertSee('Cor dos Textos e Links do Rodapé');
    }

    public function test_admin_can_update_menu_and_footer_colors()
    {
        $payload = [
            'store_name' => 'Denner Tênis',
            'header_bg_color' => '#1e293b',
            'header_text_color' => '#ffffff',
            'footer_bg_color' => '#0f172a',
            'footer_text_color' => '#cbd5e1',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.settings.update'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('#1e293b', Setting::get('header_bg_color'));
        $this->assertEquals('#ffffff', Setting::get('header_text_color'));
        $this->assertEquals('#0f172a', Setting::get('footer_bg_color'));
        $this->assertEquals('#cbd5e1', Setting::get('footer_text_color'));
    }

    public function test_storefront_applies_custom_header_and_footer_colors()
    {
        Setting::set('header_bg_color', '#123456');
        Setting::set('header_text_color', '#abcdef');
        Setting::set('footer_bg_color', '#654321');
        Setting::set('footer_text_color', '#fedcba');

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('#123456');
        $response->assertSee('#abcdef');
        $response->assertSee('#654321');
        $response->assertSee('#fedcba');
    }

    public function test_storefront_and_admin_render_favicon_links()
    {
        $storefront = $this->get('/');
        $storefront->assertStatus(200);
        $storefront->assertSee('favicon.svg');
        $storefront->assertSee('favicon.ico');
        $storefront->assertSee('apple-touch-icon.png');

        $admin = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $admin->assertStatus(200);
        $admin->assertSee('favicon.svg');
        $admin->assertSee('favicon.ico');
    }
}
