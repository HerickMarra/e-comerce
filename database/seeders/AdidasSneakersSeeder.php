<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdidasSneakersSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Garantir que a Categoria Adidas existe
        $adidasCategory = Category::firstOrCreate(
            ['name' => 'Adidas'],
            [
                'slug' => 'adidas',
                'description' => 'Tênis de corrida Adidas com tecnologias de amortecimento BOOST, Lightstrike Pro e Dreamstrike+.',
                'is_active' => true,
            ]
        );

        // 2. Limpar produtos anteriores para deixar exclusivamente Adidas
        $oldProducts = Product::all();
        foreach ($oldProducts as $old) {
            $old->images()->delete();
            $old->colors()->delete();
            $old->categories()->detach();
            $old->delete();
        }

        // 3. Catálogo de Tênis de Corrida Adidas
        $sneakers = [
            [
                'name' => 'Tênis Adidas Ultraboost Light',
                'brand' => 'Adidas',
                'model_line' => 'Ultraboost Running',
                'gender' => 'Unissex',
                'description' => "Experimente energia épica a cada passada com o novo Ultraboost Light, o Ultraboost mais leve de todos os tempos. A mágica está na entressola Light BOOST, uma nova geração do amortecimento adidas BOOST com cápsulas de energia que proporcionam resposta e retorno superiores.\n\nO cabedal adidas PRIMEKNIT+ envolve os pés com suporte direcionado e ventilação estratégica nos pontos de maior aquecimento durante a corrida.",
                'price' => 899.90,
                'compare_price' => 1199.90,
                'stock' => 18,
                'sizes' => ['38', '39', '40', '41', '42', '43', '44'],
                'material' => 'Cabedal têxtil adidas PRIMEKNIT+ respirável com ajuste elástico',
                'sole_material' => 'Entressola Light BOOST com borracha Continental™ Better Rubber',
                'technical_specifications' => "Tipo de Pisada: Neutra\nDrop da Entressola: 10 mm (calcanhar: 22 mm / antepé: 12 mm)\nPeso Aproximado: 293 g (tamanho 40)\nIndicação: Corridas diárias, meia-maratona e maratona\nFechamento: Cadarço tradicional com reforço no mediopé\nGarantia do Fabricante: Contra defeito de fabricação",
                'weight' => 0.850,
                'height' => 12.0,
                'width' => 22.0,
                'length' => 34.0,
                'image' => 'https://images.unsplash.com/photo-1587563871167-1ee9c731aefb?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#000000', 'name' => 'Core Black'],
                    ['hex' => '#FFFFFF', 'name' => 'Cloud White'],
                    ['hex' => '#0284C7', 'name' => 'Lucid Blue']
                ],
            ],
            [
                'name' => 'Tênis Adidas Adizero Adios Pro 3',
                'brand' => 'Adidas',
                'model_line' => 'Adizero Racing / Maratona',
                'gender' => 'Unissex',
                'description' => "O ápice dos produtos de corrida da família Adizero. Criado com e para atletas para alcançar resultados extraordinários no dia da prova. Desenvolvido para máxima eficiência na corrida de longa distância com hastes de carbono ENERGYRODS 2.0 que limitam a perda de energia.\n\nDuas camadas de espuma Lightstrike Pro resistente proporcionam amortecimento premium para você quebrar seus próprios recordes pessoais.",
                'price' => 1499.90,
                'compare_price' => 1899.90,
                'stock' => 12,
                'sizes' => ['39', '40', '41', '42', '43', '44'],
                'material' => 'Cabedal sintético ultraleve Celermesh 2.0 respirável',
                'sole_material' => 'Lightstrike Pro duplo com hastes de carbono ENERGYRODS e borracha Continental™',
                'technical_specifications' => "Tipo de Pisada: Neutra / Corrida de Performance\nDrop da Entressola: 6.5 mm (calcanhar: 39.5 mm / antepé: 33 mm)\nPeso Aproximado: 215 g (tamanho 40)\nIndicação: Competições de 21k, maratona de 42k e provas de rua\nPlaca de Propulsão: Hastes de carbono ENERGYRODS 2.0\nGarantia do Fabricante: Contra defeito de fabricação",
                'weight' => 0.780,
                'height' => 12.0,
                'width' => 22.0,
                'length' => 34.0,
                'image' => 'https://images.unsplash.com/photo-1571008887538-b36bb32f4571?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#EA580C', 'name' => 'Solar Red'],
                    ['hex' => '#18181B', 'name' => 'Triple Black'],
                    ['hex' => '#3B82F6', 'name' => 'Wonder Blue']
                ],
            ],
            [
                'name' => 'Tênis Adidas Adizero Boston 12',
                'brand' => 'Adidas',
                'model_line' => 'Adizero Treino Rápido',
                'gender' => 'Masculino',
                'description' => "A Maratona de Boston é uma corrida lendária, e o Boston 12 foi criado para trazer a sensação de dia de prova para os seus treinos diários de velocidade e rodagens de ritmo.\n\nA tecnologia ENERGYRODS 2.0 de fibra de vidro limita a perda de energia sob os pés, aliada à combinação de amortecimento ultraleve Lightstrike Pro e a nova e durável espuma Lightstrike 2.0.",
                'price' => 799.90,
                'compare_price' => 999.90,
                'stock' => 20,
                'sizes' => ['38', '39', '40', '41', '42', '43', '44'],
                'material' => 'Mesh leve projetado em camadas com suporte no mediopé',
                'sole_material' => 'Lightstrike Pro + Lightstrike 2.0 com solado Continental™ Rubber',
                'technical_specifications' => "Tipo de Pisada: Neutra\nDrop da Entressola: 7 mm (calcanhar: 38 mm / antepé: 31 mm)\nPeso Aproximado: 260 g (tamanho 41)\nIndicação: Treinos intervalados, tempo runs e meias-maratonas\nGarantia do Fabricante: Oficial Adidas",
                'weight' => 0.820,
                'height' => 12.0,
                'width' => 22.0,
                'length' => 34.0,
                'image' => 'https://images.unsplash.com/photo-1518002171953-a080ee817e1f?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#F4F4F5', 'name' => 'Cloud White'],
                    ['hex' => '#F97316', 'name' => 'Wonder Clay'],
                    ['hex' => '#0F172A', 'name' => 'Dark Marine']
                ],
            ],
            [
                'name' => 'Tênis Adidas Supernova Rise',
                'brand' => 'Adidas',
                'model_line' => 'Supernova Diário',
                'gender' => 'Unissex',
                'description' => "Projetado para oferecer o máximo de conforto em cada quilômetro da sua rotina de treinos. O Supernova Rise estreia a superespuma Dreamstrike+, ajustada especificamente para amortecer o impacto no asfalto e devolver energia com maciez inigualável.\n\nO sistema Support Rods de densidade mais alta garante uma transição fluida e estável do calcanhar até a ponta dos dedos.",
                'price' => 649.90,
                'compare_price' => 799.90,
                'stock' => 24,
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44'],
                'material' => 'Malha sanduíche engineered mesh respirável e gola acolchoada',
                'sole_material' => 'Superespuma Dreamstrike+ com solado Adiwear durável',
                'technical_specifications' => "Tipo de Pisada: Neutra\nDrop da Entressola: 10 mm (calcanhar: 36 mm / antepé: 26 mm)\nPeso Aproximado: 277 g (tamanho 40)\nIndicação: Treinos diários, rodagens regenerativas e esteira\nGarantia do Fabricante: Contra defeito de fabricação",
                'weight' => 0.820,
                'height' => 12.0,
                'width' => 22.0,
                'length' => 34.0,
                'image' => 'https://images.unsplash.com/photo-1582588678413-dbf45f4823e9?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#1D4ED8', 'name' => 'Lucid Blue'],
                    ['hex' => '#FFFFFF', 'name' => 'Triple White'],
                    ['hex' => '#10B981', 'name' => 'Semi Green Spark']
                ],
            ],
            [
                'name' => 'Tênis Adidas Adistar 3',
                'brand' => 'Adidas',
                'model_line' => 'Adistar Long Distance',
                'gender' => 'Masculino',
                'description' => "Projetado para suas corridas mais longas e cadenciadas. O Adistar 3 combina uma plataforma ampla e estável com a espuma de dupla densidade REPETITOR 2.0, absorvendo o impacto repetitivo dos longões e preservando as articulações.\n\nA geometria rocker impulsiona a pisada para frente com ritmo constante, quilômetro após quilômetro.",
                'price' => 749.90,
                'compare_price' => 899.90,
                'stock' => 15,
                'sizes' => ['39', '40', '41', '42', '43', '44', '45'],
                'material' => 'Monomesh técnico respirável com estrutura interna de suporte',
                'sole_material' => 'Espuma REPETITOR 2.0 com solado de borracha Adiwear de alta abrasão',
                'technical_specifications' => "Tipo de Pisada: Neutra / Apoio de Calcanhar\nDrop da Entressola: 6 mm (calcanhar: 38 mm / antepé: 32 mm)\nPeso Aproximado: 310 g (tamanho 41)\nIndicação: Longões de fim de semana, treinos de base e corredores pesados\nGarantia do Fabricante: Oficial Adidas",
                'weight' => 0.880,
                'height' => 12.5,
                'width' => 22.0,
                'length' => 34.0,
                'image' => 'https://images.unsplash.com/photo-1527010154944-f2241763d806?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#2563EB', 'name' => 'Cobalt Blue'],
                    ['hex' => '#71717A', 'name' => 'Zinc Grey']
                ],
            ],
            [
                'name' => 'Tênis Adidas Duramo Speed',
                'brand' => 'Adidas',
                'model_line' => 'Duramo Speed Performance',
                'gender' => 'Unissex',
                'description' => "Dê o próximo passo na sua jornada de corrida. O Duramo Speed traz a leveza da entressola LIGHTMOTION de ponta a ponta, proporcionando resposta rápida e amortecimento dinâmico tanto para treinos de 5 km e 10 km quanto para o uso diário em academia e esteira.\n\nO cabedal em malha mantém os pés ventilados mesmo sob sol intenso.",
                'price' => 399.90,
                'compare_price' => 499.90,
                'stock' => 28,
                'sizes' => ['37', '38', '39', '40', '41', '42', '43'],
                'material' => 'Malha engineered mesh leve com reforços sintéticos sem costura',
                'sole_material' => 'Espuma LIGHTMOTION com solado de borracha durável Adiwear',
                'technical_specifications' => "Tipo de Pisada: Neutra\nDrop da Entressola: 9.5 mm\nPeso Aproximado: 255 g (tamanho 40)\nIndicação: 5K, 10K, treinos na esteira, academia e caminhada rápida\nGarantia do Fabricante: Contra defeito de fabricação",
                'weight' => 0.800,
                'height' => 12.0,
                'width' => 22.0,
                'length' => 34.0,
                'image' => 'https://images.unsplash.com/photo-1556906781-9a412961c28c?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#DC2626', 'name' => 'Solar Red'],
                    ['hex' => '#FFFFFF', 'name' => 'Cloud White'],
                    ['hex' => '#1E293B', 'name' => 'Night Navy']
                ],
            ],
            [
                'name' => 'Tênis Adidas 4DFWD 3 Running',
                'brand' => 'Adidas',
                'model_line' => 'Inovação 4D Impressa em 3D',
                'gender' => 'Unissex',
                'description' => "O futuro da corrida urbana. A entressola adidas 4D em treliça foi desenvolvida a partir de anos de dados de atletas e impressa em 3D. Quando seus pés tocam o chão, as células em formato gravata borboleta comprimem para frente, transformando o impacto vertical em propulsão horizontal contínua.\n\nO cabedal adidas PRIMEKNIT envolve o pé com ajuste firme e suporte milimétrico.",
                'price' => 1199.90,
                'compare_price' => 1599.90,
                'stock' => 10,
                'sizes' => ['38', '39', '40', '41', '42', '43'],
                'material' => 'Cabedal têxtil adidas PRIMEKNIT com contenção anatômica',
                'sole_material' => 'Entressola treliçada impressa em 3D 4DFWD com solado Continental™',
                'technical_specifications' => "Tipo de Pisada: Neutra / Inovação Tecnológica\nDrop da Entressola: 11.3 mm (calcanhar: 32 mm / antepé: 20.7 mm)\nPeso Aproximado: 340 g (tamanho 40)\nIndicação: Corridas urbanas, 10k, conforto responsivo de última geração\nGarantia do Fabricante: Oficial Adidas",
                'weight' => 0.900,
                'height' => 13.0,
                'width' => 22.0,
                'length' => 34.0,
                'image' => 'https://images.unsplash.com/photo-1511556532299-8f662fc26c06?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#000000', 'name' => 'Core Black'],
                    ['hex' => '#475569', 'name' => 'Carbon Grey'],
                    ['hex' => '#E2E8F0', 'name' => 'Wonder Silver']
                ],
            ],
            [
                'name' => 'Tênis Adidas Galaxy 6 Running',
                'brand' => 'Adidas',
                'model_line' => 'Galaxy Treino & Conforto',
                'gender' => 'Feminino',
                'description' => "Coloque este tênis nos pés e sinta o conforto imediato a cada passada. A entressola macia Cloudfoam amortece a pisada desde o primeiro minuto, enquanto o cabedal em malha arejada mantém o fluxo de ar constante.\n\nPerfeito para corredoras iniciantes, caminhadas ao ar livre ou para encarar a rotina agitada com conforto ininterrupto.",
                'price' => 279.90,
                'compare_price' => 349.90,
                'stock' => 25,
                'sizes' => ['34', '35', '36', '37', '38', '39', '40'],
                'material' => 'Cabedal têxtil em malha respirável contendo 50% de materiais reciclados',
                'sole_material' => 'Entressola Cloudfoam com palmilha OrthoLite® e solado de borracha',
                'technical_specifications' => "Tipo de Pisada: Neutra\nDrop da Entressola: 10 mm\nPeso Aproximado: 240 g (tamanho 36)\nIndicação: Corridas leves, caminhada, esteira e treino na academia\nGarantia do Fabricante: Contra defeito de fabricação",
                'weight' => 0.750,
                'height' => 11.5,
                'width' => 21.0,
                'length' => 33.0,
                'image' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?auto=format&fit=crop&q=80&w=1000',
                'colors' => [
                    ['hex' => '#F472B6', 'name' => 'Lilac Pink'],
                    ['hex' => '#FFFFFF', 'name' => 'Cloud White'],
                    ['hex' => '#94A3B8', 'name' => 'Halo Grey']
                ],
            ],
        ];

        foreach ($sneakers as $data) {
            $slug = Str::slug($data['name']);
            
            $product = Product::create([
                'name' => $data['name'],
                'slug' => $slug,
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
            ]);

            // Vincular EXCLUSIVAMENTE à categoria Adidas
            $product->categories()->attach($adidasCategory->id);

            // Imagem principal do tênis
            $product->images()->create([
                'path' => $data['image'],
                'position' => 0,
                'is_main' => true,
            ]);

            // Cores disponíveis
            foreach ($data['colors'] as $color) {
                $product->colors()->create([
                    'hex_code' => $color['hex'],
                    'color_name' => $color['name'],
                ]);
            }
        }
    }
}

