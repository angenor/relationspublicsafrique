<template>
    <div class="form-group">

        <template v-if="['password','text','email'].includes(type) ">
            <label :for="name" :class="[ haserrors? ' text-red-700':'text-gray-900']"  >{{label}}  <span v-if="required" class="text-red-600">*</span> </label>
            <input  autofocus
                    @keydown="deleteErrors"
                    :class="{ 'is-invalid': haserrors }"
                    class="form-control"
                    :type="type"
                    :name="name"
                    :disabled="disabled"
                    :required="required"
                    autocomplete="off"
                    @input="$emit('update:modelValue',$event.target.value)"
                    :value="modelValue"
                    :id="name"
                    :placeholder="placeholder || label">
        </template>

        <template v-if="type ==='toggle'">
            <label  class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" :value="modelValue" class="sr-only peer" :checked="modelValue"   @input="$emit('update:modelValue',$event.target.checked)">
                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">{{label}}</span>
            </label>
        </template>


        <template v-if="type ==='checkbox'">
            <div class="flex items-center">
                <input :checked="modelValue"   id="checked-checkbox" type="checkbox"   @input="$emit('update:modelValue',$event.target.checked)" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label for="checked-checkbox" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{label}}</label>
            </div>
        </template>

        <template v-if="type ==='textarea'">
            <label :for="name" :class="[ haserrors? ' text-red-700':'text-gray-900']"  >{{label}}</label>
            <textarea  autofocus
                    @keydown="deleteErrors"
                    :class="{ 'is-invalid': haserrors }"
                    class="form-control"

                    :name="name"
                    :disabled="disabled"
                    :required="required"
                    autocomplete="off"
                    @input="$emit('update:modelValue',$event.target.value)"
                    :value="modelValue"
                    :id="name"
                       :placeholder="placeholder || label"> {{modelValue}}</textarea>
        </template>

        <template v-if="type ==='select'">
            <label :for="name" :class="[ haserrors? ' text-red-700':'text-gray-900']"  >{{label}}</label>
            <select   autofocus
                    @keydown="deleteErrors"
                    :class="{ 'is-invalid': haserrors }"
                    class="form-control"

                    :name="name"
                    :disabled="disabled"
                    :required="required"
                    autocomplete="off"
                    @input="$emit('update:modelValue',$event.target.value)"
                    :value="modelValue"
                    :id="name"
                     >

                <option v-for="option in options"
                        :key="option.value"
                        :value="option.value">{{ option.label }}</option>

            </select>
        </template>

        <span class="text-sm text-red-700 italic" v-if="errorMessage">{{errorMessage}}</span>
    </div>

</template>

<script setup>

import {computed} from "vue";

const haserrors = computed(()=> props.formerrors.hasOwnProperty(props.name))
const errorMessage = computed(()=>   props.formerrors[props.name]?.join(' ') )

const deleteErrors = () => {
    if(haserrors){
        delete  props.formerrors[props.name]
    }
}

const props = defineProps({

        modelValue: {
            type: [String, Number,Boolean],

        },
        label: {
            type: String,
            default: "",
        },
        type: {
            type: String,
            default: "text",
        },
        name: {
            type: String,
        },

        required: {
            type: Boolean,
            default: false,
        },
        errors: {
            type: String,
            default: "",
        },
        inputval: {
            type: String,
            default: "",
        },

        placeholder: {
            type: String,
            default: "",
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        formerrors: {
            type: Object,
            default: {},
        },

    options: {
            type: Array,
            default: [],
        },

    }



)

</script>

<style lang="scss" scoped>

.form-group{
    @apply mb-2 ;
    label{
        @apply block mb-1 text-sm font-medium dark:text-white text-sky-950;
    }

    .form-control{
        @apply bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg
        focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600
        dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500;




     &.is-invalid {
            @apply border-2 border-red-500 bg-red-50 border border-red-500 text-red-900 placeholder-red-700
     }
    }

    &.invalid-feedback {
        @apply text-red-500 text-xs pt-1
    }
}




</style>
