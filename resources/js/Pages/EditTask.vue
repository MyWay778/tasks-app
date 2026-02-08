<script setup>
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "@/axios";

const router = useRouter();
const route = useRoute(); // Для получения ID из URL

// Реактивные переменные
const title = ref("");
const description = ref("");
const isCompleted = ref(false); // Для чекбокса
const initialIsCompleted = ref(false); // Начальное состояние загруженной задачи
const createdAt = ref("");
const updatedAt = ref("");
const completedAt = ref(null);
const errors = ref({});
const loading = ref(false);
const fetching = ref(true); // Состояние загрузки данных задачи

// 1. Загрузка данных задачи
const fetchTask = async () => {
    try {
        const response = await axios.get(`tasks/${route.params.id}`);
        const task = response.data;

        title.value = task.title;
        description.value = task.description;
        isCompleted.value = task.status === "complete";
        initialIsCompleted.value = isCompleted.value;
        createdAt.value = task.created_at;
        updatedAt.value = task.updated_at;
        completedAt.value = task.completed_at;
    } catch (err) {
        alert("Задача не найдена");
        router.push("/");
    } finally {
        fetching.value = false;
    }
};

// Вызываем загрузку при монтировании
onMounted(fetchTask);

// 2. Форматирование даты
const formatDate = (dateString) => {
    if (!dateString) return "";
    return new Date(dateString).toLocaleString("ru-RU", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// 3. Сохранение изменений
const submitForm = async () => {
    loading.value = true;
    errors.value = {};

    try {
        await axios.put(`tasks/${route.params.id}`, {
            title: title.value,
            description: description.value,
            status: isCompleted.value ? "complete" : "not completed",
        });

        router.push("/");
    } catch (err) {
        if (err.response && err.response.status === 422) {
            if (err.response.data.errors) {
                errors.value = err.response.data.errors;
            } else {
                errors.value = { general: err.response.data.message };
            }
        } else {
            alert("Произошла ошибка при обновлении");
        }
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="max-w-2xl mx-auto py-8 px-4">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">
                Редактирование задачи
            </h1>
            <router-link
                to="/"
                class="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
            >
                ← К списку
            </router-link>
        </div>

        <!-- Индикатор загрузки данных -->
        <div v-if="fetching" class="text-center py-10">Загрузка...</div>

        <form
            v-else
            @submit.prevent="submitForm"
            class="bg-white p-6 rounded-xl shadow-sm border border-gray-200"
        >
            <!-- Сообщение об общей ошибке -->
            <div
                v-if="errors.general"
                class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 flex items-start"
            >
                <svg
                    class="w-5 h-5 text-red-500 mt-0.5 mr-3 flex-shrink-0"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                >
                    <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd"
                    />
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-red-800">
                        Ошибка сохранения
                    </h3>
                    <p class="mt-1 text-sm text-red-700">
                        {{ errors.general }}
                    </p>
                </div>
            </div>
            <div class="space-y-6">
                <!-- Статус (Checkbox) -->
                <div class="flex items-center">
                    <input
                        id="is_completed"
                        v-model="isCompleted"
                        type="checkbox"
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                    />
                    <label
                        for="is_completed"
                        class="ml-2 block text-sm text-gray-900 font-medium"
                    >
                        Отметить как выполненную
                    </label>
                </div>

                <!-- Название -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Название</label
                    >
                    <input
                        v-model="title"
                        type="text"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition"
                        :class="{ 'border-red-500': errors.title }"
                    />
                    <p v-if="errors.title" class="mt-1 text-xs text-red-500">
                        {{ errors.title[0] }}
                    </p>
                </div>

                <!-- Описание -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Описание</label
                    >
                    <textarea
                        v-model="description"
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition"
                    ></textarea>
                </div>

                <!-- Информационный блок (Даты) -->
                <div
                    class="pt-4 border-t border-gray-100 flex flex-col space-y-1"
                >
                    <span
                        v-if="isCompleted && completedAt"
                        class="text-xs font-medium text-green-600"
                    >
                        Завершена: {{ formatDate(completedAt) }}
                    </span>

                    <span
                        v-if="createdAt !== updatedAt"
                        class="text-xs text-gray-400"
                    >
                        Обновлена: {{ formatDate(updatedAt) }}
                    </span>

                    <span class="text-xs text-gray-400"
                        >Создана: {{ formatDate(createdAt) }}</span
                    >
                </div>

                <p
                    v-if="initialIsCompleted && isCompleted"
                    class="text-sm text-amber-600 italic"
                >
                    Завершенные задачи нельзя редактировать. Чтобы изменить
                    данные, верните задачу в статус "В работе".
                </p>

                <!-- Кнопки -->
                <div class="flex justify-end space-x-3 pt-4">
                    <button
                        type="button"
                        @click="router.push('/')"
                        class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                    >
                        Отмена
                    </button>
                    <button
                        type="submit"
                        :disabled="loading"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:bg-indigo-400 transition cursor-pointer"
                    >
                        {{ loading ? "Сохранение..." : "Обновить задачу" }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>
