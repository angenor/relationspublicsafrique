// const API_URL = 'http://127.0.0.1:8000/api/v1';
const API_URL = "";
// const API_URL = 'https://apinovissi.appsridge.com/api/v1';
// const API_URL = 'http://damssan.com:8000/api/v1';
// const API_URL = process.env.VUE_APP_URL
const data = JSON.parse(localStorage.getItem("vuex"));

import axios from "axios";
import { ref } from "vue";

const storage = ref(data?.auth || {});

const axiosService = axios.create({
    baseURL: API_URL,
    timeout: 10000,
    headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
    },
});

// Fonction pour convertir un objet en FormData
const toFormData = (obj) => {
    const formData = new FormData();
    for (const key in obj) {
        if (obj[key] !== null && obj[key] !== undefined) {
            formData.append(key, obj[key]);
        }
    }
    return formData;
};

// Ajouter le token CSRF pour toutes les requêtes
axiosService.interceptors.request.use(
    function (config) {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");

        if (token) {
            config.headers["X-CSRF-TOKEN"] = token;
        }

        return config;
    },
    function (error) {
        return Promise.reject(error);
    }
);
if (storage && storage.value.token) {
    axiosService.defaults.headers.common["Authorization"] =
        "Bearer " + storage.value.token;
} else {
    delete axiosService.defaults.headers.common["Authorization"];
}

// S'assurer que toutes les méthodes HTTP sont disponibles
axiosService.put =
    axiosService.put ||
    function (url, data, config) {
        return axios.put(url, data, { ...axiosService.defaults, ...config });
    };

axiosService.patch =
    axiosService.patch ||
    function (url, data, config) {
        return axios.patch(url, data, { ...axiosService.defaults, ...config });
    };

axiosService.delete =
    axiosService.delete ||
    function (url, config) {
        return axios.delete(url, { ...axiosService.defaults, ...config });
    };

export { axiosService };
