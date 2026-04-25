<template>

    <template
            v-if="type === 'text' || type === 'number' || type === 'password'"
    >
        <label :for="name"
               class="block my-1 lg:my-1.5 text-sm font-medium text-gray-900 dark:text-white">{{ placeholder }}</label>
        <input
                autofocus
                @keydown="deleteErrors"
                :class="{ 'is-invalid': errorMessage }"
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

        <span v-if="errorMessage" class="invalid-feedback">{{ errorMessage }}cccccccccc</span>

    </template>




    <template v-if="type === 'textarea'">
        <div  :class="haserrors ? 'form-group  has-error' : 'form-group'">
            <label :for="name"
                   class="block my-1 lg:my-1.5 text-sm font-medium text-gray-900 dark:text-white">{{ placeholder }}</label>
            <!--      <label
                      v-if="label"
                      class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400"
                      :for="name"
                  >{{ label }}</label
                  >-->
            <textarea
                    :disabled="disabled"
                    :required="required"
                    :name="name"
                    :class="{ 'is-invalid': haserrors }"
                    autocomplete="off"
                    @input="$emit('update:modelValue',$event.target.value)"
                    :value="modelValue"
                    class="form-control"
                    :id="name"
                    :placeholder="placeholder || label"
            ></textarea>
            <span v-if="haserrors" class="invalid-feedback">{{ errors }}</span>
        </div>
    </template>

    <template v-if="type === 'checkbox'">
        <div :class="haserrors ? 'form-group  has-error' : 'form-group'">
            <label v-if="label" class="control-label inline-block " :for="name">
                <input
                        type="checkbox"
                        :disabled="disabled"
                        :required="required"
                        :name="name"
                        :class="{ 'is-invalid': haserrors }"
                        @input="$emit('update:modelValue',$event.target.checked)"
                        :id="name"
                        :checked="modelValue"
                        class="rounded inline-block"
                />
                <span class="ml-2">{{ label }} </span>
            </label>
            <span v-if="haserrors" class="invalid-feedback">{{ errors }}</span>
        </div>
    </template>
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
        }
    }



)

</script>

<style scoped>

.form-control{
    @apply  inline-block
    w-full
    px-3
    py-1.5
    text-base
    font-normal
    text-gray-700
    bg-white bg-clip-padding
    border border-solid border-gray-400
    rounded
    transition
    ease-in-out
    m-0
    focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none ;
}
.invalid-feedback {
    @apply text-red-500 text-xs pt-1
}

.is-invalid {
    @apply border-2 border-red-500
}
</style>
