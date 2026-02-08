<template>
    <div
        class="flex items-center justify-between p-4 bg-white border border-gray-100 rounded-xl shadow-sm animate-pulse"
    >
        <div class="flex items-center space-x-4 flex-1">
            <!-- Имитация чекбокса -->
            <div class="w-5 h-5 bg-gray-200 rounded"></div>

            <div class="flex-1 space-y-2">
                <!-- Имитация заголовка -->
                <div class="h-5 bg-gray-200 rounded w-1/3"></div>
                <!-- Имитация описания -->
                <div class="h-4 bg-gray-100 rounded w-1/2"></div>
            </div>
        </div>

        <!-- Имитация кнопки удаления -->
        <div class="w-8 h-8 bg-gray-100 rounded-lg ml-4"></div>
    </div>
</template>
