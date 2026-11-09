<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\EnviaMaisService;
use App\Services\MelhorEnvioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            // Payment Gateway
            'asaas_url' => Setting::get('asaas_url', config('services.asaas.url')),
            'asaas_api_key' => Setting::get('asaas_api_key', config('services.asaas.key')),
            'asaas_webhook_token' => Setting::get('asaas_webhook_token'),

            // Shipping Provider & Melhor Envio
            'shipping_provider' => Setting::get('shipping_provider', 'melhorenvio'),
            'melhorenvio_token' => Setting::get('melhorenvio_token'),
            'melhorenvio_environment' => Setting::get('melhorenvio_environment', 'sandbox'),
            'melhorenvio_app_name' => Setting::get('melhorenvio_app_name', Setting::get('store_name', config('app.name'))),
            'melhorenvio_app_email' => Setting::get('melhorenvio_app_email', Setting::get('mail_from_address', 'contato@loja.com.br')),

            // EnviaMais
            'enviamais_api_key' => Setting::get('enviamais_api_key'),
            'enviamais_dev_mode' => Setting::get('enviamais_dev_mode', 'production'),

            // Visual Identity
            'store_name' => Setting::get('store_name', config('app.name')),
            'store_tagline' => Setting::get('store_tagline', 'Móveis de alto padrão para sua casa'),
            'store_logo' => Setting::get('store_logo'),
            'store_logo_size' => Setting::get('store_logo_size', '100'),
            'store_icon' => Setting::get('store_icon', 'chair'),
            'primary_color' => Setting::get('primary_color', '#10b981'),
            'secondary_color' => Setting::get('secondary_color', '#0f172a'),

            // Store Address & Pickup
            'store_street' => Setting::get('store_street'),
            'store_number' => Setting::get('store_number'),
            'store_complement' => Setting::get('store_complement', ''),
            'store_neighborhood' => Setting::get('store_neighborhood'),
            'store_city' => Setting::get('store_city'),
            'store_state' => Setting::get('store_state'),
            'store_zip' => Setting::get('store_zip'),
            'store_phone' => Setting::get('store_phone', ''),
            'store_document' => Setting::get('store_document', ''),
            'store_state_registration' => Setting::get('store_state_registration', ''),
            'shipping_additional_days' => Setting::get('shipping_additional_days', '0'),
            'shipping_additional_fee' => Setting::get('shipping_additional_fee', '0'),
            'enable_store_pickup' => Setting::get('enable_store_pickup', '1'),
            'store_pickup_name' => Setting::get('store_pickup_name', 'DENNER Tênis - Loja Física'),
            'store_pickup_hours' => Setting::get('store_pickup_hours', 'Segunda a Sexta: 09h às 18h | Sábado: 09h às 13h'),
            'store_pickup_timeframe' => Setting::get('store_pickup_timeframe', 'Pronto em até 2 horas úteis após confirmação do pagamento'),
            'store_pickup_instructions' => Setting::get('store_pickup_instructions', 'Apresente documento oficial com foto e o número do pedido no balcão.'),

            // Email (SMTP)
            'mail_mailer' => Setting::get('mail_mailer', config('mail.default')),
            'mail_host' => Setting::get('mail_host', config('mail.mailers.smtp.host')),
            'mail_port' => Setting::get('mail_port', config('mail.mailers.smtp.port')),
            'mail_username' => Setting::get('mail_username', config('mail.mailers.smtp.username')),
            'mail_password' => Setting::get('mail_password', ''),
            'mail_encryption' => Setting::get('mail_encryption', config('mail.mailers.smtp.encryption')),
            'mail_from_address' => Setting::get('mail_from_address', config('mail.from.address')),
            'mail_from_name' => Setting::get('mail_from_name', config('mail.from.name')),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function testMelhorEnvio(MelhorEnvioService $melhorEnvio)
    {
        $result = $melhorEnvio->validateToken();

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function testEnviaMais(EnviaMaisService $enviaMais)
    {
        $isValid = $enviaMais::class; // dummy call to ensure service injection works

        // Re-construct service to use current settings (could be unsaved if in a real app, but here we use saved ones)
        // Actually, the service constructor uses Setting::get, so it's fine.

        $success = $enviaMais->validateToken();

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Conexão com EnviaMais estabelecida com sucesso!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Falha ao conectar com EnviaMais. Verifique seu token e o ambiente selecionado.'
        ], 422);
    }

    public function update(Request $request)
    {
        // Payment Gateway
        if ($request->has('asaas_url')) {
            Setting::set('asaas_url', $request->asaas_url);
        }

        if ($request->has('asaas_api_key')) {
            if ($request->asaas_api_key && !str_contains($request->asaas_api_key, '****')) {
                Setting::set('asaas_api_key', $request->asaas_api_key, true);
            }
        }

        // Webhook token
        if ($request->has('asaas_webhook_token') && $request->asaas_webhook_token && !str_contains($request->asaas_webhook_token, '****')) {
            Setting::set('asaas_webhook_token', $request->asaas_webhook_token, true);
        }

        // Visual Identity
        Setting::set('store_name', $request->store_name ?? '');
        Setting::set('store_tagline', $request->store_tagline ?? '');
        Setting::set('store_logo_size', $request->store_logo_size ?? '100');
        Setting::set('store_icon', $request->store_icon ?? 'chair');
        Setting::set('primary_color', $request->primary_color ?? '#10b981');
        Setting::set('secondary_color', $request->secondary_color ?? '#0f172a');

        // Store Address & Pickup
        Setting::set('store_street', $request->store_street ?? '');
        Setting::set('store_number', $request->store_number ?? '');
        Setting::set('store_complement', $request->store_complement ?? '');
        Setting::set('store_neighborhood', $request->store_neighborhood ?? '');
        Setting::set('store_city', $request->store_city ?? '');
        Setting::set('store_state', $request->store_state ?? '');
        Setting::set('store_zip', $request->store_zip ?? '');
        Setting::set('store_phone', $request->store_phone ?? '');
        Setting::set('store_document', $request->store_document ?? '');
        Setting::set('store_state_registration', $request->store_state_registration ?? '');
        Setting::set('shipping_additional_days', $request->shipping_additional_days ?? '0');
        Setting::set('shipping_additional_fee', $request->shipping_additional_fee ?? '0');
        Setting::set('enable_store_pickup', $request->enable_store_pickup ? '1' : '0');
        Setting::set('store_pickup_name', $request->store_pickup_name ?? '');
        Setting::set('store_pickup_hours', $request->store_pickup_hours ?? '');
        Setting::set('store_pickup_timeframe', $request->store_pickup_timeframe ?? '');
        Setting::set('store_pickup_instructions', $request->store_pickup_instructions ?? '');

        // Shipping Settings (Melhor Envio & EnviaMais)
        if ($request->has('shipping_provider')) {
            Setting::set('shipping_provider', $request->shipping_provider);
        }

        if ($request->has('melhorenvio_token')) {
            if ($request->melhorenvio_token && !str_contains($request->melhorenvio_token, '****')) {
                Setting::set('melhorenvio_token', trim($request->melhorenvio_token), true);
            }
        }
        if ($request->has('melhorenvio_environment')) {
            Setting::set('melhorenvio_environment', $request->melhorenvio_environment);
        }
        if ($request->has('melhorenvio_app_name')) {
            Setting::set('melhorenvio_app_name', $request->melhorenvio_app_name);
        }
        if ($request->has('melhorenvio_app_email')) {
            Setting::set('melhorenvio_app_email', $request->melhorenvio_app_email);
        }

        // EnviaMais Shipping
        if ($request->has('enviamais_api_key') && $request->enviamais_api_key && !str_contains($request->enviamais_api_key, '****')) {
            Setting::set('enviamais_api_key', $request->enviamais_api_key, true);
        }
        Setting::set('enviamais_dev_mode', $request->enviamais_dev_mode ?? 'production');

        // Email (SMTP)
        Setting::set('mail_mailer', $request->mail_mailer ?? 'smtp');
        Setting::set('mail_host', $request->mail_host ?? '');
        Setting::set('mail_port', $request->mail_port ?? '587');
        Setting::set('mail_username', $request->mail_username ?? '');
        if ($request->has('mail_password') && $request->mail_password && !str_contains($request->mail_password, '****')) {
            Setting::set('mail_password', $request->mail_password, true); // encrypt
        }
        Setting::set('mail_encryption', $request->mail_encryption ?? 'tls');
        Setting::set('mail_from_address', $request->mail_from_address ?? '');
        Setting::set('mail_from_name', $request->mail_from_name ?? '');

        // Logo upload and removal
        if ($request->remove_logo === '1') {
            $oldLogo = Setting::get('store_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }
            Setting::set('store_logo', null);
        } elseif ($request->hasFile('store_logo') && $request->file('store_logo')->isValid()) {
            // Delete old logo
            $oldLogo = Setting::get('store_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('store_logo')->store('logos', 'public');
            Setting::set('store_logo', $path);
        }

        return back()->with('success', 'Configurações atualizadas com sucesso!');
    }
}
