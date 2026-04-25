<template>
    <button class="inline-block py-4 px-2 font-semibold hover:bg-sky-100" >
        <my-modal>
            <template v-slot:action>
                <UserIcon class=" rounded-full h-8 w-8 text-sky-900 ring" ></UserIcon>
            </template>
            <template v-slot:title>
                <button @click.prevent="closeModal">
                    <XMarkIcon  :active="active"   class="mr-2 h-8 w-8 text-sky-900"  aria-hidden="true"></XMarkIcon>
                </button>
            </template>
            <login></login>
        </my-modal>
    </button>

</template>

<script setup>

import {XMarkIcon} from "@heroicons/vue/20/solid";
import {useModal } from '../composable/useModal'
import {ref} from "vue";
import { LockOpenIcon,UserIcon } from '@heroicons/vue/20/solid'
const {closeModal } = useModal()
const active = ref(false)
</script>

<style scoped>

</style>
