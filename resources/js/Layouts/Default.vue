<script setup></script>

<template>
    <div class="min-h-screen bg-gray-50">
        <header class="bg-white shadow-sm border-b border-gray-200">
            <nav
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between h-16 items-center"
            >
                <RouterLink to="/" class="text-2xl font-bold text-indigo-600"
                    >To-Do App</RouterLink
                >
                <div class="flex space-x-8">
                    <RouterLink
                        to="/"
                        class="text-gray-700 hover:text-indigo-600 font-medium"
                        >Главная</RouterLink
                    >
                </div>
            </nav>
        </header>

        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <slot />
            <!-- Здесь будет контент страниц -->
        </main>
    </div>
</template>
