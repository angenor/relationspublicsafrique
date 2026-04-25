<template>
    <div class="z-20 relative">
        <Menu as="div" class="relative inline-block text-left">
            <div>
                <MenuButton  class="inline-flex w-full justify-center rounded-md  bg-opacity-50 px-4 py-2 -my-2 text-sm font-medium text-white hover:bg-opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75">
                    <div class="absolute">
                        <menu-avatar :user="currentUser.name"></menu-avatar>

                    </div>
                    <ChevronDownIcon class="ml-2 -mr-1 h-6 w-6 text-sky-900 hover:text-sky-100" aria-hidden="true" />
                </MenuButton>
            </div>
            <transition
                    enter-active-class="transition duration-100 ease-out"
                    enter-from-class="transform scale-95 opacity-0"
                    enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-75 ease-in"
                    leave-from-class="transform scale-100 opacity-100"
                    leave-to-class="transform scale-95 opacity-0"
            >
                <MenuItems class="absolute right-0 mt-5 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                    <div class="px-1 pb-1">

                        <template v-if="islogin">

                            <MenuItem v-slot="{ active }">
                                <a href="/user/profil" :class="[ active ? 'bg-sky-700 text-white' : 'text-gray-900',  'group flex w-full items-center rounded-md px-2 py-2 text-sm', ]" >
                                    <PencilSquareIcon
                                        :active="active"
                                        class="mr-2 h-5 w-5 text-sky-800"
                                        :class="[ active ? 'text-sky-100' : '']"
                                        aria-hidden="true"
                                    />
                                    Editer mon profil
                                </a>
                            </MenuItem>
<!--
                          <MenuItem v-slot="{ active }">
                            <a href="/user/userblog" :class="[ active ? 'bg-sky-700 text-white' : 'text-gray-900',  'group flex w-full items-center rounded-md px-2 py-2 text-sm', ]" >
                              <NewspaperIcon
                                  :active="active"
                                  class="mr-2 h-5 w-5 text-sky-800"
                                  :class="[ active ? 'text-sky-100' : '']"
                                  aria-hidden="true"
                              />
                              Editer Blog
                            </a>
                          </MenuItem>-->


                        </template>





                    </div>
                    <div class="px-1 py-1">
                        <template v-if="islogin">
                            <MenuItem v-slot="{ active }">
                                <button @click.prevent="logout" :class="[ active ? 'bg-sky-700 text-white' : 'text-gray-900',  'group flex w-full items-center rounded-md px-2 py-2 text-sm', ]" >
                                    <LockClosedIcon
                                        :active="active"
                                        class="mr-2 h-5 w-5 text-red-500"
                                        :class="[ active ? 'text-sky-100 text-white' : '']"
                                        aria-hidden="true"
                                    />
                                    Se déconnecter ffffffffffff
                                </button>
                            </MenuItem>
                        </template>
                        <template v-else>
                            <MenuItem v-slot="{ active }">
                                <a href="/login"  :class="[ active ? 'bg-sky-700 text-white' : 'text-gray-900',  'group flex w-full items-center rounded-md px-2 py-2 text-sm', ]" >
                                    <LockOpenIcon
                                        :active="active"
                                        class="mr-2 h-5 w-5 text-red-500"
                                        :class="[ active ? 'text-sky-100 ' : '']"
                                        aria-hidden="true"
                                    />
                                    Se Connecter dddd
                                </a>
                            </MenuItem>
                        </template>



                    </div>


                </MenuItems>
            </transition>
        </Menu>
    </div>
</template>

<script setup>

import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { ChevronDownIcon,PencilIcon,PencilSquareIcon,LockClosedIcon,LockOpenIcon,NewspaperIcon } from '@heroicons/vue/20/solid'
import { useToast } from "vue-toastification";
import {computed} from "vue";
const toast = useToast();

const props = defineProps({
    islogin:{ default :false },
    user: { default : { },type:String}
})
const currentUser = computed(()=>JSON.parse(props.user))
const logout = () => {
  if ( confirm('Se déconnecter ?') ){

      try{
          axios.post('/logout')
          toast.success('Vous êtes déconnecté')
          setTimeout(()=>{ window.location = '/'},500)

      }catch (e) {
          toast.error('Rien ')

      }
  }
}
</script>
