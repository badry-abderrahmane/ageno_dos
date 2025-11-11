<script setup lang="ts">
import { dashboard, login } from '@/routes';
import { Link } from '@inertiajs/vue3';
import { Flame } from 'lucide-vue-next';

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
    <div :class="['bg-secondary text-primary min-h-screen transition-colors duration-500']">

        <!-- Header / Navigation -->
        <header
            class="sticky top-0 z-50 bg-secondary/95 backdrop-blur-sm border-b border-gray-200 transition-colors duration-500">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <!-- Logo / App Name -->
                <a href="#" class="flex items-center text-2xl font-extrabold tracking-tight">
                    <Flame></Flame>
                    Ageno
                </a>

                <!-- Desktop Links -->
                <nav class="hidden md:flex space-x-8 font-medium">
                    <!-- <a href="#features" class="hover:text-gray-600 dark:hover:text-gray-400">Fonctionnalités</a> -->
                    <!-- <a href="#pricing" class="hover:text-gray-600 dark:hover:text-gray-400">Tarification</a> -->
                    <!-- <a href="#contact" class="hover:text-gray-600 dark:hover:text-gray-400">Contact</a> -->
                </nav>

                <!-- Actions and Theme Toggle -->
                <div class="flex items-center space-x-4">
                    <Link v-if="$page.props.auth.user" :href="dashboard()"
                        class="text-sm font-semibold hover:underline">Tableau de board</Link>
                    <Link :href="login()" class="text-sm font-semibold hover:underline">Se connecter</Link>
                </div>
            </div>
        </header>

        <main>
            <!-- 1. Hero Section -->
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-32 text-center">
                <h1 class="text-6xl sm:text-7xl lg:text-8xl font-extrabold leading-tight tracking-tighter mb-6">
                    Ageno
                    <span class="block text-gray-700  mt-4">Gestion intelligente.</span>
                </h1>
                <p class="mt-4 text-xl sm:text-2xl text-gray-600  max-w-3xl mx-auto">
                    La plateforme SaaS moderne conçue pour rationaliser vos relations clients et votre facturation, vous
                    donnant un contrôle total.
                </p>

                <div class="mt-10 flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <button
                        class="cta-button bg-primary text-secondary  px-8 py-3 rounded-xl text-lg font-semibold border-2 border-primary  shadow-lg">
                        Commencer l'essai gratuit
                    </button>
                    <button
                        class="cta-button bg-transparent border-2 border-gray-400 text-primary  px-8 py-3 rounded-xl text-lg font-semibold hover:bg-gray-100 ">
                        Voir la démo
                    </button>
                </div>
            </section>

            <!-- 2. Features Section -->
            <section id="features"
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 bg-gray-50  rounded-3xl shadow-xl">
                <h2 class="text-4xl font-bold text-center mb-16">Fonctionnalités conçues pour la croissance</h2>

                <div class="grid md:grid-cols-3 gap-12">

                    <!-- Feature 1: Client Management -->
                    <div class="p-6 rounded-xl border border-gray-200 hover:shadow-2xl transition duration-500">
                        <!-- SVG Illustration: Person with a contact book -->
                        <div class="h-32 w-full flex justify-center items-center mb-6">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                class="w-24 h-24 text-primary ">
                                <path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2z"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M12 16a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M15.5 17.1c-.2-.6-.5-1.1-1-1.5-.7-.6-1.5-1-2.5-1s-1.8.4-2.5 1c-.5.4-.8.9-1 1.5M8 12h8"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M6 8V6.5c0-.8.7-1.5 1.5-1.5h10c.8 0 1.5.7 1.5 1.5V8M9 4V2.5m6 0V4"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Centre Client</h3>
                        <p class="text-gray-600 ">Centralisez toutes les données clients, l'historique
                            des communications et les contrats dans un espace unique et consultable. Ne laissez aucun
                            détail vous échapper.</p>
                    </div>

                    <!-- Feature 2: Invoicing & Billing -->
                    <div class="p-6 rounded-xl border border-gray-200  hover:shadow-2xl transition duration-500">
                        <!-- SVG Illustration: Invoice with a checkmark -->
                        <div class="h-32 w-full flex justify-center items-center mb-6">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                class="w-24 h-24 text-primary ">
                                <path d="M14 2H6C5.4 2 5 2.4 5 3v18c0 .6.4 1 1 1h12c.6 0 1-.4 1-1V8l-6-6z"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M14 2v6h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M9 13h6M9 17h4M10 15h1" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M18 10l-4 4-2-2" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Facturation Automatisée</h3>
                        <p class="text-gray-600 ">Générez instantanément des factures professionnelles
                            à votre image. Planifiez les factures récurrentes et envoyez des rappels de paiement
                            automatiques aux clients.</p>
                    </div>

                    <!-- Feature 3: Reporting & Analytics -->
                    <div class="p-6 rounded-xl border border-gray-200  hover:shadow-2xl transition duration-500">
                        <!-- SVG Illustration: Bar Chart -->
                        <div class="h-32 w-full flex justify-center items-center mb-6">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                class="w-24 h-24 text-primary ">
                                <path d="M3 3v18h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M18 17V8M13 17V12M8 17v-4" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <circle cx="18" cy="8" r="1.5" fill="currentColor" />
                                <circle cx="13" cy="12" r="1.5" fill="currentColor" />
                                <circle cx="8" cy="13" r="1.5" fill="currentColor" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Aperçus Intelligents</h3>
                        <p class="text-gray-600 ">Analysez les tendances de paiement, suivez la
                            rentabilité des clients et visualisez les flux de trésorerie grâce à des tableaux de bord
                            financiers puissants et faciles à lire.</p>
                    </div>

                </div>
            </section>

            <!-- 3. Call to Action (Final) -->
            <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
                <div class="bg-primary  text-secondary  p-10 sm:p-16 rounded-3xl shadow-2xl">
                    <h2 class="text-3xl sm:text-4xl font-extrabold mb-4">Prêt pour une Gestion Intelligente ?</h2>
                    <p class="text-lg mb-8 opacity-90">Rejoignez des milliers d'entreprises qui simplifient leurs
                        opérations avec Ageno dès aujourd'hui.</p>
                    <button
                        class="cta-button bg-secondary text-primary   px-10 py-4 rounded-xl text-xl font-bold shadow-lg border-2 border-primary hover:scale-[1.02] transition duration-300">
                        Démarrer Maintenant
                    </button>
                </div>
            </section>

        </main>

        <!-- Footer -->
        <footer class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 border-t border-gray-200 ">
            <div class="flex flex-col sm:flex-row justify-between items-center text-sm text-gray-600 ">
                <p>&copy; 2025 Ageno. Tous droits réservés.</p>
                <div class="flex space-x-4 mt-4 sm:mt-0">
                    <a href="#" class="hover:text-primary">Politique de Confidentialité</a>
                    <a href="#" class="hover:text-primary">Conditions d'Utilisation</a>
                </div>
            </div>
        </footer>
    </div>
</template>
