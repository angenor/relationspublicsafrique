<template>
    <div class=" max-w-7xl mx-auto">
        <div class="bg-white my-4 p-4 rounded-lg shadow flex flex-col lg:flex-row">
            <div class="lg:w-64">
                <h1 class="text-2xl font-bold mb-6 uppercase text-sky-950">Profil</h1>
            </div>
            <div class=" flex-1">
                <div class="   pt-36 ">


                    <Breadcrumb>
                        <BreadcrumbItem href="/" home>
                            Accueil
                        </BreadcrumbItem>
                        <BreadcrumbItem href="/user/consulter-profil">
                            Profil
                        </BreadcrumbItem>
                        <BreadcrumbItem>
                            Metre à jours mon profil
                        </BreadcrumbItem>
                    </Breadcrumb>

                    <div class="">

                        <Alert v-if="eurrors.length" type="danger" class="mb-2">
                            <ul v-for="eurror in eurrors">
                                <li>{{Object.values(eurror).join('\n')}}</li>
                            </ul>
                        </Alert>
                    </div>
                    <hr class="my-3 lg:mb-16 mb-10">


                    <div class="lg:h-48 h-24 bg-sky-900 rounded-xl my-4 relative w-full " >



                           <user-profil-avatar v-if="form.img" :img="form.img" @on-avatar-file-selected="onAvatarFileSelected"></user-profil-avatar>

                        <div class="overflow-hidden h-full w-full rounded-xl">
                            <img src="/images/front/ban.jpg" alt="">
                        </div>

                    </div>
                    <div class="">
                        <h1 class="text-2xl font-bold mb-6 uppercase text-sky-950">Mettre à jours mon profil</h1>
                    </div>

                    <form class="" @submit.prevent="saveProfil">


                        <div class="md:grid grid-cols-3 gap-4">
                            <div class="w-full">
                                <binput name="title" required :options=sexeOptions    label="Sexe" type="select" v-model="form.title" :formerrors="eurrorsApi"></binput>
                            </div>

                            <div class="w-full">
                                <binput name="nom" required label="Nom" type="text" v-model="form.nom" :formerrors="eurrorsApi"></binput>
                            </div>

                            <div class="w-full">
                                <binput name="prenom" required  label="Prénom" type="text" v-model="form.prenom" :formerrors="eurrorsApi"></binput>
                            </div>

