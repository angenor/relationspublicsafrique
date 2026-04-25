import { ref } from "vue";
import { axiosService } from "../services/axios.service.js";
export function useSavedata() {
    const eurrorsApi = ref({});
    const eurrors = ref([]);
    const data = ref({});
    const isLoading = ref(false);
    const etat = ref(false);
    const message = ref("");

    const save = async (url, form, method = "POST") => {
        isLoading.value = true;
        eurrorsApi.value = {};
        eurrors.value = [];
        etat.value = false;

        try {
            let res;

            if (method === "PUT") {
                // Pour les requêtes PUT, utiliser la méthode PUT directement
                res = await axiosService.put(url, form);
                message.value = "L'enregistrement a bien été modifié";
            } else if (method === "POST" && form.id) {
                // Pour les mises à jour, utiliser POST avec _method=PUT
                const formData = new FormData();
                for (const key in form) {
                    if (form[key] !== null && form[key] !== undefined) {
                        formData.append(key, form[key]);
                    }
                }
                formData.append("_method", "PUT");

                res = await axiosService.post(url, formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                });
                message.value = "L'enregistrement a bien été modifié";
            } else {
                // Pour les nouvelles créations
                res = await axiosService.post(url, form);
                message.value = "L'enregistrement a bien été ajouté";
            }

            data.value = res.data;
            isLoading.value = false;
            eurrors.value = {};
            etat.value = true;
        } catch (e) {
            etat.value = false;
            isLoading.value = false;

            if (e.response && e.response.status === 422) {
                eurrorsApi.value = e.response.data.errors;

                for (const k in eurrorsApi.value) {
                    let ele = {};
                    ele[k] = eurrorsApi.value[k].join("\n");
                    eurrors.value.push(ele);
                }

                message.value = "Merci de corriger vos erreurs !";
            } else {
                message.value =
                    e.response?.data?.message || "Une erreur est survenue";
            }
        }
    };

    return { message, data, isLoading, save, eurrors, etat, eurrorsApi };
}
