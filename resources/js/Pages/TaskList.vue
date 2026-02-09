<script setup>
import { ref, onMounted } from "vue";
import axios from "@/axios";
import TaskItem from "@/Components/TaskItem.vue";
import TaskSkeleton from "@/Components/TaskSkeleton.vue";

const tasks = ref([]);
const isFirstLoading = ref(true); // Состояние первой загрузки

const fetchTasks = async () => {
    try {
        const response = await axios.get("/tasks");
        tasks.value = response.data.data;
    } catch (error) {
        console.error("Ошибка при загрузке:", error);
    } finally {
        // Отключаем скелет с небольшой задержкой для плавности
        setTimeout(() => {
            isFirstLoading.value = false;
        }, 500);
    }
};

onMounted(fetchTasks);

const onTaskUpdated = (updatedTask) => {
    const index = tasks.value.findIndex((t) => t.id === updatedTask.id);
    if (index !== -1) tasks.value[index] = updatedTask;
};

const onTaskDeleted = (id) => {
    tasks.value = tasks.value.filter((task) => task.id !== id);
};
</script>

<template>
    <div class="max-w-2xl mx-auto py-8">
        <!-- Заголовок и кнопка создания -->
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Мои задачи</h1>
            <RouterLink
                :to="{ name: 'create-task' }"
                class="px-4 py-2 cursor-pointer bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm flex items-center"
            >
                <svg
                    class="w-5 h-5 mr-1"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    />
                </svg>
                Новая задача
            </RouterLink>
        </div>

        <div class="space-y-4">
            <!-- Показываем скелеты, пока идет загрузка -->
            <template v-if="isFirstLoading">
                <TaskSkeleton v-for="n in 5" :key="n" />
            </template>

            <!-- Показываем реальный список после загрузки -->
            <template v-else>
                <div
                    v-if="tasks.length === 0"
                    class="text-center py-10 text-gray-500"
                >
                    Список задач пуст
                </div>

                <TaskItem
                    v-for="task in tasks"
                    :key="task.id"
                    :task="task"
                    @updated="onTaskUpdated"
                    @deleted="onTaskDeleted"
                />
            </template>
        </div>
    </div>
</template>
