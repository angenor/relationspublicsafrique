import {ref} from "vue";
import {axiosService} from "../services/axios.service";

export function useSavefile(){


    const eurrorsApi = ref({})
    const eurrors = ref([])
    const data = ref({})
    const isLoading = ref(false)
    const etat = ref(false)
    const message = ref('')

    const saveFile = async (url,form)=>{
        isLoading.value = true
        eurrorsApi.value = {}
        eurrors.value = []
        etat.value = false

        try{
            const formData =  new FormData
            for (const formKey in form) {
                formData.append(formKey,form[formKey])
            }
            if(form.id){
                const  res  = await axiosService.post(url+'?_method=PUT',formData)
                data.value = res.data
                message.value= 'L\'enregistrement a bien été modifier'
            }else {
                // const form = toMutipartedForm(data)
                // const form =  (data)
                const  res  = await axiosService.post(url,formData)
                data.value = res.data
                message.value= 'L\'enregistrement a bien été ajouté '
            }
            isLoading.value = false
            eurrors.value = {}
            etat.value = true
        }catch (e) {
            etat.value = false
            isLoading.value = false
            if(e.response.status === 422){
                eurrorsApi.value =  e.response.data.errors

                for (const k in eurrorsApi.value ) {
                    let ele = {}
                    ele[k] =   eurrorsApi.value[k].join('\n')
                    eurrors.value.push( ele)
                }

                message.value= 'Merci de corriger vos erreurs ! '
            }else {
                message.value= e.response.data.message
            }



        }
    }

    return { message,data, isLoading, saveFile ,eurrors,etat,eurrorsApi}
}
