import "../css/app.css";
import { createApp } from "vue";
import App from "./App.vue"; // Создайте корневой компонент
import router from "./router";

createApp(App).use(router).mount("#app");
