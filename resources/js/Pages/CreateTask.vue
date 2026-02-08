<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import axios from "@/axios";

const router = useRouter();

// Реактивные переменные для полей формы
const title = ref("");
const description = ref("");
const errors = ref({});
const loading = ref(false);

const submitForm = async () => {
    loading.value = true;
    errors.value = {};

    try {
        // Отправка данных на ваш Laravel REST API (/api/tasks)
        await axios.post("tasks", {
            title: title.value,
            description: description.value,
            status: "not completed", // Значение по умолчанию из вашего Enum
        });

        // После успеха возвращаемся на главную
        router.push("/");
    } catch (err) {
        if (err.response && err.response.status === 422) {
            errors.value = err.response.data.errors; // Ошибки валидации Laravel
        } else {
            alert("Произошла ошибка при сохранении");
        }
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="max-w-2xl mx-auto py-8 px-4">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Новая задача</h1>
            <router-link
                to="/"
                class="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
                >← К списку</router-link
            >
        </div>

        <form
            @submit.prevent="submitForm"
            class="bg-white p-6 rounded-xl shadow-sm border border-gray-200"
        >
            <div class="space-y-6">
                <!-- Заголовок -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Название</label
                    >
                    <input
                        v-model="title"
                        type="text"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition"
                        :class="{ 'border-red-500': errors.title }"
                        placeholder="Что нужно сделать?"
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
                        placeholder="Детали задачи..."
                    ></textarea>
                </div>

                <!-- Кнопка -->
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
                        {{ loading ? "Сохранение..." : "Создать задачу" }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>
