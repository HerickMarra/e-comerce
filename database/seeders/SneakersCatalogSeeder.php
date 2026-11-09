<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SneakersCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Garantir que as categorias das principais marcas existem com descrições e status ativo
        $brands = [
            [
                'name' => 'Nike',
                'slug' => 'nike',
                'description' => 'Tênis de alta performance Nike com tecnologias ZoomX, ReactX, Air Zoom e placas de carbono Flyplate.',
                'image_path' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'Adidas',
                'slug' => 'adidas',
                'description' => 'Tênis de corrida Adidas com amortecimento Light BOOST, Lightstrike Pro e hastes EnergyRods.',
                'image_path' => 'https://images.unsplash.com/photo-1587563871167-1ee9c731aefb?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'Olympikus',
                'slug' => 'olympikus',
                'description' => 'A marca brasileira líder em inovação com a consagrada Família Corre: Corre 3, Corre Grafeno e Corre Vento.',
                'image_path' => 'https://images.unsplash.com/photo-1579338559194-a162d19bf842?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'Asics',
                'slug' => 'asics',
                'description' => 'Referência mundial em estabilidade e conforto supremo com PureGEL, FF BLAST PLUS ECO e 4D Guidance System.',
                'image_path' => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'Mizuno',
                'slug' => 'mizuno',
                'description' => 'Engenharia japonesa de ponta com placas Mizuno Wave e espumas ultra responsivas Mizuno Enerzy.',
                'image_path' => 'https://images.unsplash.com/photo-1514989940723-e8e51635b782?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'Puma',
                'slug' => 'puma',
                'description' => 'Velocidade e propulsão com nitrogênio infuso NITRO Foam, placas INNOPLATE e borracha PUMAGRIP.',
                'image_path' => 'https://images.unsplash.com/photo-1605348532760-6753d2c43329?auto=format&fit=crop&q=80&w=800',
            ],
        ];

        $categoryMap = [];
        foreach ($brands as $b) {
            $category = Category::updateOrCreate(
                ['slug' => $b['slug']],
                [
                    'name' => $b['name'],
                    'description' => $b['description'],
                    'image_path' => $b['image_path'],
                ]
            );
            $categoryMap[$b['name']] = $category;
        }

        // 2. Catálogo completo de tênis para as marcas
        $products = [
            // ================= NIKE =================
            [
                'name' => 'Tênis Nike Air Zoom Pegasus 40',
                'brand' => 'Nike',
                'model_line' => 'Pegasus / Treino Diário',
                'gender' => 'Unissex',
                'description' => "O cavalo de batalha com asas está de volta. O Nike Pegasus 40 traz o retorno elástico da espuma React combinado com duas unidades Zoom Air (no antepé e calcanhar) para uma passada suave e energizada.\n\nCabedal de camada única em mesh altamente respirável que se molda perfeitamente ao formato do pé, oferecendo conforto quilômetro após quilômetro.",
                'price' => 749.90,
                'compare_price' => 999.90,
                'stock' => 24,
                'sizes' => ['38', '39', '40', '41', '42', '43', '44'],
                'material' => 'Mesh estruturado de camada única respirável com ajuste dinâmico no mediopé',
                'sole_material' => 'Entressola Nike React com 2 unidades Zoom Air e solado Waffle de borracha',
                'technical_specifications' => "Tipo de Pisada: Neutra\nDrop da Entressola: 10 mm (calcanhar: 33 mm / antepé: 23 mm)\nPeso Aproximado: 288 g (tamanho 41)\nIndicação: Rodagens diárias, treinos de ritmo e provas de 5k a maratona\nFechamento: Cadarço com ilhós reforçados\nGarantia: Oficial Nike do Brasil contra defeitos de fabricação",
                'weight' => 0.820,
                'height' => 12.0,
                'width' => 22.0,
                'length' => 34.0,
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#DC2626', 'name' => 'University Red'],
                    ['hex' => '#000000', 'name' => 'Black / White'],
                    ['hex' => '#2563EB', 'name' => 'Royal Blue'],
                ],
            ],
            [
                'name' => 'Tênis Nike ZoomX Vaporfly 3',
                'brand' => 'Nike',
                'model_line' => 'Vaporfly / Competição Maratona',
                'gender' => 'Unissex',
                'description' => "Persiga tempos recordes com o Nike ZoomX Vaporfly 3. Projetado para quem busca cruzar a linha de chegada mais rápido do que nunca, conta com a espuma ZoomX mais responsiva e leve do planeta, além da placa inteiriça de fibra de carbono Flyplate.\n\nO cabedal Flyknit ultra ventilado abraça o pé com contenção firme e respirabilidade máxima.",
                'price' => 1799.90,
                'compare_price' => 2199.90,
                'stock' => 10,
                'sizes' => ['39', '40', '41', '42', '43', '44'],
                'material' => 'Cabedal Flyknit super leve com fios específicos para suporte e ventilação',
                'sole_material' => 'Espuma ZoomX premium com placa de fibra de carbono Flyplate integral',
                'technical_specifications' => "Tipo de Pisada: Neutra / Performance Máxima\nDrop da Entressola: 8 mm (calcanhar: 40 mm / antepé: 32 mm)\nPeso Aproximado: 198 g (tamanho 41)\nIndicação: Dias de prova, meia maratona (21k) e maratona (42k)\nPlaca de Propulsão: Fibra de carbono inteiriça Flyplate\nGarantia: Oficial Nike",
                'weight' => 0.750,
                'height' => 12.0,
                'width' => 22.0,
                'length' => 34.0,
                'image' => 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#84CC16', 'name' => 'Volt / Electric Green'],
                    ['hex' => '#FFFFFF', 'name' => 'Pure Platinum'],
                    ['hex' => '#F97316', 'name' => 'Total Orange'],
                ],
            ],
            [
                'name' => 'Tênis Nike Invincible 3',
                'brand' => 'Nike',
                'model_line' => 'Invincible / Máximo Amortecimento',
                'gender' => 'Masculino',
                'description' => "Com amortecimento máximo para dar suporte a cada quilômetro, o Invincible 3 oferece o nível mais alto de conforto sob os pés para que você possa correr hoje, amanhã e depois. Equipado com uma base alargada de espuma ZoomX ultramacia e elástica.",
                'price' => 1199.90,
                'compare_price' => 1499.90,
                'stock' => 15,
                'sizes' => ['38', '39', '40', '41', '42', '43'],
                'material' => 'Flyknit avançado com zonas de elasticidade onde o pé mais precisa',
                'sole_material' => 'Entressola total de ZoomX ultra espessa com solado waffle antiderrapante',
                'technical_specifications' => "Tipo de Pisada: Neutra com base estável\nDrop da Entressola: 9 mm (calcanhar: 40 mm / antepé: 31 mm)\nPeso Aproximado: 310 g (tamanho 41)\nIndicação: Treinos regenerativos, longões e proteção articular\nGarantia: Contra defeitos de fabricação",
                'weight' => 0.880,
                'height' => 13.0,
                'width' => 23.0,
                'length' => 34.0,
                'image' => 'https://images.unsplash.com/photo-1552346154-21d32810aba3?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#0F172A', 'name' => 'Obsidian Navy'],
                    ['hex' => '#FFFFFF', 'name' => 'Summit White'],
                ],
            ],
            [
                'name' => 'Tênis Nike Downshifter 13',
                'brand' => 'Nike',
                'model_line' => 'Downshifter / Treino e Corrida',
                'gender' => 'Unissex',
                'description' => "Esteja você iniciando sua jornada nas corridas ou buscando um tênis versátil para seus treinos diários e caminhadas na esteira, o Nike Downshifter 13 oferece passada suave e amortecida com excelente relação custo-benefício.",
                'price' => 349.90,
                'compare_price' => 449.90,
                'stock' => 30,
                'sizes' => ['37', '38', '39', '40', '41', '42', '43', '44'],
                'material' => 'Mesh leve e flexível com reforços sintéticos estruturados',
                'sole_material' => 'Entressola de EVA macio com sola de borracha resistente de tração',
                'technical_specifications' => "Tipo de Pisada: Neutra\nDrop da Entressola: 10 mm\nPeso Aproximado: 275 g (tamanho 40)\nIndicação: Academia, corrida leve e esteira\nGarantia: Oficial Nike",
                'weight' => 0.790,
                'height' => 12.0,
                'width' => 22.0,
                'length' => 33.0,
                'image' => 'https://images.unsplash.com/photo-1515955656352-a1fa3ffcd111?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#1E293B', 'name' => 'Black Stealth'],
                    ['hex' => '#3B82F6', 'name' => 'Blue Lagoon'],
                ],
            ],

            // ================= OLYMPIKUS =================
            [
                'name' => 'Tênis Olympikus Corre 3',
                'brand' => 'Olympikus',
                'model_line' => 'Família Corre / Treino e Prova',
                'gender' => 'Unissex',
                'description' => "O tênis de corrida mais premiado do Brasil. Desenvolvido em parceria com atletas profissionais e os maiores especialistas do país no Laboratório de Biomecânica da USP.\n\nConta com a tecnologia Eleva Pro de entressola que garante máxima resposta e durabilidade com apenas 210g. O cabedal Oxitec 2.0 oferece leveza incomparável sem reter suor.",
                'price' => 499.90,
                'compare_price' => 599.90,
                'stock' => 28,
                'sizes' => ['37', '38', '39', '40', '41', '42', '43', '44'],
                'material' => 'Oxitec 2.0 com monofilamento respirável que não absorve água',
                'sole_material' => 'Espuma Eleva Pro com borracha Gripper e Grippertraci em parceria com a Michelin',
                'technical_specifications' => "Tipo de Pisada: Neutra\nDrop da Entressola: 8 mm\nPeso Aproximado: 210 g (tamanho 40)\nIndicação: Corridas diárias, meia maratona, maratona e treinos de ritmo\nTecnologias: Eleva Pro, Oxitec 2.0, Gripper antiderrapante\nGarantia do Fabricante: Contra defeito de fabricação",
                'weight' => 0.740,
                'height' => 12.0,
                'width' => 22.0,
                'length' => 34.0,
                'image' => 'https://images.unsplash.com/photo-1579338559194-a162d19bf842?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#E11D48', 'name' => 'Cereja / Coral'],
                    ['hex' => '#0F172A', 'name' => 'Preto / Limão'],
                    ['hex' => '#06B6D4', 'name' => 'Azul Turquesa'],
                ],
            ],
            [
                'name' => 'Tênis Olympikus Corre Grafeno 2',
                'brand' => 'Olympikus',
                'model_line' => 'Família Corre / Placa de Grafeno',
                'gender' => 'Unissex',
                'description' => "O primeiro tênis do mundo com placa de grafeno. O Corre Grafeno 2 eleva o padrão de propulsão com a tecnologia Grafenus, uma placa rígida que multiplica o retorno de energia a cada passada.\n\nPerfeito para corredores que buscam superar seus recordes pessoais em maratonas e provas oficiais de rua.",
                'price' => 799.90,
                'compare_price' => 999.90,
                'stock' => 16,
                'sizes' => ['38', '39', '40', '41', '42', '43', '44'],
                'material' => 'Oxitec 2.0 ultraleve com reforços aplicados por fusão térmica',
                'sole_material' => 'Entressola dupla Eleva Pro com Placa Grafenus de Grafeno e borracha Michelin',
                'technical_specifications' => "Tipo de Pisada: Neutra / Competição e Super Tênis\nDrop da Entressola: 8 mm\nPeso Aproximado: 240 g (tamanho 40)\nIndicação: Competições de rua de 10k, 21k e 42k\nPlaca de Propulsão: Placa de Grafeno rígida Grafenus\nGarantia: Fabricação nacional com tecnologia patenteada",
                'weight' => 0.770,
                'height' => 12.0,
                'width' => 22.0,
                'length' => 34.0,
                'image' => 'https://images.unsplash.com/photo-1588117260148-b47818741c74?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#F59E0B', 'name' => 'Amarelo Ouro / Grafite'],
                    ['hex' => '#FFFFFF', 'name' => 'Branco / Grafeno'],
                ],
            ],
            [
                'name' => 'Tênis Olympikus Corre Vento 2',
                'brand' => 'Olympikus',
                'model_line' => 'Família Corre / Velocidade e Tiro',
                'gender' => 'Unissex',
                'description' => "Sensação de estar descalço com máxima tração e propulsão. Pesando apenas 168 gramas, o Corre Vento 2 foi desenhado para treinos de velocidade, tiros e corridas de curta distância com resposta instantânea da espuma Eleva Pro.",
                'price' => 449.90,
                'compare_price' => 549.90,
                'stock' => 20,
                'sizes' => ['38', '39', '40', '41', '42', '43'],
                'material' => 'Cabedal Oxitec ultrafino semi-transparente respirável',
                'sole_material' => 'Entressola Eleva Pro responsiva e solado em borracha Gripper antiderrapante',
                'technical_specifications' => "Tipo de Pisada: Neutra\nDrop da Entressola: 8 mm\nPeso Aproximado: 168 g (o mais leve da linha)\nIndicação: Treinos de tiro, fartlek, 5k e 10k\nGarantia: Contra defeito de fabricação",
                'weight' => 0.690,
                'height' => 11.0,
                'width' => 21.0,
                'length' => 33.0,
                'image' => 'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#10B981', 'name' => 'Verde Neon'],
                    ['hex' => '#18181B', 'name' => 'All Black'],
                ],
            ],

            // ================= ASICS =================
            [
                'name' => 'Tênis Asics Gel-Nimbus 26',
                'brand' => 'Asics',
                'model_line' => 'Gel-Nimbus / Conforto Absoluto',
                'gender' => 'Unissex',
                'description' => "Sinta como se estivesse pisando nas nuvens. O Gel-Nimbus 26 revoluciona o conforto com a tecnologia PureGEL posicionada sob o calcanhar, sendo 65% mais macia que o gel tradicional.\n\nA entressola FF BLAST™ PLUS ECO oferece transições aveludadas e amortecimento sustentável de alta densidade.",
                'price' => 1199.90,
                'compare_price' => 1399.90,
                'stock' => 18,
                'sizes' => ['38', '39', '40', '41', '42', '43', '44'],
                'material' => 'Knit elástico super macio que envolve o pé como uma meia anatômica',
                'sole_material' => 'FF BLAST PLUS ECO com PureGEL e sola híbrida ASICSGRIP com borracha AHAR+',
                'technical_specifications' => "Tipo de Pisada: Neutra a Supinada\nDrop da Entressola: 8 mm (calcanhar: 41.5 mm / antepé: 33.5 mm)\nPeso Aproximado: 304 g (tamanho 41)\nIndicação: Longões, corridas de recuperação e maratona com máximo conforto\nGarantia: Oficial Asics Brasil",
                'weight' => 0.890,
                'height' => 13.0,
                'width' => 23.0,
                'length' => 34.0,
                'image' => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#0284C7', 'name' => 'French Blue / White'],
                    ['hex' => '#1E293B', 'name' => 'Black / Pure Silver'],
                    ['hex' => '#F43F5E', 'name' => 'Sunrise Red'],
                ],
            ],
            [
                'name' => 'Tênis Asics Novablast 4',
                'brand' => 'Asics',
                'model_line' => 'Novablast / Efeito Trampolim',
                'gender' => 'Unissex',
                'description' => "Energia explosiva a cada passada. A geometria da entressola inspirada em um trampolim proporciona uma experiência dinâmica e divertida, com a nova espuma FF BLAST™ PLUS ECO oferecendo impulso vigoroso e leveza notável.",
                'price' => 999.90,
                'compare_price' => 1199.90,
                'stock' => 22,
                'sizes' => ['38', '39', '40', '41', '42', '43', '44'],
                'material' => 'Cabedal de engineered woven respirável e maleável',
                'sole_material' => 'FF BLAST PLUS ECO com solado de geometria trampolim e borracha AHAR Low-Hardness',
                'technical_specifications' => "Tipo de Pisada: Neutra\nDrop da Entressola: 8 mm\nPeso Aproximado: 260 g (tamanho 41)\nIndicação: Treinos diários, rodagens com ritmo e provas de 10k a 42k\nGarantia: Contra defeito de fabricação",
                'weight' => 0.810,
                'height' => 12.0,
                'width' => 22.0,
                'length' => 34.0,
                'image' => 'https://images.unsplash.com/photo-1582588678413-dbf45f4823e9?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#8B5CF6', 'name' => 'Illusion Blue'],
                    ['hex' => '#10B981', 'name' => 'Mint Tint'],
                    ['hex' => '#000000', 'name' => 'Triple Black'],
                ],
            ],
            [
                'name' => 'Tênis Asics Gel-Kayano 30',
                'brand' => 'Asics',
                'model_line' => 'Gel-Kayano / Estabilidade Suprema',
                'gender' => 'Masculino',
                'description' => "O ápice em estabilidade para corredores pronadores. Comemorando três décadas de inovação, o Gel-Kayano 30 estreia o sistema 4D Guidance System, uma geometria adaptativa que guia seus passos de forma natural prevenindo a pronação excessiva sem rigidez.",
                'price' => 1299.90,
                'compare_price' => 1499.90,
                'stock' => 14,
                'sizes' => ['39', '40', '41', '42', '43', '44'],
                'material' => 'Engineered stretch knit altamente respirável com ajuste seguro',
                'sole_material' => '4D Guidance System + FF BLAST PLUS ECO + PureGEL interno',
                'technical_specifications' => "Tipo de Pisada: Pronada a Neutra\nDrop da Entressola: 10 mm\nPeso Aproximado: 303 g (tamanho 41)\nIndicação: Corredores que buscam estabilidade, suporte e segurança articular em longas distâncias\nGarantia: Oficial Asics",
                'weight' => 0.880,
                'height' => 13.0,
                'width' => 23.0,
                'length' => 34.0,
                'image' => 'https://images.unsplash.com/photo-1539185441755-769473a23570?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#0F172A', 'name' => 'Black / Glow Yellow'],
                    ['hex' => '#64748B', 'name' => 'Sheet Rock'],
                ],
            ],

            // ================= MIZUNO =================
            [
                'name' => 'Tênis Mizuno Wave Rider 27',
                'brand' => 'Mizuno',
                'model_line' => 'Wave Rider / Clássico Confiável',
                'gender' => 'Unissex',
                'description' => "O modelo mais lendário da história da Mizuno. O Wave Rider 27 entrega a tradicional durabilidade e confiabilidade que os corredores amam, combinada à moderna tecnologia de amortecimento Mizuno Enerzy e placa Wave bio-baseada para transições suaves e impulsão contínua.",
                'price' => 699.90,
                'compare_price' => 899.90,
                'stock' => 20,
                'sizes' => ['38', '39', '40', '41', '42', '43', '44'],
                'material' => 'Jacquard mesh sem costura com excelente respirabilidade e ajuste firme',
                'sole_material' => 'Espuma Mizuno Enerzy por toda a extensão com placa Parallel Wave e borracha X10',
                'technical_specifications' => "Tipo de Pisada: Neutra\nDrop da Entressola: 12 mm\nPeso Aproximado: 280 g (tamanho 41)\nIndicação: Rodagens diárias, treinos em asfalto e esteira\nDurabilidade: Borracha X10 de alta resistência à abrasão no calcanhar\nGarantia: Oficial Mizuno Brasil",
                'weight' => 0.840,
                'height' => 12.0,
                'width' => 22.0,
                'length' => 34.0,
                'image' => 'https://images.unsplash.com/photo-1514989940723-e8e51635b782?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#1D4ED8', 'name' => 'Mizuno Blue'],
                    ['hex' => '#111827', 'name' => 'Black Shadow'],
                ],
            ],
            [
                'name' => 'Tênis Mizuno Wave Prophecy 12',
                'brand' => 'Mizuno',
                'model_line' => 'Wave Prophecy / Ícone Tecnológico',
                'gender' => 'Unissex',
                'description' => "O ícone definitivo de design futurista e status. Com a tecnologia Wave Infinity em toda a extensão do solado, placas duplas de Pebax Rnew dispersam as forças de impacto transformando cada pisada em retorno elástico e presença marcante.",
                'price' => 1699.90,
                'compare_price' => 1999.90,
                'stock' => 12,
                'sizes' => ['38', '39', '40', '41', '42', '43', '44'],
                'material' => 'AIRmesh estruturado com sobreposições metálicas e detalhes refletivos 3M',
                'sole_material' => 'Placa Wave Infinity completa em Pebax com borracha de carbono X10 de extrema durabilidade',
                'technical_specifications' => "Tipo de Pisada: Neutra\nDrop da Entressola: 10 mm\nPeso Aproximado: 380 g\nIndicação: Treinos de impacto, estilo urbano premium e caminhadas esportivas\nGarantia: Oficial Mizuno",
                'weight' => 0.950,
                'height' => 13.0,
                'width' => 23.0,
                'length' => 35.0,
                'image' => 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#0F172A', 'name' => 'Black / Golden Wave'],
                    ['hex' => '#94A3B8', 'name' => 'Silver Metal'],
                ],
            ],

            // ================= PUMA =================
            [
                'name' => 'Tênis Puma Deviate Nitro 2',
                'brand' => 'Puma',
                'model_line' => 'Deviate Nitro / Placa de Carbono',
                'gender' => 'Unissex',
                'description' => "Alta velocidade acessível e durável. O Puma Deviate Nitro 2 conta com a placa de propulsão em fibra de carbono INNOPLATE ensanduichada entre duas camadas de espuma NITRO Elite supercrítica.\n\nFinalizado com o lendário solado PUMAGRIP, considerado pelos corredores como a borracha mais aderente do mercado em pistas secas e molhadas.",
                'price' => 999.90,
                'compare_price' => 1299.90,
                'stock' => 18,
                'sizes' => ['38', '39', '40', '41', '42', '43', '44'],
                'material' => 'Mesh respirável projetado com reforços PWRTAPE para contenção e durabilidade',
                'sole_material' => 'Camada dupla NITRO Elite + placa de carbono INNOPLATE + solado PUMAGRIP',
                'technical_specifications' => "Tipo de Pisada: Neutra\nDrop da Entressola: 6 mm (calcanhar: 38 mm / antepé: 32 mm)\nPeso Aproximado: 258 g (tamanho 41)\nIndicação: Treinos de velocidade, ritmo e provas oficiais de 21k e 42k\nPlaca de Propulsão: Placa de composto de carbono INNOPLATE\nGarantia: Oficial Puma Brasil",
                'weight' => 0.810,
                'height' => 12.0,
                'width' => 22.0,
                'length' => 34.0,
                'image' => 'https://images.unsplash.com/photo-1605348532760-6753d2c43329?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#F97316', 'name' => 'Sun Stream / Fire Red'],
                    ['hex' => '#000000', 'name' => 'Puma Black'],
                    ['hex' => '#06B6D4', 'name' => 'Electric Cyan'],
                ],
            ],
            [
                'name' => 'Tênis Puma Velocity Nitro 3',
                'brand' => 'Puma',
                'model_line' => 'Velocity Nitro / Rodagem Versátil',
                'gender' => 'Unissex',
                'description' => "O queridinho da versatilidade diária. O Velocity Nitro 3 traz ainda mais espuma NITRO para uma corrida macia, responsiva e confortável em qualquer distância ou ritmo, sempre acompanhado da aderência líder do solado PUMAGRIP.",
                'price' => 649.90,
                'compare_price' => 799.90,
                'stock' => 25,
                'sizes' => ['38', '39', '40', '41', '42', '43', '44'],
                'material' => 'Engineered mesh premium com reforço PWRTAPE no mediopé',
                'sole_material' => 'Infusão de nitrogênio NITRO Foam com borracha de tração integral PUMAGRIP',
                'technical_specifications' => "Tipo de Pisada: Neutra\nDrop da Entressola: 8 mm\nPeso Aproximado: 264 g (tamanho 41)\nIndicação: Treinos diários, rodagens regenerativas e caminhadas\nGarantia: Contra defeitos de fabricação",
                'weight' => 0.800,
                'height' => 12.0,
                'width' => 22.0,
                'length' => 34.0,
                'image' => 'https://images.unsplash.com/photo-1608256246200-53e635b5b65f?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#84CC16', 'name' => 'Lime Squeeze'],
                    ['hex' => '#1E293B', 'name' => 'Dark Coal'],
                ],
            ],
        ];

        // 3. Cadastrar ou atualizar cada produto com imagens e cores
        foreach ($products as $data) {
            $brandCategory = $categoryMap[$data['brand']] ?? null;

            $product = Product::updateOrCreate(
                ['name' => $data['name']],
                [
                    'slug' => Str::slug($data['name']),
                    'brand' => $data['brand'],
                    'model_line' => $data['model_line'],
                    'gender' => $data['gender'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'compare_price' => $data['compare_price'],
                    'stock' => $data['stock'],
                    'sizes' => $data['sizes'],
                    'material' => $data['material'],
                    'sole_material' => $data['sole_material'],
                    'weight' => $data['weight'],
                    'height' => $data['height'],
                    'width' => $data['width'],
                    'length' => $data['length'],
                    'technical_specifications' => $data['technical_specifications'],
                    'is_active' => true,
                ]
            );

            // Associar à categoria da marca
            if ($brandCategory) {
                $product->categories()->syncWithoutDetaching([$brandCategory->id]);
            }

            // Foto principal do tênis
            if ($product->images()->count() === 0) {
                $product->images()->create([
                    'path' => $data['image'],
                    'position' => 0,
                    'is_main' => true,
                ]);
            } else {
                $product->images()->first()->update([
                    'path' => $data['image'],
                    'is_main' => true,
                ]);
            }

            // Cores disponíveis
            $product->colors()->delete();
            foreach ($data['colors'] as $color) {
                $product->colors()->create([
                    'hex_code' => $color['hex'],
                    'color_name' => $color['name'],
                ]);
            }
        }
    }
}
