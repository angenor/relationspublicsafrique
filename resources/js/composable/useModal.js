import { ref } from 'vue'

const isOpen = ref(false)


export function   useModal (){


    function closeModal() {
        isOpen.value = false
    }
    function openModal() {
        isOpen.value = true
    }
    return { closeModal,openModal,isOpen}
}
