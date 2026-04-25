<template>
    <div class="card shadow-lg border-0">
        <div class="row g-0">
            <div class="col-md-6">
                <img class="img-fluid h-100 w-100 object-cover" src="images/user2.jpg" alt="Inscription" style="object-fit: cover; min-height: 500px;">
            </div>
            <div class="col-md-6">
                <div class="card-body p-5">
                    <form @submit.prevent="register">
                        <div class="mb-4">
                            <h2 class="h3 fw-bold text-theme mb-3 border-start border-4 ps-3" style="border-color: var(--theme-color) !important;">
                                S'inscrire
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
                            <label for="name" class="form-label">Nom complet</label>
                            <input
                                type="text"
                                class="form-control"
                                id="name"
                                name="name"
                                v-model="form.name"
                                :class="{'is-invalid': eurrorsApi.name}"
                                placeholder="Votre nom complet"
                            >
                            <div v-if="eurrorsApi.name" class="invalid-feedback">
                                {{ eurrorsApi.name[0] }}
                            </div>
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

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                            <input
                                type="password"
                                class="form-control"
                                id="password_confirmation"
                                name="password_confirmation"
                                v-model="form.password_confirmation"
                                :class="{'is-invalid': eurrorsApi.password_confirmation}"
                                placeholder="Confirmez votre mot de passe"
                            >
                            <div v-if="eurrorsApi.password_confirmation" class="invalid-feedback">
                                {{ eurrorsApi.password_confirmation[0] }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="terms"
                                    v-model="form.terms"
                                    :class="{'is-invalid': eurrorsApi.terms}"
                                >
                                <label class="form-check-label" for="terms">
                                    J'accepte les <a href="#" class="text-decoration-none text-theme">conditions d'utilisation</a>
                                </label>
                                <div v-if="eurrorsApi.terms" class="invalid-feedback d-block">
                                    {{ eurrorsApi.terms[0] }}
                                </div>
                            </div>
                        </div>

                        <div class="d-grid mb-4">
                            <button v-if="!saveLoading" type="submit" class="btn btn-lg text-white bg-theme">
                                <i class="fas fa-user-plus me-2"></i>
                                S'inscrire
                            </button>
                            <button v-else disabled type="submit" class="btn btn-lg text-white bg-theme">
                                <div class="spinner-border spinner-border-sm me-2" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                                Inscription...
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">
                    <div class="text-center">
                        <a href="/login" class="text-decoration-none d-flex align-items-center justify-content-center">
                            <span class="me-2">Déjà un compte ?</span>
                            <span class="d-flex align-items-center text-theme fw-bold">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Se connecter
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
    name:'',
    email:'',
    password:'',
    password_confirmation:'',
    terms:false
})
const isLoading = ref(false)

const  register = async ()=>{

    await  save('/register',  form)
    if (!etat.value){
        toast.error(message.value)
        console.log(eurrors.value)
    }else {
        toast.success('Inscription réussie! Vérifiez votre email pour activer votre compte.')
        const cnx = function () {

            location.href="/email/verify"
        }
        setTimeout( cnx,2000)
    }



}
</script>

<style scoped></style>
