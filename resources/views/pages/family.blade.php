<x-storefront-layout>
    <div class="bg-white dark:bg-slate-950 min-h-screen">
        <!-- Infinite Carousel Section -->
        <div class="relative w-full overflow-hidden bg-slate-900 py-12 border-b border-white/5">
            <div class="flex animate-infinite-scroll hover:[animation-play-state:paused] gap-6 px-3">
                @php $photos = ['0.png', '1.png', '2.png', '3.png', '4.png']; @endphp
                @foreach(array_merge($photos, $photos, $photos) as $photo)
                    <div
                        class="flex-none w-48 md:w-80 aspect-square rounded-2xl overflow-hidden shadow-2xl border border-white/10 group">
                        <img src="{{ asset('img/familia/' . $photo) }}" alt="Família Studart"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    </div>
                @endforeach
            </div>

            <style>
                @keyframes infinite-scroll {
                    from {
                        transform: translateX(0);
                    }

                    to {
                        transform: translateX(calc(-100% / 3));
                    }
                }

                .animate-infinite-scroll {
                    display: flex;
                    width: max-content;
                    animation: infinite-scroll 60s linear infinite;
                }
            </style>

            <div
                class="absolute inset-y-0 left-0 w-32 bg-gradient-to-r from-slate-900 to-transparent z-10 pointer-events-none">
            </div>
            <div
                class="absolute inset-y-0 right-0 w-32 bg-gradient-to-l from-slate-900 to-transparent z-10 pointer-events-none">
            </div>
        </div>

        <!-- Header -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-slate-900 dark:text-white mb-6 uppercase tracking-tight">
                Nossa História</h1>
            <p
                class="text-xl md:text-2xl text-slate-600 dark:text-slate-300 italic font-light max-w-3xl mx-auto leading-relaxed px-4">
                "Aqui tem muita história pra contar..."
                <span class="block text-sm not-italic mt-6 font-bold uppercase tracking-[0.3em] text-primary">Por Hugo
                    Studart</span>
            </p>
        </div>

        <!-- Content -->
        <div class="max-w-4xl mx-auto px-6 py-20 pb-32">
            <div class="prose prose-slate prose-lg dark:prose-invert max-w-none">
                <p
                    class="lead text-xl text-slate-700 dark:text-slate-200 font-medium leading-relaxed mb-12 border-l-4 border-primary/30 pl-8">
                    A Casa Studart Cachaçaria foi criada com o objetivo de resgatar uma tradição dos meus ancestrais em
                    destilados, que teve início em meados do Século XVI, quando William Thomas Studart começou a
                    produzir e a comercializar uísque na pequena cidade de Dunbar, sudeste da Escócia, até chegar a meu
                    bisavô, Carlos Guilherme Gordon Studart, de Fortaleza. Carlos Guilherme viveu até os 103 anos,
                    lúcido e ativo, tomando diariamente duas doses de uísque escocês ou uma generosa talagada da cachaça
                    artesanal que produzia em seu alambique em Aracati, Ceará. Seus filhos e netos preferiam o vinho do
                    Porto. Coube a mim, Carlos Hugo, assim batizado em homenagem a meu bisavô, prosseguir com uma
                    tradição familiar de 400 anos, interrompida em 1965 com o falecimento de Carlos Guilherme.
                </p>

                <h2
                    class="text-3xl font-bold text-slate-900 dark:text-white mb-8 border-b border-primary/10 pb-4 inline-block">
                    Origem Medieval</h2>
                <p class="text-slate-600 dark:text-slate-400 mb-8">
                    A família Studart tem origem em Northumberland, a região da Grã-Bretanha que separa a
                    Inglaterra da Escócia, cenário das primeiras invasões vikings e, também, das Crônicas
                    Saxônicas do rei Alfredo, O Grande. O fundador da família, Vlfus Studhlerde, teria chegado a
                    Northumberland em 1066 no Exército de Guilherme, O Conquistador, Duque da Normandia, França,
                    que se tornou o primeiro rei normando da Inglaterra. O sobrenome é derivado de Stud, cavalo
                    em inglês arcaico, com hlerde, rebanho. Acredita-se que Vlfus Studhlerde fosse criador de
                    cavalos. Depois o sobrenome derivou para Studart, ou seja, arte de montar ou de criar
                    cavalos, com variadas corruptelas.
                </p>

                <p class="text-slate-600 dark:text-slate-400 mb-12">
                    Geoffrey Stodhurd foi listado no Curia Regis Rolls para Northumberland em 1219. Mais tarde, em 1286,
                    John the Stodhirde foi encontrado no Assize Rolls para Cheshire; já em 1332, Richard le Stodehard
                    foi encontrado nos registros de Yorkshire. Em York, apareceram registrados os nomes de Thomas
                    Stoderd em 1481 e, em 1482, o de John Studart. Com o passar dos séculos, os descendentes do normando
                    Vlfus, usando sobrenomes com variadas grafias, especialmente Studart e Stoodart, foram se
                    estabelecendo nas cidades de York, Alnwick e Bamburg, em Northumberland, como também no sul da
                    Escócia, sobretudo em Edimburg e imediações.
                </p>

                <div
                    class="bg-slate-50 dark:bg-slate-900/50 p-10 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 mb-16 shadow-inner">
                    <p class="text-slate-600 dark:text-slate-400 italic mb-0 leading-loose">
                        A grafia Stoodart começou a aparecer nos registros das paróquias escocesas no final do Século
                        XVI, provavelmente por conta da implacável perseguição da rainha Elizabeth I, da Inglaterra, aos
                        parentes de sua prima Mary Stuart, rainha da Escócia, decapitada em 1589. Foi quando muitos
                        Studart da Escócia começaram a mudar a grafia do nome para Stoodart, decerto para não serem
                        confundidos com os membros da família Stuart.
                    </p>
                </div>

                <h2
                    class="text-3xl font-bold text-slate-900 dark:text-white mb-8 border-b border-primary/10 pb-4 inline-block">
                    A Água da Vida</h2>
                <p class="text-slate-600 dark:text-slate-400 mb-12">
                    Em fins do Século XV, os escoceses usaram malte de cevada para destilar uma bebida usada pelos
                    médicos árabes medievais, batizada pelos monges beneditinos de aqua vitae, água da vida. Passaram a
                    chamá-la de uisge beatha, água da vida em gaélico. O néctar seria amplamente difundido na Escócia a
                    partir de 1494, quando o rei James IV ordenou a fabricação de 1.500 garrafas de uisge.
                </p>

                <p class="text-slate-600 dark:text-slate-400 mb-12">
                    Em meados do Século XVI, membros escoceses da família Studart começaram a produzir e a comercializar
                    o novo destilado. Há um registro de 1568, da Cúria de Edimburg, sobre William Thomas Studart,
                    comerciante de uísque em Dunbar, cidade no litoral sul da Escócia. Alguns Studart do sul da Escócia
                    produziram ou comercializaram uisge por dois séculos até que, em 1746, ocorreu a antológica Batalha
                    de Culloden.
                </p>

                <h2
                    class="text-3xl font-bold text-slate-900 dark:text-white mb-8 border-b border-primary/10 pb-4 inline-block">
                    Escoceses em Portugal</h2>
                <p class="text-slate-600 dark:text-slate-400 mb-12">
                    Após a Batalha de Culloden, o escocês Edward Gordon Studart conseguiu fugir dos ingleses e
                    chegar à cidade do Porto, Portugal. Estabeleceu-se como comerciante de vinho do Porto. Foi
                    lá que, em 1754, nasceu seu filho Amos, que herdou os negócios do pai. Com o tempo, Amos
                    Studart foi criando fortes laços com a Inglaterra, sobretudo com os industriais da emergente
                    Manchester.
                </p>

                <p class="text-slate-600 dark:text-slate-400 mb-12">
                    John William Smith Studart, nascido em 1828 em Lisboa, desembarcaria em Fortaleza em 1840, seguindo
                    os passos do meio-irmão, o Barão José Smith de Vasconcelos. John William casou-se com Leonísia
                    Castro Barbosa, cuja família possuía usina de açúcar em Aracati, onde era produzida uma aguardente
                    de cana. Seu primogênito, Guilherme Chambly Studart, o Barão de Studart, foi o principal líder da
                    campanha que acabou com a escravidão no Ceará quatro anos antes da Lei Áurea.
                </p>

                <h2
                    class="text-3xl font-bold text-slate-900 dark:text-white mb-8 border-b border-primary/10 pb-4 inline-block">
                    As Extravagâncias de Carlos</h2>
                <p class="text-slate-600 dark:text-slate-400 mb-8">
                    Mas foi o sexto filho de John William, Carlos Guilherme Gordon Studart, meu bisavô, quem herdou o
                    gosto pelos destilados e, sobretudo, pelos prazeres da vida. Era farmacêutico de formação, mas tinha
                    forte vocação para os negócios. Em 1891, após um golpe de Estado, teve que fugir de Fortaleza em um
                    de seus cavalos de corrida para não ser degolado. Escondeu-se na fazenda da família materna em
                    Aracati, onde começou a aperfeiçoar o processo de produção de cachaça artesanal.
                </p>

                <div class="bg-primary/5 border-l-4 border-primary p-12 py-14 mb-16 rounded-r-[3rem] shadow-sm">
                    <p class="text-xl text-slate-800 dark:text-slate-200 leading-relaxed italic mb-0 font-light">
                        "Reza a lenda em família que Carlos Guilherme... decidiu 'morrer em grande estilo'.
                        Contratou duas coristas no Quartier Latin, voltou bebendo champagne e fumando charutos.
                        Viveu até os 103 anos, alegre e lúcido, saboreando bons destilados."
                    </p>
                </div>

                <p
                    class="text-slate-700 dark:text-slate-200 leading-relaxed text-xl font-medium pt-12 border-t border-slate-100 dark:border-slate-800">
                    Coube a mim, Carlos Hugo, resgatar a tradição pelos destilados a partir de um forte desejo
                    imanente pela alquimia, a ideia de transformar a cachaça bruta em ouro com sabor e odor. E
                    que os espíritos do normando Vlfus, do escocês William Thomas e do extravagante cearense
                    Carlos Guilherme estejam sempre a me inspirar.
                </p>
            </div>
        </div>
    </div>
</x-storefront-layout>