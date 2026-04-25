<template>
    <div class="card shadow-lg border-0">
        <div class="row g-0">
            <div class="col-md-6">
                <img class="img-fluid h-100 w-100 object-cover" src="images/user2.jpg" alt="Connexion" style="object-fit: cover; min-height: 500px;">
            </div>
            <div class="col-md-6">
                <div class="card-body p-5">
                    <form @submit.prevent="login">
                        <div class="mb-4">
                            <h2 class="h3 fw-bold text-theme mb-3 border-start border-4 ps-3" style="border-color: var(--theme-color) !important;">
                                Se connecter
                            </h2>
                            <hr class="my-4">
                        </div>

                        <div v-if="eurrors.length" class="alert alert-danger mb-3">
                            <ul class="mb-0">
                                <li v-for="eurror in eurrors" :key="eurror">
                                    {{Object.values(eurror).join('\n')}}
                                </li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                v-model="form.email"
                                :class="{'is-invalid': eurrorsApi.email}"
                                placeholder="Votre adresse email"
                            >
                            <div v-if="eurrorsApi.email" class="invalid-feedback">
                                {{ eurrorsApi.email[0] }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                v-model="form.password"
                                :class="{'is-invalid': eurrorsApi.password}"
                                placeholder="Votre mot de passe"
                            >
                            <div v-if="eurrorsApi.password" class="invalid-feedback">
                                {{ eurrorsApi.password[0] }}
                            </div>
                        </div>

                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="remember"
                                    v-model="form.remember"
                                >
                                <label class="form-check-label" for="remember">
                                    Se souvenir de moi
                                </label>
                            </div>
                            <a href="/password/reset" class="text-decoration-none small text-theme">
                                Mot de passe oublié ?
                            </a>
                        </div>

                        <div class="d-grid mb-4">
                            <button v-if="!saveLoading" type="submit" class="btn btn-lg text-white bg-theme">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Se connecter
                            </button>
                            <button v-else disabled type="submit" class="btn btn-lg text-white bg-theme">
                                <div class="spinner-border spinner-border-sm me-2" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                                Connexion...
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">
                    <div class="text-center">
                        <a href="/register" class="text-decoration-none d-flex align-items-center justify-content-center">
                            <span class="me-2">Nouveau sur notre plateforme ?</span>
                            <span class="d-flex align-items-center text-theme fw-bold">
                                <i class="fas fa-user-plus me-2"></i>
                                S'inscrire
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {Alert, Input} from 'flowbite-vue'
import { useToast } from "vue-toastification";
const toast = useToast();
import {reactive, ref} from "vue";
import { useSavedata} from '../composable/useSavedata'
import {LockOpenIcon,UsersIcon} from "@heroicons/vue/20/solid";

const {save, isLoading: saveLoading, data: saveData, etat, eurrors, message,eurrorsApi} = useSavedata()

const form = reactive({
    email:'',
    password:'admin123',
    remember:false
})
const isLoading = ref(false)

const  login = async ()=>{

    await  save('/login',  form)
    if (!etat.value){
        toast.error(message.value)
        console.log(eurrors.value)
    }else {
        toast.success('Vous êtes maintenant connecté!')
        const cnx = function () {

            location.href="/user/tableau-de-bord"
        }
        setTimeout( cnx,1000)
    }



}
</script>

<style scoped></style>
