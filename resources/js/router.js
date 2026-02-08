import { createRouter, createWebHistory } from "vue-router";
import TaskList from "./Pages/TaskList.vue";
import TaskCreate from "./Pages/CreateTask.vue";
import TaskEdit from "./Pages/EditTask.vue";

const routes = [
    { path: "/", name: "tasks", component: TaskList },
    { path: "/tasks/create", name: "create-task", component: TaskCreate },
    {
        path: "/tasks/:id",
        name: "edit-task",
        component: TaskEdit,
        props: true,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
