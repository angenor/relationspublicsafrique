<template>
    <div class="col-12">
        <form @submit.prevent="register" class="form-style5 ajax-contact">
            <div class="vs-circle"></div>
            <h3 class="form-title">S'inscrire</h3>

            <div v-if="eurrors.length" class="alert alert-danger">
                <ul class="mb-0">
                    <li v-for="eurror in eurrors" :key="eurror">
                        {{Object.values(eurror).join('\n')}}
                    </li>
                </ul>
            </div>

            <div class="form-group">
                <input v-model="form.name" type="text" name="name" id="name" placeholder="Nom complet" class="form-control">
                <div class="text-danger" v-if="eurrorsApi.name">{{ eurrorsApi.name[0] }}</div>
            </div>

            <div class="form-group">
                <input v-model="form.email" type="email" name="email" id="email" placeholder="Email" class="form-control">
                <div class="text-danger" v-if="eurrorsApi.email">{{ eurrorsApi.email[0] }}</div>
            </div>

            <div class="form-group">
                <input v-model="form.password" type="password" name="password" id="password" placeholder="Mot de passe" class="form-control">
                <div class="text-danger" v-if="eurrorsApi.password">{{ eurrorsApi.password[0] }}</div>
            </div>

            <div class="form-group">
                <input v-model="form.password_confirmation" type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirmer le mot de passe" class="form-control">
                <div class="text-danger" v-if="eurrorsApi.password_confirmation">{{ eurrorsApi.password_confirmation[0] }}</div>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" v-model="form.terms" id="terms">
                    <label class="form-check-label" for="terms">
                        J'accepte les <a href="#" class="text-decoration-none" style="color: #713d0b;">conditions d'utilisation</a>
                    </label>
                </div>
                <div class="text-danger" v-if="eurrorsApi.terms">{{ eurrorsApi.terms[0] }}</div>
            </div>

            <button v-if="!saveLoading" type="submit" class="vs-btn">S'inscrire</button>
            <button v-else disabled type="submit" class="vs-btn">
                <apploading></apploading>
            </button>

            <div class="text-center mt-3">
                <p class="mb-0">Déjà un compte ?</p>
                <a href="/login" class="vs-btn style4 mt-2">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Se connecter
                </a>
            </div>
        </form>
    </div>
</template>

<script setup>
import { useToast } from "vue-toastification";
const toast = useToast();
import {reactive, ref} from "vue";
import { useSavedata} from '../composable/useSavedata'

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

            location.href="/login"
        }
        setTimeout( cnx,2000)
    }



}
</script>

<style scoped>
.vs-btn {
    background: linear-gradient(45deg, #713d0b, #8b5a2b);
    border: none;
    color: white;
    padding: 12px 30px;
    border-radius: 5px;
    font-weight: 600;
    text-transform: uppercase;
    transition: all 0.3s ease;
    width: 100%;
}

.vs-btn:hover {
    background: linear-gradient(45deg, #5a2f08, #713d0b);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(113, 61, 11, 0.3);
}

.vs-btn.style4 {
    background: transparent;
    border: 2px solid #713d0b;
    color: #713d0b;
    padding: 10px 25px;
    border-radius: 5px;
    font-weight: 600;
    text-transform: uppercase;
    transition: all 0.3s ease;
    width: auto;
    display: inline-block;
}

.vs-btn.style4:hover {
    background: #713d0b;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(113, 61, 11, 0.3);
}

.form-control:focus {
    border-color: #713d0b;
    box-shadow: 0 0 0 0.2rem rgba(113, 61, 11, 0.25);
}

.form-check-input:checked {
    background-color: #713d0b;
    border-color: #713d0b;
}
</style>
