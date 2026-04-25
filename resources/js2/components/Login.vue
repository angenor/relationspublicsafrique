<template>

    <form action="" @submit.prevent="handelConnexion">
        <h1 class="text-center text-lg font-bold mb-6 ">Connexion</h1>

        <input v-model="form.email" class="form-control mb-4" type="email" placeholder="Email">

        <input v-model="form.password" class="form-control mb-4" type="password" placeholder="Mot de passe">



        <button :disabled="isLoading" type="submit" class="vs-btn">
            <span v-if="!isLoading">Connexion</span>
            <apploading v-else></apploading>
        </button>

        <span class="btn btn-icon " @click.prevent="() => toggleLogin()"> S'inscrire </span>
    </form>



</template>

<script setup>

import { useToast } from "vue-toastification";
const toast = useToast();
import {reactive, ref} from "vue";

const props = defineProps({
    toggleLogin:{
        type:Function,
        required: true
    }
})

const form = reactive({
    email :('gastinoking@gmail.com'),
    password :('admin123')
})



const isLoading = ref(false)

const handelConnexion = async () => {
    isLoading.value =true
    try {
    const res = await axios.post('/login',form,{headers:{'Content-Type':'application/json'}})
    toast.success('Vous êtes maintenent connecté!')
    location.href="/user/profil"
    }catch (err) {
        if (err.response.status ===422){
            const rep =Object.values(err.response.data.errors).join(' \n')
            console.log( rep)
            // toast.error('Attention les identifiants ne sont pas correctes !')
            toast.error(rep)

        }else {
            toast.error('Une erreur est survenue !')
        }
    }finally {
        isLoading.value =false
    }

}
</script>

<style scoped>

</style>
