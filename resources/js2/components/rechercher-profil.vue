
<template>
  <input v-model="q" type="text" placeholder="Recherchez un profil"
         class="max-w-md w-full rounded-full pl-16 border-gray-300 shadow">

  <div class="fixed inset-0 h-[calc(100vh-5rem)] w-screen bg-black/75 mx-auto mt-16 flex  p-5"
       v-if="showSercheModal" @click.prevent.self="showSercheModal =false">
    <div class="bg-white max-w-2xl mx-auto w-full p-5 rounded-xl shadow-2xl border">
      <div class="mb-5">
        <input v-model="q" type="text" placeholder="Recherchez un profil"
               class="w-full rounded-full pl-16 border-gray-300 shadow focus:bg-gray-200">
      </div>
      <div class="overflow-y-scroll h-[500px]  overflow-hidden">
        <div class="">
          <div class=" space-y-2 cursor-pointer" v-for="i in 20">
            <div class=" flex items-center border p-3 border-b-0">
              <img class="h-12 w-12 overflow-hidden object-cover rounded-full shadow border  "
                   src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTOSIZ6hZseAPKb42yOVWSqt00bWSi8yusbMQ&usqp=CAU"
                   alt="">
              <h3 class="text-lg font-bold text-sky-900/95 ml-4">Bienfoali Damssan </h3>
            </div>
          </div>
        </div>


      </div>
    </div>
  </div>
</template>

<script setup>
import {ref, watch} from "vue";

const q = ref('')
const showSercheModal = ref(false)

watch(q, (value)=>{
  if(value.length>0){
    showSercheModal.value = true
  }else{
    showSercheModal.value = false
  }



})

</script>

<style scoped>

</style>