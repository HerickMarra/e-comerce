<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'slug' => 'welcome-email',
                'name' => 'Bem-vindo (Novo Usuário)',
                'subject' => 'Bem-vindo à Sisters Esportes!',
                'content' => '<h1>Olá, {name}!</h1><p>Estamos muito felizes em ter você conosco. Sua conta foi criada com sucesso.</p><p>Aproveite nossas ofertas!</p>',
                'type' => 'system',
                'is_system' => true,
            ],
            [
                'slug' => 'new-order',
                'name' => 'Pedido Realizado',
                'subject' => 'Pedido Recebido - #{order_number}',
                'content' => '<h1>Olá, {name}!</h1><p>Recebemos o seu pedido <strong>#{order_number}</strong>.</p><p>O status atual é: {status}.</p><p>Assim que o pagamento for confirmado, iniciaremos o processo de envio.</p>',
                'type' => 'system',
                'is_system' => true,
            ],
            [
                'slug' => 'payment-confirmed',
                'name' => 'Pagamento Confirmado',
                'subject' => 'Pagamento Confirmado - Pedido #{order_number}',
                'content' => '<h1>Boas notícias, {name}!</h1><p>O pagamento do seu pedido <strong>#{order_number}</strong> foi confirmado.</p><p>Agora nossa equipe vai preparar tudo para o envio o mais rápido possível.</p>',
                'type' => 'system',
                'is_system' => true,
            ],
        ];

        foreach ($templates as $template) {
            \App\Models\EmailTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}
