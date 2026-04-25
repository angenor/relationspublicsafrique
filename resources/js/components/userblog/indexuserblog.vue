<template>
  <div class=" max-w-7xl mx-auto">

  <div v-if="isEditing" class="flex">
    <div class="w-full bg-white my-4 p-4 rounded-lg shadow flex flex-col lg:flex-row">
      <div class="lg:w-64">
        <h1 class="text-2xl font-bold mb-6 uppercase text-sky-950">Profil</h1>
      </div>
      <div class="w-full">
        <div class="    ">


          <Breadcrumb>
            <BreadcrumbItem href="/" home>
              Accueil
            </BreadcrumbItem>
            <BreadcrumbItem  class="cursor-pointer" @click.prevent="$event => postsStore.isEditing = false">
              Blog
            </BreadcrumbItem>
            <BreadcrumbItem>
              Mettre à jours {{post.name}}
            </BreadcrumbItem>
          </Breadcrumb>

          <div class="">

            <Alert v-if="eurrors.length" type="danger" class="mb-2">
              <ul v-for="eurror in eurrors">
                <li>{{Object.values(eurror).join('\n')}}</li>
              </ul>
            </Alert>
          </div>
          <hr class="my-3 lg:mb-4 mb-10">
          <form class=" w-full" @submit.prevent="saveProfil">

            <div class="w-full">
              <binput name="name"  label="Titre de l'article" placeholder="Titre de l'article"  type="text" v-model="post.name" :formerrors="eurrorsApi"></binput>
            </div>

            <div class="">
              <div class="w-full ">
                  <editor  :api-key="key"  v-model="post.content" :init="init" />


<!--                <binput name="content"  label="Contenu de l'article" placeholder="Contenu de l'article" type="textarea" v-model="post.content" :formerrors="eurrorsApi"></binput>-->
              </div>
            </div>

            <div class="">

              <div class="w-full">
                <binput name="online"  class="mt-5" label="Etat ( en ligne ou hors ligne)" placeholder="Biographie" type="toggle" v-model="post.online" :formerrors="eurrorsApi"></binput>
              </div>
            </div>


            <hr class="my-4">
            <div class="max-w-md">

              <button v-if="!saveLoading" type="submit" class="btn-success w-full">Publier l'artile</button>
              <button v-else disabled type="submit" class="btn-success w-full"><apploading></apploading></button>
            </div>

          </form>

        </div>
      </div>
    </div>
    <div class="lg:w-96 mt-4">
      <div class="bg-white lg:ml-4 rounded-lg  border-l shadow p-2 w-full">
          <div class="p-3 border-b rounded-lg">  <h1 class="text-lg font-bold mb-6 uppercase text-sky-950">Publier un article</h1></div>
        <div class="relative mb-10 p-3">
          <blug-upload-image  v-if="post.img"   ></blug-upload-image>
        </div>

      </div>
    </div>
  </div>

  <div class="bg-white w-full p-3 flex mt-4" v-else>


      <div class="lg:w-64">
          <h1 class="text-2xl font-bold mb-6 uppercase text-sky-950">Profil</h1>
      </div>


      <div class="w-full">
          <Breadcrumb>
              <BreadcrumbItem href="/" home>
                  Accueil
              </BreadcrumbItem>
              <BreadcrumbItem  class="cursor-pointer" @click.prevent="resetPost">
                  Blog
              </BreadcrumbItem>

          </Breadcrumb>


          <div class="">
              <Button @click.prevent="resetPost">Ajouter</Button>
          </div>
          <blog-posts-list></blog-posts-list>

<!--          <pre>-->
<!--   post:     {{ post}}-->
<!--    </pre>-->
      </div>


    </div>


  </div>
</template>

<script setup>
import  { storeToRefs}  from 'pinia'
import { usePostsStore}  from  '@/stores/posts'
const postsStore = usePostsStore()
import {useSavedata} from "@/composable/useSavedata";
import { useToast } from "vue-toastification";
import {Breadcrumb, BreadcrumbItem, Table, Button, Badge } from 'flowbite-vue'
const toast = useToast();
const {save, isLoading: saveLoading, data: saveData, etat, eurrors, message,eurrorsApi} = useSavedata()

const key = "1c0pe574qgdnj4mfzxbn6s4cblkbj5vkugz8hkk7i7hqu9te"
import {computed, onMounted, reactive, ref, watch} from "vue";
import {Alert} from "flowbite-vue";
import axios from "axios";
import Editor from '@tinymce/tinymce-vue'









const init = reactive({
    language: 'fr_FR',
    height: 300,
    menubar: false,
    content_css: false,
    // skin: false,
    // plugins: plugins,
    toolbar:  ' bold italic underline strikethrough | fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignjustify|bullist numlist |outdent indent blockquote | undo redo | axupimgs | removeformat | table | emoticons',
    quickbars_insert_toolbar: false,
    branding: false,
});




const  { post , isEditing} = storeToRefs(postsStore)
const posts = ref({})
const postsdata = ref([])

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

const loadData = async () => {
    try {
        const res = await  axios.get('/user/api/posts')
        post.value =  post.value.id ?  post.value: res.data['post'] ;
        posts.value =res.data  ;
        postsdata.value =res.data['data'] ;
        postsStore.setPosts(res.data)


    }catch (err) {
        console.log(err)
    }
}

onMounted(async ()=>{
  await  loadData()
})

const  saveProfil = async ()=>{
  await  save('/user/api/posts',   post.value)
  if (!etat.value){
    toast.error(message.value)
    return   console.log(eurrors.value)
  }
  loadData()
  postsStore.isEditing = false

  toast.success("L'article a   bien été mis à jour !")
}


const resetPost = () => {
    postsStore.isEditing = true ;
    post.value = {}
    window.location.href ='/user/userblog'
}

</script>

<style scoped>

</style>
