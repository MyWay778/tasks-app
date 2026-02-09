<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "@/axios";
import TaskItem from "@/Components/TaskItem.vue";
import TaskSkeleton from "@/Components/TaskSkeleton.vue";

const tasks = ref([]);
const isFirstLoading = ref(true); // Состояние первой загрузки
const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 5,
    total: 0,
    links: {},
});

const fetchTasks = async (page = 1) => {
    try {
        isFirstLoading.value = true;
        const response = await axios.get("/tasks", {
            params: { page, per_page: pagination.value.per_page },
        });

        // Handle both paginated and non-paginated responses
        if (response.data.data) {
            tasks.value = response.data.data;

            // Update pagination metadata if available
            if (response.data.meta) {
                pagination.value = {
                    current_page: response.data.meta.current_page,
                    last_page: response.data.meta.last_page,
                    per_page: response.data.meta.per_page,
                    total: response.data.meta.total,
                    links: response.data.links || {},
                };
            }
        } else {
            // Fallback for non-paginated responses
            tasks.value = Array.isArray(response.data) ? response.data : [];
        }
    } catch (error) {
        console.error("Ошибка при загрузке:", error);
    } finally {
        // Отключаем скелет с небольшой задержкой для плавности
        setTimeout(() => {
            isFirstLoading.value = false;
        }, 500);
    }
};

onMounted(() => fetchTasks(1));

const onTaskUpdated = (updatedTask) => {
    const index = tasks.value.findIndex((t) => t.id === updatedTask.id);
    if (index !== -1) tasks.value[index] = updatedTask;
};

const onTaskDeleted = (id) => {
    tasks.value = tasks.value.filter((task) => task.id !== id);
    // Update total count
    if (pagination.value.total > 0) {
        pagination.value.total--;
    }
};

const goToPage = (page) => {
    if (page >= 1 && page <= pagination.value.last_page) {
        fetchTasks(page);
    }
};

const hasPagination = computed(() => {
    return pagination.value.last_page > 1;
});

const visiblePages = computed(() => {
    const current = pagination.value.current_page;
    const last = pagination.value.last_page;
    const pages = [];

    if (last <= 7) {
        // Show all pages if 7 or fewer
        for (let i = 1; i <= last; i++) {
            pages.push(i);
        }
    } else {
        // Show pages around current page
        if (current <= 4) {
            // Near the start
            for (let i = 1; i <= 5; i++) {
                pages.push(i);
            }
            pages.push("...");
            pages.push(last);
        } else if (current >= last - 3) {
            // Near the end
            pages.push(1);
            pages.push("...");
            for (let i = last - 4; i <= last; i++) {
                pages.push(i);
            }
        } else {
            // In the middle
            pages.push(1);
            pages.push("...");
            for (let i = current - 1; i <= current + 1; i++) {
                pages.push(i);
            }
            pages.push("...");
            pages.push(last);
        }
    }

    return pages;
});
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

        <!-- Pagination Controls -->
        <div
            v-if="hasPagination && !isFirstLoading"
            class="mt-8 flex items-center justify-center space-x-2"
        >
            <!-- Previous Button -->
            <button
                @click="goToPage(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
            >
                Назад
            </button>

            <!-- Page Numbers -->
            <div class="flex space-x-1">
                <template v-for="page in visiblePages" :key="page">
                    <button
                        v-if="page !== '...'"
                        @click="goToPage(page)"
                        :class="[
                            'px-3 py-2 text-sm font-medium rounded-lg transition',
                            page === pagination.current_page
                                ? 'bg-indigo-600 text-white'
                                : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50',
                        ]"
                    >
                        {{ page }}
                    </button>
                    <span v-else class="px-3 py-2 text-sm text-gray-500">
                        ...
                    </span>
                </template>
            </div>

            <!-- Next Button -->
            <button
                @click="goToPage(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
            >
                Вперед
            </button>
        </div>

        <!-- Pagination Info -->
        <div
            v-if="hasPagination && !isFirstLoading"
            class="mt-4 text-center text-sm text-gray-500"
        >
            Показано
            {{ (pagination.current_page - 1) * pagination.per_page + 1 }} -
            {{
                Math.min(
                    pagination.current_page * pagination.per_page,
                    pagination.total,
                )
            }}
            из {{ pagination.total }} задач
        </div>
    </div>
</template>
