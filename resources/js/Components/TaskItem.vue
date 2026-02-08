<script setup>
import { ref, computed } from "vue";
import { useRouter } from "vue-router"; // Добавляем роутер
import axios from "@/axios";

const props = defineProps({ task: Object });
const emit = defineEmits(["updated", "deleted"]);
const router = useRouter(); // Инициализируем

const isUpdating = ref(false);
const isDeleting = ref(false); // Состояние для лоадера удаления
const localStatus = ref(props.task.status);

const isCompleted = computed(() => localStatus.value === "complete");

// Переход на страницу редактирования
const goToEdit = () => {
    router.push(`/tasks/${props.task.id}`);
};

// Обновление статуса
const toggleStatus = async () => {
    const oldStatus = localStatus.value;
    const newStatus = isCompleted.value ? "not completed" : "complete";

    localStatus.value = newStatus;
    isUpdating.value = true;

    try {
        const response = await axios.patch(`/tasks/${props.task.id}`, {
            status: newStatus,
        });
        emit("updated", response.data);
    } catch (error) {
        localStatus.value = oldStatus;
        alert("Ошибка синхронизации с сервером");
    } finally {
        isUpdating.value = false;
    }
};

// Удаление
const deleteTask = async () => {
    if (!confirm("Вы уверены, что хотите удалить эту задачу?")) return;

    isDeleting.value = true;
    try {
        await axios.delete(`/tasks/${props.task.id}`);
        emit("deleted", props.task.id); // Уведомляем родителя об удалении
    } catch (error) {
        alert("Не удалось удалить задачу");
        isDeleting.value = false;
    }
};
</script>

<template>
    <div
        @click="goToEdit"
        class="group flex items-center justify-between p-4 bg-white border rounded-xl transition-all cursor-pointer hover:border-indigo-300 hover:shadow-md active:scale-[0.99]"
        :class="
            isCompleted
                ? 'bg-gray-50 border-gray-100'
                : 'border-gray-200 shadow-sm'
        "
    >
        <div class="flex items-center space-x-4 flex-1">
            <!-- Остановка всплытия события (.stop), чтобы не срабатывал goToEdit -->
            <div class="relative flex items-center justify-center" @click.stop>
                <input
                    type="checkbox"
                    :checked="isCompleted"
                    @change="toggleStatus"
                    :disabled="isUpdating"
                    class="w-5 h-5 text-indigo-600 border-gray-300 rounded cursor-pointer transition disabled:opacity-50"
                />
                <div
                    v-if="isUpdating"
                    class="absolute inset-0 flex items-center justify-center bg-white/50 rounded"
                >
                    <svg
                        class="animate-spin h-3 w-3 text-indigo-600"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                            fill="none"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                    </svg>
                </div>
            </div>

            <div class="flex-1">
                <h3
                    class="font-medium transition-all duration-500"
                    :class="
                        isCompleted
                            ? 'text-gray-400 line-through'
                            : 'text-gray-900 group-hover:text-indigo-600'
                    "
                >
                    {{ task.title }}
                </h3>
                <p
                    v-if="task.description"
                    class="text-sm transition-all duration-500 line-clamp-1"
                    :class="
                        isCompleted
                            ? 'text-gray-300 line-through'
                            : 'text-gray-500'
                    "
                >
                    {{ task.description }}
                </p>
            </div>
        </div>

        <!-- Остановка всплытия события (.stop) на кнопке удаления -->
        <button
            @click.stop="deleteTask"
            :disabled="isDeleting"
            class="p-2 text-gray-400 cursor-pointer hover:text-red-500 transition relative z-10 disabled:opacity-50"
        >
            <!-- Спиннер вместо иконки при удалении -->
            <svg
                v-if="isDeleting"
                class="animate-spin h-5 w-5 text-red-500"
                viewBox="0 0 24 24"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                    fill="none"
                ></circle>
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
            </svg>

            <!-- Иконка корзины -->
            <svg
                v-else
                class="w-5 h-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                />
            </svg>
        </button>
    </div>
</template>
