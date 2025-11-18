<script setup lang="ts">
import BlurReveal from '@/components/ui/blur-reveal/BlurReveal.vue';
import { dashboard, login } from '@/routes';
import { Link } from '@inertiajs/vue3';
import { Users, FileText, BarChart, Leaf } from 'lucide-vue-next';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <div :class="['bg-gray-900 text-gray-100 min-h-screen transition-colors duration-500']">

        <header class="sticky top-0 z-50  before:content-[''] before:absolute before:inset-0 before:bg-gray-900/95 before:backdrop-blur-sm 
                    transition-colors duration-500">
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <a href="#" class="flex items-center text-2xl font-extrabold tracking-tight text-white">
                    <Leaf class="w-7 h-7 mr-1 text-green-500"></Leaf>
                    Ageno
                </a>

                <nav class="hidden md:flex space-x-8 font-medium text-gray-300">
                    <!-- <a href="#features" class="hover:text-green-400 transition duration-150">Fonctionnalités</a>
                    <a href="#pricing" class="hover:text-green-400 transition duration-150">Tarification</a>
                    <a href="#contact" class="hover:text-green-400 transition duration-150">Contact</a> -->
                </nav>

                <div class="flex items-center space-x-4">
                    <Link v-if="$page.props.auth.user" :href="dashboard()"
                        class="text-sm font-semibold text-gray-300 hover:text-green-400 transition duration-150">
                    Tableau de board
                    </Link>
                    <Link v-else :href="login()"
                        class="text-sm font-semibold text-gray-300 hover:text-green-400 transition duration-150">
                    Se connecter
                    </Link>
                    <!-- <Link v-if="canRegister" :href="login()"
                        class="hidden sm:block px-4 py-2 bg-green-600 text-white rounded-lg font-bold hover:bg-green-500 transition duration-150 shadow-md">
                    S'inscrire
                    </Link> -->
                </div>
            </div>
        </header>

        <main>
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-20">
                <div class="lg:grid lg:grid-cols-12 lg:gap-12 items-center">
                    <div class="lg:col-span-6 text-center lg:text-left">
                        <span
                            class="animate-in fade-in slide-in-from-top-12 delay-1200 duration-500 fill-mode-both inline-block text-sm font-semibold text-green-400 bg-green-900/50 px-3 py-1 rounded-full mb-4 border border-green-700">
                            Nouvelle Version Disponible
                        </span>
                        <ClientOnly>
                            <BlurReveal :delay="0.2" :duration="0.75">
                                <h1
                                    class="text-5xl sm:text-6xl lg:text-7xl font-extrabold leading-tight tracking-tighter mb-6 text-white">
                                    Gérez vos clients <br class="hidden lg:inline" />
                                    <span class="text-green-500">et factures</span> simplement.
                                </h1>
                            </BlurReveal>
                            <BlurReveal :delay="3" :duration="1.2">
                                <p class="mt-4 text-lg sm:text-xl text-gray-400 max-w-xl mx-auto lg:mx-0">
                                    La plateforme moderne conçue pour rationaliser vos relations clients et votre
                                    facturation,
                                    vous donnant un contrôle total.
                                </p>
                            </BlurReveal>
                        </ClientOnly>


                        <div
                            class=" mt-10 flex flex-col sm:flex-row justify-center lg:justify-start space-y-4 sm:space-y-0 sm:space-x-4 animate-in fade-in slide-in-from-top-12 delay-800 duration-500 fill-mode-both">
                            <Link :href="login()"
                                class="px-8 py-3 rounded-xl text-lg font-semibold border-2 border-green-600 bg-green-600 text-white shadow-lg hover:bg-green-700 transition duration-300">
                            Commencer
                            </Link>
                            <button
                                class="px-8 py-3 rounded-xl text-lg font-semibold border-2 border-gray-600 text-gray-300 hover:bg-gray-800 transition duration-300">
                                Voir la démo
                            </button>
                        </div>
                    </div>

                    <div class="lg:col-span-6 mt-16 lg:mt-0 relative">
                        <div
                            class="bg-gray-800 h-96 w-full rounded-2xl border-4 border-gray-700 flex items-center justify-center shadow-2xl shadow-black/50 overflow-hidden">
                            <span class="text-gray-500 text-xl font-medium">Capture d'écran de l'interface</span>
                        </div>
                    </div>
                </div>
            </section>

            <section id="features" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
                <h2 class="text-4xl font-extrabold text-center mb-6 text-white">
                    Fonctionnalités conçues pour la <span class="text-green-500">croissance</span>
                </h2>
                <p class="text-xl text-center text-gray-400 mb-20 max-w-3xl mx-auto">
                    Centralisez, automatisez et analysez vos processus métier pour une efficacité maximale.
                </p>

                <div class="grid md:grid-cols-2 gap-x-16 gap-y-16">

                    <div class="flex space-x-6">
                        <div class="flex-shrink-0 pt-1">
                            <Users class="w-8 h-8 text-green-500" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-3 text-white">Centre Client</h3>
                            <p class="text-gray-400">Centralisez toutes les données clients, l'historique
                                des communications et les contrats dans un espace unique et consultable. Ne laissez
                                aucun
                                détail vous échapper.</p>
                        </div>
                    </div>

                    <div class="flex space-x-6">
                        <div class="flex-shrink-0 pt-1">
                            <FileText class="w-8 h-8 text-green-500" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-3 text-white">Facturation Automatisée</h3>
                            <p class="text-gray-400">Générez instantanément des factures professionnelles
                                à votre image. Planifiez les factures récurrentes et envoyez des rappels de paiement
                                automatiques aux clients.</p>
                        </div>
                    </div>

                    <div class="flex space-x-6">
                        <div class="flex-shrink-0 pt-1">
                            <BarChart class="w-8 h-8 text-green-500" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-3 text-white">Aperçus Intelligents</h3>
                            <p class="text-gray-400">Analysez les tendances de paiement, suivez la
                                rentabilité des clients et visualisez les flux de trésorerie grâce à des tableaux de
                                bord
                                financiers puissants et faciles à lire.</p>
                        </div>
                    </div>

                    <div class="flex space-x-6">
                        <div class="flex-shrink-0 pt-1">
                            <Leaf class="w-8 h-8 text-green-500" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-3 text-white">Support Dédié</h3>
                            <p class="text-gray-400">Bénéficiez d'un support client réactif et personnalisé par une
                                équipe
                                qui comprend vos défis. Nous sommes là pour vous aider à grandir.</p>
                        </div>
                    </div>

                </div>
            </section>

            <section id="contact" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
                <div class="bg-green-900/50 p-10 sm:p-16 rounded-3xl shadow-xl border border-green-700/50">
                    <h2 class="text-3xl sm:text-4xl font-extrabold mb-4 text-white drop-shadow-sm">
                        Prêt pour une Gestion <span class="text-green-300">Simplifiée</span> ?
                    </h2>
                    <p class="text-lg mb-10 text-gray-300 drop-shadow-sm">
                        Rejoignez des milliers d'entreprises qui simplifient leurs opérations avec Ageno dès
                        aujourd'hui.
                    </p>
                    <Link :href="login()"
                        class="px-10 py-4 rounded-xl text-xl font-bold bg-green-500 text-white shadow-2xl hover:scale-[1.02] hover:bg-green-600 transition duration-300">
                    Démarrer
                    </Link>
                </div>
            </section>

        </main>

        <footer class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 border-t border-gray-800">
            <div class="flex flex-col sm:flex-row justify-between items-center text-sm text-gray-500">
                <p>&copy; 2025 Ageno. Tous droits réservés.</p>
                <div class="flex space-x-4 mt-4 sm:mt-0">
                    <a href="#" class="hover:text-green-500 transition duration-150">Politique de Confidentialité</a>
                    <a href="#" class="hover:text-green-500 transition duration-150">Conditions d'Utilisation</a>
                </div>
            </div>
        </footer>
    </div>
</template>