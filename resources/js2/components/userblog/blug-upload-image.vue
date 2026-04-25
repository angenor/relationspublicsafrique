<template>
  <div @click.stop="openFileFor"  class="cursor-pointer lg:h-40 w-full relative object-cover   " >
    <div @click.prevent.stop="viderAvatar" class=" bg-white absolute p-2   rounded-full shadow-lg border  border-sky-900 -top-4 -right-4">
      <TrashIcon v-if="avatarInputFile?.value" class="h-6 w-6 text-red-500 relative"> </TrashIcon>
      <PencilIcon v-else class="h-6 w-6 text-green-500 relative" @click.stop="openFileFor" > </PencilIcon>
      <!--            <Pin class="h-6 w-6 text-red-500 relative"> </Pin>-->
    </div>

    <img   :src="avatarFileUrl" class="lg:h-40 w-96 object-cover rounded-xl shadow-xl ring ring-sky-800 bg-white/90" alt="">
  </div>
  <input hidden  id="avatarFile" type="file" ref="avatarInputFile" @change="onFileChangeAvatar">
</template>

<script setup>

import {onMounted, ref} from "vue";
import {useToast} from "vue-toastification";
import { TrashIcon,PencilIcon } from '@heroicons/vue/20/solid'
import { usePostsStore}  from  '@/stores/posts'
const postsStore = usePostsStore()

const  { post,posts } = storeToRefs(postsStore)

import  { useSavefile} from "../../composable/useSavefile";
import {storeToRefs} from "pinia";
const emiter = defineEmits(['onAvatarFileSelected'])

const avatarInputFile = ref(null)
const avatarFileUrl = ref('/images/user.png')
const toast = useToast();

const extensions = ["image/jpg", "image/jpeg", "image/png"]

const { etat,saveFile,message,data ,eurrorsApi }  = useSavefile();

onMounted(()=>{

  avatarFileUrl.value = post.value.img
})
const onFileChangeAvatar =async (e) => {
  const files = e.target.files
  if (!files.length) {
    return
  }
  const file = files[0];
  if (!extensions.includes(file.type.toLowerCase())) {
    toast.error("L'image selectionnée n'est pas au bon format !")
    return
  }
  const reader = new FileReader();
  reader.onload = (event) => {
    avatarFileUrl.value = event.target.result;
    // initCropper();
  };
  reader.readAsDataURL(file);
  const  imageUrl = await saveAvatar(file)



}


const viderAvatar = () => {
  avatarFileUrl.value = '/images/user.png'
  avatarInputFile.value.value = null
  emiter('onAvatarFileSelected','images/user.png')
}
const openFileFor = () => {
  avatarInputFile.value.click()
}
const saveAvatar = async (file) => {

  await saveFile('/user/saveimage',{image:file})
  if (!etat){
    toast.error(eurrorsApi)

  }
  toast.success('mis à jours')
  post.value.image = data.value['image']
  post.value.img = location.origin+'/'+ data.value['image']
  return data.value['image'] ;
}
</script>

<style scoped>

</style>
