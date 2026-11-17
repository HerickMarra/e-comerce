<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\StockMovement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AirsoftPescaCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Atualizar Configurações Gerais da Loja para o Nicho de Airsoft, Pesca e Camping
        $settings = [
            'store_name' => 'Alpha Tactical & Pesca',
            'store_slogan' => 'Airsoft de Elite, Pesca Esportiva & Equipamentos de Camping',
            'store_category' => 'Airsoft, Pescaria & Camping',
            'store_footer_description' => 'Sua loja especializada em armas de airsoft (AEG, GBB, Snipers), equipamentos táticos, varas e carretilhas de pesca esportiva, barracas impermeáveis e sobrevivência.',
            'store_company_name' => 'Alpha Tactical & Pesca do Brasil LTDA',
            'store_document' => '38.452.190/0001-44',
            'store_state_register' => 'ISENTO',
            'store_email' => 'contato@alphatacticalpesca.com.br',
            'store_phone' => '(61) 98522-0100',
            'store_whatsapp' => '5561985220100',
            'store_whatsapp_message' => 'Olá! Gostaria de tirar dúvidas sobre os equipamentos de Airsoft / Pescaria.',
            'store_street' => 'Avenida das Forças, Lote 14',
            'store_number' => '102',
            'store_neighborhood' => 'Setor de Armaduras e Aventura',
            'store_city' => 'Brasília',
            'store_state' => 'DF',
            'store_zip' => '70070-010',
            'store_opening_hours' => 'Segunda a Sexta: 08:30 às 18:30 | Sábado: 09h às 15h',
            'home_hero_tag' => 'Airsoft, Pesca & Aventura 2026',
            'home_hero_title' => 'Equipamentos de Elite para Operações e Aventuras',
            'home_hero_subtitle' => 'Rifles AEG, Pistolas GBB, Varas de Carbono, Carretilhas de Alta Velocidade, Barracas Impermeáveis e Proteção Tática.',
            'home_hero_btn_text' => 'Explorar Catálogo',
            'home_hero_btn_link' => '/explorar',
            'primary_color' => '#10b981',
            'secondary_color' => '#0f172a',
            'footer_bg_color' => '#0f172a',
            'footer_text_color' => '#f8fafc',
            'header_bg_color' => '#ffffff',
            'header_text_color' => '#0f172a',
        ];

        foreach ($settings as $key => $val) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $val, 'is_encrypted' => false]
            );
        }

        // 2. Banners Promocionais da Home
        Banner::truncate();
        Banner::create([
            'title' => 'Airsoft de Elite: Rifles AEG Full Metal & Pistolas GBB',
            'media_type' => 'image',
            'image_url' => 'https://images.unsplash.com/photo-1595590424283-b8f17842773f?auto=format&fit=crop&q=80&w=1600',
            'btn_text' => 'Ver Armas de Airsoft',
            'btn_link' => '/explorar',
            'is_active' => true,
            'position' => 0,
        ]);

        Banner::create([
            'title' => 'Pesca Esportiva: Varas de Carbono, Carretilhas & Iscas Artificiais',
            'media_type' => 'image',
            'image_url' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&q=80&w=1600',
            'btn_text' => 'Linha Pescaria',
            'btn_link' => '/explorar',
            'is_active' => true,
            'position' => 1,
        ]);

        Banner::create([
            'title' => 'Camping & Sobrevivência: Barracas Impermeáveis e Mochilas Táticas',
            'media_type' => 'image',
            'image_url' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&q=80&w=1600',
            'btn_text' => 'Ver Linha Camping',
            'btn_link' => '/explorar',
            'is_active' => true,
            'position' => 2,
        ]);

        // 3. Cadastrar as Categorias Principais
        $categoriesData = [
            [
                'name' => 'Armas de Airsoft',
                'slug' => 'armas-de-airsoft',
                'description' => 'Rifles AEG elétricos, pistolas GBB a gás com blowback real, snipers de precisão e shotguns com ponta laranja regulamentada.',
                'image_path' => 'https://images.unsplash.com/photo-1595590424283-b8f17842773f?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'Pescaria & Acessórios',
                'slug' => 'pescaria-e-acessorios',
                'description' => 'Varas de fibra de carbono ultra leves, carretilhas de alta velocidade, molinetes marinados, linhas multifilamento e iscas.',
                'image_path' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'Barracas & Camping',
                'slug' => 'barracas-e-camping',
                'description' => 'Barracas impermeáveis para 2 a 6 pessoas com alta coluna d\'água, sacos de dormir térmicos, colchões infláveis e fogareiros.',
                'image_path' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'Equipamentos Táticos',
                'slug' => 'equipamentos-taticos',
                'description' => 'Coletes modulares Molle, capacetes balísticos FAST, óculos antiembaçantes UV400, luvas táticas e coldres rígidos.',
                'image_path' => 'https://images.unsplash.com/photo-1579829366248-204fe8413f31?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'Munições & Co2',
                'slug' => 'municoes-e-co2',
                'description' => 'Esferas BBs 0.20g, 0.25g e 0.28g de alta precisão com polimento triplo, cilindros CO2 12g, Green Gas e magazines.',
                'image_path' => 'https://images.unsplash.com/photo-1585250004680-753f5052706e?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'Cutelaria & Sobrevivência',
                'slug' => 'cutelaria-e-sobrevivencia',
                'description' => 'Facas militares de caça, canivetes automáticos inox, lanternas táticas LED T6 de longo alcance, mochilas 45L e cantis.',
                'image_path' => 'https://images.unsplash.com/photo-1593079831268-3381b0db4a77?auto=format&fit=crop&q=80&w=800',
            ],
        ];

        $categoryMap = [];
        foreach ($categoriesData as $catData) {
            $cat = Category::updateOrCreate(
                ['slug' => $catData['slug']],
                [
                    'name' => $catData['name'],
                    'description' => $catData['description'],
                    'image_path' => $catData['image_path'],
                ]
            );
            $categoryMap[$catData['slug']] = $cat;
        }

        // 4. Catálogo Completo de Produtos
        $catalog = [
            // ================= ARMAS DE AIRSOFT =================
            [
                'name' => 'Rifle Airsoft AEG M4A1 Carbine Full Metal 6mm',
                'category_slug' => 'armas-de-airsoft',
                'brand' => 'Cyma / Rossi',
                'model_line' => 'AEG Profissional',
                'gender' => 'Unissex',
                'description' => "O consagrado Rifle M4A1 Carbine é a arma mais icônica do airsoft mundial. Possui corpo full metal de altíssima durabilidade, Gearbox V2 em metal reforçado com engrenagens de aço, Hop-Up ajustável e coronha retrátil tipo Crane multi-posições.\n\nAcompanha magazine Hi-Cap para 300 BBs, bateria recarregável, carregador bi-volt e ponta laranja regulamentada pelo Exército Brasileiro.",
                'price' => 1890.00,
                'compare_price' => 2250.00,
                'stock' => 15,
                'sizes' => ['Padrão (Calibre 6mm)'],
                'material' => 'Full Metal (Receptor e cano em liga metálica, coronha e empunhadura em polímero reforçado)',
                'sole_material' => 'Gearbox V2 Metal com buchas de 8mm e engrenagens em aço',
                'technical_specifications' => "Calibre: 6mm BBs\nVelocidade de Disparo: 380 a 400 FPS (com esferas 0.20g)\nAlcance Efetivo: 50 a 60 metros\nModos de Disparo: Semi-Automático, Automático (Full-Auto) e Safe\nCapacidade do Magazine: 300 BBs (Hi-Cap)\nPeso Aproximado: 2.850 g\nComprimento: 770 mm (fechada) a 850 mm (estendida)\nPonta Laranja: Sim, metálica regulamentada\nGarantia: 3 meses contra defeitos de fabricação",
                'weight' => 2.85,
                'height' => 26.0,
                'width' => 9.0,
                'length' => 85.0,
                'image' => 'https://images.unsplash.com/photo-1595590424283-b8f17842773f?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#0F172A', 'name' => 'Preto Tático (Blackout)'],
                    ['hex' => '#D4A373', 'name' => 'Tan / Desert'],
                    ['hex' => '#4A5D4E', 'name' => 'Camuflado Multicam'],
                ],
            ],
            [
                'name' => 'Pistola Airsoft GBB Glock G17 Gen5 Blowback Green Gas 6mm',
                'category_slug' => 'armas-de-airsoft',
                'brand' => 'Umarex / VFC',
                'model_line' => 'GBB Blowback Real',
                'gender' => 'Unissex',
                'description' => "Réplica oficial licenciada pela Glock Áustria com marcações perfeitas e numeração de série exclusiva. Sistema Gas Blowback (GBB) potente que simula com exatidão o recuo real da arma a cada disparo efetuado.\n\nSlide em metal usinado CNC, travas ambidestras e empunhadura ergonômica com textura antiderrapante Gen5.",
                'price' => 1390.00,
                'compare_price' => 1650.00,
                'stock' => 20,
                'sizes' => ['Padrão (Calibre 6mm)'],
                'material' => 'Slide em metal usinado CNC e corpo inferior em polímero reforçado com fibra de vidro',
                'sole_material' => 'Sistema Blowback com bocal de vedação de alto fluxo',
                'technical_specifications' => "Calibre: 6mm\nVelocidade: 300 a 320 FPS com BBs 0.20g\nPropulsão: Green Gas com silicone\nCapacidade do Carregador: 22 BBs\nPeso: 650 g\nComprimento Total: 202 mm\nTrilho Inferior: 20 mm Picatinny para lanterna ou laser\nGarantia: 3 meses",
                'weight' => 0.75,
                'height' => 16.0,
                'width' => 5.0,
                'length' => 25.0,
                'image' => 'https://images.unsplash.com/photo-1584281722572-8f191b7d598e?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#0A0A0A', 'name' => 'Preto Fosco'],
                    ['hex' => '#9C826B', 'name' => 'Coyote Tan'],
                ],
            ],
            [
                'name' => 'Rifle Sniper Airsoft L96 AWP Bolt Action com Luneta 3-9x40 e Bipé',
                'category_slug' => 'armas-de-airsoft',
                'brand' => 'Well Fire',
                'model_line' => 'Sniper Longo Alcance',
                'gender' => 'Unissex',
                'description' => "Projetado para atiradores de elite que exigem alcance extremo e precisão cirúrgica. Sistema por ferrolho manual (Bolt Action) com mola reforçada capaz de disparar acima de 480 FPS.\n\nAcompanha Luneta 3-9x40 com retículo de mira Mil-Dot, bipé tático retrátil e magazine com engatilhamento suave.",
                'price' => 1650.00,
                'compare_price' => 1990.00,
                'stock' => 10,
                'sizes' => ['Padrão (Calibre 6mm)'],
                'material' => 'Cano e conjunto de gatilho em metal; coronha tática em polímero de alta densidade',
                'sole_material' => 'Sistema de mola Spring Bolt Action de alta compressão',
                'technical_specifications' => "Calibre: 6mm BBs\nVelocidade de Saída: 480 a 500 FPS (com 0.20g)\nAlcance Efetivo: Mais de 70 metros\nAcompanha: Luneta 3-9x40 + Bipé de Metal Ajustável\nCapacidade do Magazine: 30 BBs\nPeso Total: 3.900 g com acessórios\nComprimento: 1.150 mm\nGarantia: 3 meses",
                'weight' => 3.9,
                'height' => 20.0,
                'width' => 15.0,
                'length' => 120.0,
                'image' => 'https://images.unsplash.com/photo-1579373903781-fd5c0c30c4cd?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#3D4F3F', 'name' => 'Verde Militar (OD Green)'],
                    ['hex' => '#18181B', 'name' => 'Preto Blackout'],
                ],
            ],
            [
                'name' => 'Pistola Airsoft CO2 1911 Tactical Full Metal Blowback 6mm',
                'category_slug' => 'armas-de-airsoft',
                'brand' => 'KWC / CyberGun',
                'model_line' => 'CO2 Blowback Potente',
                'gender' => 'Unissex',
                'description' => "A lendária pistola Colt 1911 em versão tática contemporânea para airsoft. Construção 100% full metal que confere um peso idêntico ao modelo real. Utiliza cápsulas de CO2 12g que entregam um recuo contundente e forte estalo.",
                'price' => 1150.00,
                'compare_price' => 1380.00,
                'stock' => 18,
                'sizes' => ['Padrão (Calibre 6mm)'],
                'material' => 'Construção 100% em liga de zinco e alumínio usinado (Full Metal)',
                'sole_material' => 'Válvula de alta pressão com autonomia de até 60 disparos por cápsula de CO2',
                'technical_specifications' => "Calibre: 6mm\nVelocidade: 360 FPS\nPropulsão: Cilindro CO2 12g\nCapacidade: 16 BBs\nPeso: 980 g\nComprimento: 220 mm\nGarantia: 3 meses",
                'weight' => 1.05,
                'height' => 16.0,
                'width' => 6.0,
                'length' => 26.0,
                'image' => 'https://images.unsplash.com/photo-1585250004680-753f5052706e?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#94A3B8', 'name' => 'Inox / Prata Fosco'],
                    ['hex' => '#0F172A', 'name' => 'Preto Tático'],
                ],
            ],
            [
                'name' => 'Rifle Airsoft AEG AK-47 Tactical Kalashnikov 6mm',
                'category_slug' => 'armas-de-airsoft',
                'brand' => 'Cyma',
                'model_line' => 'AEG Linha Tática',
                'gender' => 'Unissex',
                'description' => "O fuzil russo mais famoso do planeta adaptado para combates modernos de airsoft. Equipada com trilhos táticos RIS de 20mm no guarda-mão para instalação de lanternas, grips e red dots.\n\nRobusto, confiável e com alta cadência de tiro em qualquer condição de clima.",
                'price' => 1580.00,
                'compare_price' => 1890.00,
                'stock' => 12,
                'sizes' => ['Padrão (Calibre 6mm)'],
                'material' => 'Receptor de metal e empunhaduras em polímero resistente a choques',
                'sole_material' => 'Gearbox V3 em metal reforçado',
                'technical_specifications' => "Calibre: 6mm\nVelocidade: 390 FPS\nModos de Disparo: Semi, Full-Auto e Safe\nCapacidade Magazine: 500 BBs (Hi-Cap)\nPeso: 3.100 g\nComprimento: 875 mm\nGarantia: 3 meses",
                'weight' => 3.2,
                'height' => 28.0,
                'width' => 10.0,
                'length' => 90.0,
                'image' => 'https://images.unsplash.com/photo-1599818816942-83da70b779a9?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#1E293B', 'name' => 'Preto Tático'],
                    ['hex' => '#78350F', 'name' => 'Madeira Clássica com Metal'],
                ],
            ],

            // ================= PESCARIA & ACESSÓRIOS =================
            [
                'name' => 'Carretilha de Pesca Maruri Black Tamba 11 Rolamentos Drag 8kg',
                'category_slug' => 'pescaria-e-acessorios',
                'brand' => 'Maruri',
                'model_line' => 'Alta Velocidade 7.1:1',
                'gender' => 'Unissex',
                'description' => "Desenvolvida especialmente para pescadores exigentes que buscam grandes peixes em pesqueiros, rios e represas (como Tambacus, Pintados e Dourados). Sistema de freio duplo magnético e centrífugo que evita cabeleiras.\n\nCorpo compacto em grafite de alta resistência, carretel em alumínio aliviado e drag super potente de 8 kg.",
                'price' => 429.90,
                'compare_price' => 520.00,
                'stock' => 25,
                'sizes' => ['Manivela Direita', 'Manivela Esquerda'],
                'material' => 'Chassi em grafite reforçado e carretel em alumínio aliviado CNC',
                'sole_material' => 'Sistema de freio magnético com 10 níveis de regulagem precisa',
                'technical_specifications' => "Relação de Recolhimento: 7.1:1\nRolamentos: 10 de esferas + 1 de roletes em aço inox\nDrag Máximo (Fricção): 8 kg\nCapacidade de Linha: 0.37 mm - 135 m\nPeso: Apenas 202 g\nGuia de linha revestido em titânio para linhas multifilamento\nGarantia: 6 meses",
                'weight' => 0.25,
                'height' => 9.0,
                'width' => 13.0,
                'length' => 16.0,
                'image' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#0F172A', 'name' => 'Blackout com Detalhes Rubro'],
                    ['hex' => '#334155', 'name' => 'Gunmetal Metálico'],
                ],
            ],
            [
                'name' => 'Vara de Pesca de Carbono Tubular Shimano FX 6\'0" Ação Rápida',
                'category_slug' => 'pescaria-e-acessorios',
                'brand' => 'Shimano',
                'model_line' => 'Carbono FX Performance',
                'gender' => 'Unissex',
                'description' => "A clássica vara Shimano FX foi aperfeiçoada com blank em grafite aeroglass de alta sensibilidade e flexibilidade controlada. Ideal para pesca esportiva com iscas artificiais de tucunaré, robalo, traíra e dourado.\n\nPassadores reforçados com anéis de óxido de alumínio compatíveis com linhas multifilamento de alta abrasão.",
                'price' => 289.90,
                'compare_price' => 359.00,
                'stock' => 30,
                'sizes' => ['1.83m (6\'0") 12-25 Lbs', '1.98m (6\'6") 14-30 Lbs', '2.10m (7\'0") 15-35 Lbs'],
                'material' => 'Blank em composto de grafite e fibra de carbono tubular de alta sensibilidade',
                'sole_material' => 'Cabo em EVA bipartido ergonômico e passadores de cerâmica',
                'technical_specifications' => "Aplicação: Carretilha ou Molinete\nAção: Rápida\nLibragem: 12-25 Lbs (versão padrão)\nSecções: 2 partes para fácil transporte\nPassadores: 6 + ponteira anti-enrosco\nPeso Aproximado: 145 g\nGarantia: 1 ano oficial Shimano",
                'weight' => 0.28,
                'height' => 7.0,
                'width' => 7.0,
                'length' => 105.0,
                'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#000000', 'name' => 'Preto com Dourado'],
                ],
            ],
            [
                'name' => 'Molinete de Pesca Daiwa Crossfire 3000-LT 4 Rolamentos Drag 10kg',
                'category_slug' => 'pescaria-e-acessorios',
                'brand' => 'Daiwa',
                'model_line' => 'Light & Tough (LT Concept)',
                'gender' => 'Unissex',
                'description' => "Com o conceito de engenharia LT (Light & Tough), a Daiwa conseguiu criar um molinete incrivelmente leve sem perder a resistência e a robustez necessárias para combater grandes peixes.\n\nCarretel em alumínio ABS com saída de linha livre que proporciona arremessos muito mais longos e precisos.",
                'price' => 349.90,
                'compare_price' => 419.00,
                'stock' => 16,
                'sizes' => ['Modelo 3000-LT (Drag 10kg)'],
                'material' => 'Corpo em carbono DS4 e engrenagens DigiGear usinadas com precisão digital',
                'sole_material' => 'Freio ATD (Automatic Tournament Drag) ultra suave',
                'technical_specifications' => "Rolamentos: 4 em aço inox\nRecolhimento: 5.3:1 (80 cm por volta)\nDrag Máximo: 10 kg\nCapacidade de Linha: 0.28 mm - 150 m\nPeso: 260 g\nManivela: Reversível (Direita/Esquerda) em alumínio\nGarantia: 1 ano Daiwa",
                'weight' => 0.35,
                'height' => 12.0,
                'width' => 13.0,
                'length' => 15.0,
                'image' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#475569', 'name' => 'Grafite com Prata'],
                ],
            ],
            [
                'name' => 'Kit com 10 Iscas Artificiais Meia Água com Rattlin e Olhos 3D',
                'category_slug' => 'pescaria-e-acessorios',
                'brand' => 'Marine Sports',
                'model_line' => 'Iscas Holográficas Predator',
                'gender' => 'Unissex',
                'description' => "Kit premium com 10 iscas de meia água balanceadas com pesos internos e esferas de chocalho (Rattlin) sonoro para provocar o ataque dos peixes predadores mais manhosos.\n\nPintura holográfica a laser com cores realistas e garatéias em aço carbono VMC afiadas quimicamente.",
                'price' => 139.90,
                'compare_price' => 189.90,
                'stock' => 40,
                'sizes' => ['Kit Completo 10 Unidades'],
                'material' => 'Plástico ABS de alta durabilidade e garatéias em aço carbono',
                'sole_material' => 'Olhos 3D realistas e chocalho interno com esferas de tungstênio',
                'technical_specifications' => "Quantidade: 10 iscas variadas\nTamanho médio: 8,5 cm a 10 cm\nPeso médio: 9 g a 13 g\nAção: Meia-Água (Floating / Suspending)\nProfundidade de nado: 0,8m a 1,8m\nPeixes indicados: Tucunaré, Robalo, Traíra, Dourado, Black Bass e Matrinxã",
                'weight' => 0.2,
                'height' => 5.0,
                'width' => 15.0,
                'length' => 22.0,
                'image' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#06B6D4', 'name' => 'Sortidas Holográficas'],
                ],
            ],
            [
                'name' => 'Linha de Pesca Multifilamento 8X 300m Trançada Ultra Resistente',
                'category_slug' => 'pescaria-e-acessorios',
                'brand' => 'YGK / Maruri',
                'model_line' => 'Super PE 8 Fios',
                'gender' => 'Unissex',
                'description' => "Linha trançada com 8 fios de microfibra de polietileno de última geração. Perfil 100% redondo e liso que desliza com perfeição pelos passadores, proporcionando arremessos 25% mais distantes.\n\nElasticidade zero para fisgadas imediatas e máxima sensibilidade no toque dos peixes.",
                'price' => 99.90,
                'compare_price' => 135.00,
                'stock' => 50,
                'sizes' => ['30 Lbs (0.23mm)', '40 Lbs (0.28mm)', '50 Lbs (0.32mm)'],
                'material' => '100% Microfibra de Polietileno (Ultra PE) com revestimento teflonado impermeável',
                'sole_material' => 'Trançamento octogonal de 8 filamentos de alta tenacidade',
                'technical_specifications' => "Comprimento do Carretel: 300 Metros\nResistência: Até 23 kg (versão 50 Lbs)\nMemória: Nula\nAbsorção de Água: Zero\nVisibilidade: Alta visibilidade fora da água para o pescador",
                'weight' => 0.15,
                'height' => 8.0,
                'width' => 10.0,
                'length' => 10.0,
                'image' => 'https://images.unsplash.com/photo-1541781774459-bb2af2f05b55?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#15803D', 'name' => 'Verde Musgo Floresta'],
                    ['hex' => '#FACC15', 'name' => 'Amarelo Fluorescente'],
                ],
            ],

            // ================= BARRACAS & CAMPING =================
            [
                'name' => 'Barraca de Camping Impermeável Nautika Indy GT 4/5 Pessoas Coluna d\'Água 2500mm',
                'category_slug' => 'barracas-e-camping',
                'brand' => 'Nautika (NTK)',
                'model_line' => 'Indy GT Aventura',
                'gender' => 'Unissex',
                'description' => "A barraca preferida dos campistas experientes. Conta com avançê dianteiro fechado com janela de visualização e tapete, funcionando como uma antessala privativa para guardar calçados, mochilas e equipamentos com total segurança.\n\nSobreteto integral em poliéster 190T resinado com coluna d'água de 2.500 mm e costuras seladas termo-soldadas que garantem proteção 100% à prova de chuvas torrenciais.",
                'price' => 899.90,
                'compare_price' => 1150.00,
                'stock' => 14,
                'sizes' => ['4 a 5 Pessoas (2.40m x 2.10m + 1.55m avanço)'],
                'material' => 'Poliéster 190T impermeável com piso em polietileno de alta densidade anti-fungo',
                'sole_material' => 'Varetas em fibra de vidro virgem Nano-Flex com tecnologia de memória elástica',
                'technical_specifications' => "Capacidade: 4 a 5 Pessoas com conforto\nColuna d'Água: 2.500 mm (resistente a temporais)\nDimensões do Dormitório: 2,40 m x 2,10 m x 1,30 m de altura\nÁrea do Avançê: 1,55 m de comprimento frontal\nPeso Total: 5.000 g\nVentilação: Janelas respiráveis com mosquiteiro no-see-um extra fino\nAcompanha: Bolsa de transporte reforçada com alças, estacas e cordas de fixação\nGarantia: 6 meses Nautika",
                'weight' => 5.1,
                'height' => 18.0,
                'width' => 20.0,
                'length' => 65.0,
                'image' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#0F766E', 'name' => 'Verde Petróleo com Cinza'],
                ],
            ],
            [
                'name' => 'Barraca de Camping Dome 2 Pessoas Leve e Compacta Impermeável',
                'category_slug' => 'barracas-e-camping',
                'brand' => 'Coleman / NTK',
                'model_line' => 'Dome Compact Ultralight',
                'gender' => 'Unissex',
                'description' => "Ideal para trilhas, pesca e acampamentos rápidos a dois. Montagem instantânea em menos de 5 minutos através do sistema de engates rápidos das varetas cruzadas.\n\nLeve e extremamente compacta, cabe facilmente amarrada na lateral de qualquer mochila de aventura.",
                'price' => 389.90,
                'compare_price' => 479.00,
                'stock' => 22,
                'sizes' => ['2 Pessoas (2.05m x 1.45m x 1.10m)'],
                'material' => 'Poliéster respirável com sobreteto impermeabilizado',
                'sole_material' => 'Piso em polietileno selado à prova de infiltração de solo úmido',
                'technical_specifications' => "Capacidade: 2 Pessoas\nColuna d'Água: 1.800 mm\nPeso: Apenas 2.100 g\nDimensões: 2,05 m x 1,45 m x 1,10 m\nMosquiteiro: Tela ultrafina que impede entrada de pernilongos e mosquitos",
                'weight' => 2.2,
                'height' => 14.0,
                'width' => 15.0,
                'length' => 55.0,
                'image' => 'https://images.unsplash.com/photo-1478860409698-8707f313ee8b?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#1E3A8A', 'name' => 'Azul Marinho com Cinza'],
                    ['hex' => '#EA580C', 'name' => 'Laranja Segurança'],
                ],
            ],
            [
                'name' => 'Barraca Familiar Camping 6 Pessoas com Varanda e Quartos Duplos',
                'category_slug' => 'barracas-e-camping',
                'brand' => 'Nautika',
                'model_line' => 'Grand Family Luxury',
                'gender' => 'Unissex',
                'description' => "O ápice do conforto para acampamentos em família ou grandes grupos de amigos. Possui altura interna de 1,90 metro permitindo que adultos fiquem em pé confortavelmente no seu interior.\n\nDivisória interna removível que cria 2 dormitórios privativos independentes além da grande varanda frontal.",
                'price' => 1490.00,
                'compare_price' => 1850.00,
                'stock' => 8,
                'sizes' => ['6 Pessoas (4.60m x 2.60m x 1.90m)'],
                'material' => 'Poliéster 190T respirável impermeabilizado com 3000mm de coluna d\'água',
                'sole_material' => 'Estrutura reforçada de fibra de vidro de 11mm e estacas em aço galvanizado',
                'technical_specifications' => "Capacidade: 6 Pessoas adultas\nColuna d'Água: 3.000 mm resistente a tempestades fortes\nAltura Interna: 1,90 m\nPeso: 8.900 g\nAcompanha: 2 dormitórios com portas independentes e bolsas organizadoras internas",
                'weight' => 9.2,
                'height' => 25.0,
                'width' => 28.0,
                'length' => 70.0,
                'image' => 'https://images.unsplash.com/photo-1510312305653-8ed496efae75?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#334155', 'name' => 'Cinza Chumbo com Verde Oliva'],
                ],
            ],
            [
                'name' => 'Saco de Dormir Térmico Militar Antares -5°C a 10°C Compacto',
                'category_slug' => 'barracas-e-camping',
                'brand' => 'Azteq',
                'model_line' => 'Expedition Thermal',
                'gender' => 'Unissex',
                'description' => "Saco de dormir tipo sarcófago para noites frias de acampamento em serras, rios e matas fechadas. Enchimento com fibras sintéticas espiraladas ocas que retêm o calor do corpo sem acumular umidade.\n\nAcompanha bolsa de compressão que reduz o volume do saco de dormir em até 40% para caber na mochila.",
                'price' => 349.90,
                'compare_price' => 420.00,
                'stock' => 25,
                'sizes' => ['Adulto (2.20m x 0.80m)'],
                'material' => 'Tecido externo em Poliéster Ripstop 210T à prova d\'água e forro em algodão suave',
                'sole_material' => 'Zíper duplo reforçado com aba interna anti-vento',
                'technical_specifications' => "Faixa de Temperatura: Conforto 8°C | Limite 0°C | Extremo -5°C\nDimensões aberto: 2,20 m x 0,80 m\nPeso: 1.250 g\nCapuz ergonômico ajustável com cordão elástico para vedação do pescoço",
                'weight' => 1.3,
                'height' => 18.0,
                'width' => 18.0,
                'length' => 35.0,
                'image' => 'https://images.unsplash.com/photo-1517824806704-9040b037703b?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#2E3A2F', 'name' => 'Verde Tático Militar'],
                    ['hex' => '#111827', 'name' => 'Preto Blackout'],
                ],
            ],
            [
                'name' => 'Mochila Tática Militar 45 Litros Sistema Molle Impermeável',
                'category_slug' => 'cutelaria-e-sobrevivencia',
                'brand' => 'Invictus / Tactical Force',
                'model_line' => 'Assault 45L Molle',
                'gender' => 'Unissex',
                'description' => "Mochila padrão militar construída em tecido resinado de alta densidade 600D. Possui 5 compartimentos principais com zíperes duplos de nylon silencioso e sistema de fitas MOLLE em toda a área frontal e lateral para fixação de bolsos extras, canivetes e cantis.\n\nCostas acolchoadas com canal de ventilação e alças anatômicas que garantem conforto mesmo com carga pesada.",
                'price' => 269.90,
                'compare_price' => 349.00,
                'stock' => 35,
                'sizes' => ['Capacidade 45 Litros'],
                'material' => 'Poliéster 600D resinado em PVC resistente a água, rasgos e abrasão',
                'sole_material' => 'Fitas e fechos em poliacetal de alta resistência mecânica',
                'technical_specifications' => "Volume: 45 Litros\nDimensões: 50 cm x 30 cm x 30 cm\nPeso: 1.200 g\nCompatibilidade: Possui bolso traseiro especial para refil de hidratação de até 3L (Camelbak)\nCinto de fixação torácico e abdominal ajustáveis",
                'weight' => 1.25,
                'height' => 10.0,
                'width' => 35.0,
                'length' => 50.0,
                'image' => 'https://images.unsplash.com/photo-1622560480605-d83c853bc5c3?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#0F172A', 'name' => 'Preto Tático'],
                    ['hex' => '#A38B6D', 'name' => 'Coyote Tan'],
                    ['hex' => '#3F4F3F', 'name' => 'Verde Oliva'],
                ],
            ],

            // ================= EQUIPAMENTOS TÁTICOS =================
            [
                'name' => 'Colete Tático Modular Plate Carrier Molle com Porta Carregadores',
                'category_slug' => 'equipamentos-taticos',
                'brand' => 'Invictus / EmersonGear',
                'model_line' => 'Plate Carrier Operacional',
                'gender' => 'Unissex',
                'description' => "O colete mais utilizado por operadores de airsoft e forças especiais. Equipado com sistema MOLLE cortado a laser ou em fitas de alta resistência. Inclui porta-carregadores triplo para rifle M4/AK47 frontal e bolso utilitário administrativo.\n\nPermite a inserção de placas dummy de proteção de EVA inclusas para manter o colete armado e realista.",
                'price' => 489.90,
                'compare_price' => 599.00,
                'stock' => 20,
                'sizes' => ['Tamanho Único Ajustável (P ao GG)'],
                'material' => 'Tecido Cordura 1000D de padrão militar resistente a rasgos e abrasão',
                'sole_material' => 'Placas internas dummy em EVA de alta absorção de impacto de tiros de BBs',
                'technical_specifications' => "Ajuste: Ombros e laterais com fecho autocolante resistente\nAcessórios inclusos: Porta 3 Magazines de Fuzil + Bolso Utilitário Medic/Admin\nPeso: 1.400 g\nAlça de resgate reforçada na parte traseira",
                'weight' => 1.45,
                'height' => 12.0,
                'width' => 35.0,
                'length' => 45.0,
                'image' => 'https://images.unsplash.com/photo-1541781774459-bb2af2f05b55?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#09090B', 'name' => 'Preto Blackout'],
                    ['hex' => '#5C624E', 'name' => 'Camuflado Multicam'],
                    ['hex' => '#8C7456', 'name' => 'Coyote Brown'],
                ],
            ],
            [
                'name' => 'Capacete Tático Militar FAST PJ com Trilhos Laterais e Suporte NVG',
                'category_slug' => 'equipamentos-taticos',
                'brand' => 'FMA Tactical',
                'model_line' => 'FAST Helmet PJ',
                'gender' => 'Unissex',
                'description' => "Capacete tático leve e ultra resistente com trilhos laterais ARC para instalação de lanternas, câmeras esportivas e fones abafadores. Possui suporte frontal reforçado tipo Shroud para óculos de visão noturna (NVG) ou action cams.\n\nAcompanha almofadas internas de memória com retenção por catraca tipo Dial ajustável à cabeça com precisão milimétrica.",
                'price' => 249.90,
                'compare_price' => 320.00,
                'stock' => 25,
                'sizes' => ['Tamanho Ajustável (56 a 62 cm de circunferência)'],
                'material' => 'Polímero ABS de alta resistência a impacto e fivelas de nylon reforçado',
                'sole_material' => 'Pads em espuma acolchoada EPP com forro antibacteriano',
                'technical_specifications' => "Peso: 850 g\nVentilação: Furos estratégicos superiores para circulação de ar\nTrilhos: Sistema ARC de 20 mm\nSuporte Frontal: Padrão NVG 3 furos\nFechamento: Jugular de 4 pontos com queixeira confortável",
                'weight' => 0.95,
                'height' => 18.0,
                'width' => 24.0,
                'length' => 28.0,
                'image' => 'https://images.unsplash.com/photo-1579829366248-204fe8413f31?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#18181B', 'name' => 'Preto Fosco'],
                    ['hex' => '#A89276', 'name' => 'Tan / Areia'],
                    ['hex' => '#3D493D', 'name' => 'Verde Oliva'],
                ],
            ],
            [
                'name' => 'Óculos de Proteção Balística Tática Anti-Impacto com 3 Lentes UV400',
                'category_slug' => 'equipamentos-taticos',
                'brand' => 'Daisy / ESS',
                'model_line' => 'C5 Balístico Militar',
                'gender' => 'Unissex',
                'description' => "Item de proteção essencial e obrigatório em qualquer jogo de airsoft ou estande de tiro. Suporta impactos diretos de esferas BBs acima de 500 FPS sem quebrar ou trincar.\n\nAcompanha estojo rígido e 3 lentes intercambiáveis em policarbonato: Fumê Polarizada para sol intenso, Amarela para visão noturna/baixa luz e Transparente para ambientes fechados (CQB).",
                'price' => 129.90,
                'compare_price' => 169.00,
                'stock' => 45,
                'sizes' => ['Tamanho Único com Fita Elástica e Hastes'],
                'material' => 'Armação em polímero TR90 flexível com lentes de policarbonato militar balístico de 2.2mm',
                'sole_material' => 'Revestimento interno em espuma macia com vedação completa dos olhos',
                'technical_specifications' => "Proteção: 100% UV400 contra raios solares e anti-impacto ANSI Z87.1\nLentes Inclusas: 3 (Fumê, Amarela e Incolor)\nAcessórios: Estojo semirrígido, flanela de microfibra e cinta elástica para cabeça\nPeso: 45 g",
                'weight' => 0.25,
                'height' => 8.0,
                'width' => 12.0,
                'length' => 18.0,
                'image' => 'https://images.unsplash.com/photo-1577803645773-f96470509666?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#0F172A', 'name' => 'Armação Preta'],
                    ['hex' => '#8C7456', 'name' => 'Armação Tan Desert'],
                ],
            ],
            [
                'name' => 'Luvas Táticas Meio Dedo com Proteção em Fibra de Carbono nas Juntas',
                'category_slug' => 'equipamentos-taticos',
                'brand' => 'Mechanix / Tactical',
                'model_line' => 'Hard Knuckle Half-Finger',
                'gender' => 'Unissex',
                'description' => "Desenvolvidas para oferecer a máxima sensibilidade no gatilho mantendo a proteção completa das articulações e dorso da mão contra disparos de BBs, galhos e quedas.\n\nPalma em camurça sintética com grip antiderrapante e fecho de pulso em borracha termoplástica com ajuste seguro.",
                'price' => 89.90,
                'compare_price' => 120.00,
                'stock' => 40,
                'sizes' => ['M (Médio)', 'G (Grande)', 'GG (Extra Grande)'],
                'material' => 'Microfibra respirável, camurça sintética e protetores de nódulos em polímero rígido',
                'sole_material' => 'Costura dupla reforçada em Kevlar nas áreas de maior tensão',
                'technical_specifications' => "Tipo: Meio Dedo (sensibilidade total ao toque e gatilho)\nFechamento: Faixa aderente ajustável no pulso\nPalma: Acolchoada para amortecer vibrações de tiro e manuseio de cordas\nGarantia: 3 meses",
                'weight' => 0.18,
                'height' => 4.0,
                'width' => 14.0,
                'length' => 20.0,
                'image' => 'https://images.unsplash.com/photo-1542282088-72c9c27ed0cd?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#0F172A', 'name' => 'Preto Tático'],
                    ['hex' => '#2D3A2D', 'name' => 'Verde Musgo'],
                    ['hex' => '#A38B6D', 'name' => 'Coyote Tan'],
                ],
            ],

            // ================= MUNIÇÕES & CO2 =================
            [
                'name' => 'Pacote Esferas BBs Airsoft Rossi 0.25g Alta Precisão 4.000 Unidades',
                'category_slug' => 'municoes-e-co2',
                'brand' => 'Rossi',
                'model_line' => 'Precision Grade 0.25g',
                'gender' => 'Unissex',
                'description' => "As esferas de 0.25g da Rossi são o equilíbrio perfeito entre velocidade e estabilidade balística em jogos abertos com vento. Passam por processo de polimento triplo que elimina qualquer bolha interna de ar ou rebarba na superfície externa.\n\nEvita entupimentos no cano de precisão e garante trajetória retilínea constante.",
                'price' => 89.90,
                'compare_price' => 115.00,
                'stock' => 80,
                'sizes' => ['Pacote 4.000 BBs (1.000g)'],
                'material' => 'Plástico ABS de altíssima densidade com polimento triplo espelhado',
                'sole_material' => 'Embalagem tipo saco stand-up pouch com zíper resselável',
                'technical_specifications' => "Gramatura: 0.25 g cada esfera\nDiâmetro: 5.95 mm ± 0.01 mm de tolerância extrema\nQuantidade: 4.000 esferas\nIndicação: Rifles AEG com velocidade de 350 a 420 FPS e Pistolas GBB\nCor: Branca de alta visibilidade no trajeto",
                'weight' => 1.05,
                'height' => 8.0,
                'width' => 18.0,
                'length' => 24.0,
                'image' => 'https://images.unsplash.com/photo-1585250004680-753f5052706e?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#FFFFFF', 'name' => 'Branco Pérola de Alta Visibilidade'],
                ],
            ],
            [
                'name' => 'Kit 10 Cilindros de Gás CO2 12g Descartáveis para Pistolas e Rifles',
                'category_slug' => 'municoes-e-co2',
                'brand' => 'Rossi / Crosman',
                'model_line' => 'CO2 Power 12g',
                'gender' => 'Unissex',
                'description' => "Cilindros de CO2 comprimido de 12 gramas fabricados sob os mais rigorosos testes de pressão internacional. Compatíveis com todas as marcas de pistolas e rifles de airsoft e carabinas de pressão que utilizam ampolas de 12g.\n\nGarante pressão consistente tiro após tiro com excelente autonomia.",
                'price' => 69.90,
                'compare_price' => 90.00,
                'stock' => 60,
                'sizes' => ['Kit com 10 Ampolas de 12g'],
                'material' => 'Cápsula de aço selada sem costura resistente a alta pressão',
                'sole_material' => 'Gás CO2 de pureza industrial sem resíduos ou umidade',
                'technical_specifications' => "Quantidade: 10 cilindros descartáveis de 12g cada\nRendimento médio: 50 a 80 disparos por cilindro (varia conforme o modelo e força do blowback)\nPeso total do kit: 420 g\nCompatibilidade: Pistolas e Rifles Airsoft calibre 6mm e 4.5mm",
                'weight' => 0.45,
                'height' => 5.0,
                'width' => 10.0,
                'length' => 15.0,
                'image' => 'https://images.unsplash.com/photo-1584281722572-8f191b7d598e?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#94A3B8', 'name' => 'Aço Metálico'],
                ],
            ],
            [
                'name' => 'Mira Holográfica Red Dot Tático 1x40 Trilho 11mm e 22mm com Retículo Bicolor',
                'category_slug' => 'municoes-e-co2',
                'brand' => 'Vector Optics / T-Eagle',
                'model_line' => 'Optics Reflex 1x40',
                'gender' => 'Unissex',
                'description' => "Mira eletrônica holográfica tipo Red Dot com campo de visão amplo de 40mm. Permite rápida aquisição de alvos em combates aproximados mantendo os dois olhos abertos.\n\nRetículo de ponto luminoso com opção de cor Vermelha ou Verde e 5 níveis de intensidade para jogos sob sol forte ou partidas noturnas.",
                'price' => 199.90,
                'compare_price' => 260.00,
                'stock' => 22,
                'sizes' => ['Montagem Universal Trilho 11mm e 20/22mm'],
                'material' => 'Corpo em alumínio aeronáutico com acabamento preto anodizado fosco',
                'sole_material' => 'Lentes ópticas multirrevestidas com tratamento anti-reflexo',
                'technical_specifications' => "Aumento: 1x (Sem magnificação)\nDiâmetro da Objetiva: 40 mm\nCores do Retículo: Vermelho (Red) e Verde (Green)\nIntensidade: 5 níveis para cada cor\nAlimentação: Bateria de lítio CR2032 (inclusa)\nPeso: 220 g\nAcompanha: Tampas protetoras emborrachadas e chave allen de montagem",
                'weight' => 0.35,
                'height' => 7.0,
                'width' => 8.0,
                'length' => 13.0,
                'image' => 'https://images.unsplash.com/photo-1595590424283-b8f17842773f?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#111827', 'name' => 'Preto Fosco Anodizado'],
                ],
            ],

            // ================= CUTELARIA & SOBREVIVÊNCIA =================
            [
                'name' => 'Faca Tática Militar Rambo com Bainha Rígida e Kit de Sobrevivência',
                'category_slug' => 'cutelaria-e-sobrevivencia',
                'brand' => 'Columbia Tactical',
                'model_line' => 'Heavy Duty Survival',
                'gender' => 'Unissex',
                'description' => "Faca militar de combate e sobrevivência com lâmina inteiriça Full Tang de 18 cm em aço inoxidável 440C de alta retenção de corte. O dorso conta com serra dupla capaz de cortar madeira e cordas grossas.\n\nAcompanha bainha rígida em polímero com pederneira iniciadora de fogo, bússola integrada e pedra de afiação rápida.",
                'price' => 149.90,
                'compare_price' => 199.00,
                'stock' => 30,
                'sizes' => ['Lâmina 18cm / Total 31cm'],
                'material' => 'Lâmina em Aço Inoxidável 440C com acabamento teflonado anti-corrosão',
                'sole_material' => 'Empunhadura ergonômica emborrachada antiderrapante com quebra-vidros na base',
                'technical_specifications' => "Comprimento da Lâmina: 18 cm\nEspessura da Lâmina: 4.5 mm\nComprimento Total: 31 cm\nPeso: 380 g com bainha\nAcessórios inclusos na bainha: Pederneira com apito de emergência + Pedra de amolar",
                'weight' => 0.45,
                'height' => 6.0,
                'width' => 9.0,
                'length' => 34.0,
                'image' => 'https://images.unsplash.com/photo-1593079831268-3381b0db4a77?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#1F2937', 'name' => 'Preto Tático Blackout'],
                    ['hex' => '#475569', 'name' => 'Lâmina Camuflada'],
                ],
            ],
            [
                'name' => 'Lanterna Tática Militar LED T6 Recarregável com Zoom 2000 Lumens',
                'category_slug' => 'cutelaria-e-sobrevivencia',
                'brand' => 'Tactical Force',
                'model_line' => 'Ultra Beam T6',
                'gender' => 'Unissex',
                'description' => "Lanterna tática profissional com emissor LED CREE T6 de altíssimo brilho (2.000 Lumens reais). Conta com foco telescópico com zoom regulável de 1x a 2000x capaz de alcançar objetos a mais de 300 metros de distância.\n\nResistente a respingos fortes de chuva e quedas em terrenos rochosos. Bateria recarregável via cabo USB.",
                'price' => 79.90,
                'compare_price' => 110.00,
                'stock' => 50,
                'sizes' => ['Tamanho Portátil (13.5cm a 15.5cm)'],
                'material' => 'Alumínio aeroespacial usinado com anodização militar tipo III',
                'sole_material' => 'Lente convexa temperada de alta transparência com anel de vedação o-ring',
                'technical_specifications' => "Potência: 2.000 Lumens\nModos de Operação: 5 (Forte, Médio, Fraco, Estroboscópico/SOS e Pisca Alerta)\nAlcance: Até 300 metros de iluminação focada\nAlimentação: Bateria Li-Ion 18650 recarregável (inclusa) ou 3 pilhas AAA com adaptador\nPeso: 150 g",
                'weight' => 0.3,
                'height' => 5.0,
                'width' => 10.0,
                'length' => 18.0,
                'image' => 'https://images.unsplash.com/photo-1508873696983-2df57046475a?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#0F172A', 'name' => 'Preto Anodizado Fosco'],
                ],
            ],
        ];

        // 5. Cadastrar todos os produtos com imagens, cores, variações e movimentação de estoque inicial
        foreach ($catalog as $item) {
            $cat = $categoryMap[$item['category_slug']] ?? null;

            $product = Product::updateOrCreate(
                ['name' => $item['name']],
                [
                    'slug' => Str::slug($item['name']),
                    'brand' => $item['brand'],
                    'model_line' => $item['model_line'],
                    'gender' => $item['gender'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'compare_price' => $item['compare_price'],
                    'stock' => $item['stock'],
                    'sizes' => $item['sizes'],
                    'material' => $item['material'],
                    'sole_material' => $item['sole_material'],
                    'weight' => $item['weight'],
                    'height' => $item['height'],
                    'width' => $item['width'],
                    'length' => $item['length'],
                    'technical_specifications' => $item['technical_specifications'],
                    'is_active' => true,
                ]
            );

            // Relacionar com a categoria
            if ($cat) {
                $product->categories()->sync([$cat->id]);
            }

            // Foto Principal
            $product->images()->delete();
            $product->images()->create([
                'path' => $item['image'],
                'position' => 0,
                'is_main' => true,
            ]);

            // Cadastrar Cores
            $product->colors()->delete();
            $colorModels = [];
            foreach ($item['colors'] as $color) {
                $colorModels[] = $product->colors()->create([
                    'hex_code' => $color['hex'],
                    'color_name' => $color['name'],
                ]);
            }

            // Cadastrar Variações (Tamanhos x Cores)
            $product->variants()->delete();
            $skuPrefix = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $item['brand']), 0, 3));
            $index = 1;

            $sizesList = $item['sizes'];
            foreach ($sizesList as $size) {
                foreach ($colorModels as $colorModel) {
                    $variantStock = max(2, (int) round($item['stock'] / (count($sizesList) * count($colorModels))));
                    $variantSku = $skuPrefix . '-' . $product->id . '-' . str_pad($index++, 2, '0', STR_PAD_LEFT);

                    $variant = $product->variants()->create([
                        'product_color_id' => $colorModel->id,
                        'color_name' => $colorModel->color_name,
                        'color_hex' => $colorModel->hex_code,
                        'size' => $size,
                        'stock' => $variantStock,
                        'sku' => $variantSku,
                    ]);

                    // Movimento de estoque auditável inicial
                    StockMovement::create([
                        'product_id' => $product->id,
                        'product_variant_id' => $variant->id,
                        'type' => 'initial_entry',
                        'quantity_before' => 0,
                        'quantity_delta' => $variantStock,
                        'quantity_after' => $variantStock,
                        'size' => $size,
                        'color_name' => $colorModel->color_name,
                        'reason' => 'Carga inicial do catálogo de Airsoft, Pescaria & Camping',
                    ]);
                }
            }

            // Sincronizar estoque total
            $product->syncStockFromVariants();
        }
    }
}