<!--                            <div class="w-full">-->
<!--                                <binput name="name"  label="Pseudo" type="text" v-model="form.name" :formerrors="eurrorsApi"></binput>-->
<!--                            </div>-->


                        </div>



                        <div class="md:grid grid-cols-3 gap-4">
                            <div class="w-full">
                                <binput name="email"  required  label="Email" type="text" v-model="form.email" :formerrors="eurrorsApi"></binput>
                            </div>


                            <div class="w-full">
                                <binput name="tel" required  label="Téléphone" type="text" v-model="form.tel" :formerrors="eurrorsApi"></binput>
                            </div>
                            <div class="w-full">
                                <binput name="contact" required  label="Autres contacts" type="text" v-model="form.contact" :formerrors="eurrorsApi"></binput>
                            </div>



                        </div>


                        <div class="md:grid grid-cols-3 gap-4">
                            <div class="w-full ">
                                <binput name="pays" required  label="Pays" type="select" :options="listPays" v-model="form.pays_id" :formerrors="eurrorsApi"></binput>
                            </div>

                            <div class="w-full ">
                                <binput name="adresse" required  label="Adresse" type="text" v-model="form.adresse" :formerrors="eurrorsApi"></binput>
                            </div>
                            <div class="w-full">
                                <binput name="fonction" required    label="Fonction" type="text" v-model="form.fonction" :formerrors="eurrorsApi"></binput>
                            </div>

                            <div class="w-full">
                                <binput name="domaine" required  label="Domaine de compétances" type="text" v-model="form.domaine" :formerrors="eurrorsApi"></binput>
                            </div>


                        </div>

                        <div class="md:grid grid-cols-3 gap-4">
                            <div class="w-full ">
                                <binput name="facebook"  label="Lien Facebook" placeholder="https://www.facebook.com" type="text" v-model="form.facebook" :formerrors="eurrorsApi"></binput>
                            </div>
                            <div class="w-full">
                                <binput name="twitter"   label="Lien Twitter" placeholder="https://www.twitter.com" type="text" v-model="form.twitter" :formerrors="eurrorsApi"></binput>
                            </div>

                            <div class="w-full">
                                <binput name="youtube"  label="Lien Youtube" placeholder="https://www.youtube.com" type="text" v-model="form.youtube" :formerrors="eurrorsApi"></binput>
                            </div>

                            <div class="w-full">
                                <binput name="linkding"  label="Lien Linkedin" placeholder="https://www.linkedin.com"  type="text" v-model="form.linkding" :formerrors="eurrorsApi"></binput>
                            </div>
                            <div class="w-full">
                                <binput name="site"  label="Lien Site internet" placeholder="https://www.site.com"  type="text" v-model="form.site" :formerrors="eurrorsApi"></binput>
                            </div>
                            <div class="w-full">
                                <binput name="online"  class="mt-5" label="Etat en ligne ou hors ligne" placeholder="Biographie" type="toggle" v-model="form.online" :formerrors="eurrorsApi"></binput>
                            </div>


                        </div>

                        <div class="">
                            <div class="w-full ">
                                <binput name="bio"  label="Votre Biographie" placeholder="Biographie" type="textarea" v-model="form.bio" :formerrors="eurrorsApi"></binput>
                            </div>
                        </div>

                        <div class="">
                            <div class="w-full ">
                                <input type="file" class="form-control" name="avatar" id="avatar">
                            </div>
                        </div>


                        <hr class="my-4">
                        <div class="max-w-md">

                            <button v-if="!saveLoading" type="submit" class="btn-success w-full">Modifier mon profil</button>
                            <button v-else disabled type="submit" class="btn-success w-full"><apploading></apploading></button>
                        </div>

                    </form>

<!--                    <pre>-->
<!--                        {{form}}-->
<!--                    </pre>-->
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>

import {useSavedata} from "@/composable/useSavedata";
import { useToast } from "vue-toastification";
import { Breadcrumb,BreadcrumbItem } from 'flowbite-vue'
const toast = useToast();
const {save, isLoading: saveLoading, data: saveData, etat, eurrors, message,eurrorsApi} = useSavedata()
import {computed, onMounted, ref} from "vue";
import {Alert} from "flowbite-vue";
import axios from "axios";

  const form = ref({
      name:'',
      slug:'',
      nom:'',
      prenom:'',
      title:'',
      fonction:'',
      domaine:'',
      email:'',
      facebook:'',
      twitter:'',
      youtube:'',
      linkding:'',
      site:'',
      contact:'',
      adresse:'',
      tel:'',
      bio:'',
      online:'',
      image:'',

  })
const pays = ref([]);

const sexeOptions = [
    { label: 'Homme', value: 'homme' },
    { label: 'Femme', value: 'femme' },

]
const listPays = computed(()=>{
    return pays.value.map(p=> {
        return { label:p.name, value: p.id }
    })
})

onMounted(async ()=>{
    try {
        const res = await  axios.get('/user/api/profil')
        form.value =res.data['profil'] ;
        pays.value =res.data['pays'] ;
        console.log(pays.value)
    }catch (err) {
        console.log(err)
    }
})

const  saveProfil = async ()=>{
    await  save('/user/profil',  form.value)
    if (!etat.value){
      toast.error(message.value)
      return   console.log(eurrors.value)
    }
    toast.success("Les informations de votre profil ont bien été mis à jour !")
}

const onAvatarFileSelected = (fileUrl) => {
    console.log(fileUrl)
    form.value.image = fileUrl
}

</script>

<style scoped>

</style>
