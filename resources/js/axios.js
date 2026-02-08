import axios from "axios";

const instance = axios.create({
    baseURL: "/api", // Все запросы будут начинаться с /api
    headers: {
        "X-Requested-With": "XMLHttpRequest",
        Accept: "application/json",
        "Content-Type": "application/json",
    },
    withCredentials: true, // Нужно для работы с сессиями и Sanctum (если добавите позже)
});

// Перехватчик для обработки ошибок (опционально)
instance.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 419) {
            alert("Сессия истекла, страница будет перезагружена");
            window.location.reload();
        }
        return Promise.reject(error);
    },
);

// Перехватчик для добавления CSRF-токена
instance.interceptors.request.use((config) => {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        config.headers["X-CSRF-TOKEN"] = token.content;
    }
    return config;
});

export default instance;
