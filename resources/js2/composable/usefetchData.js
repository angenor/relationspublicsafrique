import {ref, watch} from 'vue'
import {axiosService} from "../services/axios.service.js";
import _ from 'lodash'

function getUrlPart(link) {

    const parsLink  = new URL(link)
    return parsLink;
}

export   function usefetchData() {

    const data = ref([])
    const eurrors = ref({})
    const isLoading = ref(true)
    const q = ref('')
    const getlink = ref('')

    watch(q,_.debounce( async()=>{
        await  getData(getlink.value)
    },500))


    const getData = async (link) => {
        isLoading.value = true
        console.log(isLoading.value,'DAMSSSSSSSSSSSSSSSSS')
        getlink.value = link
        eurrors.value = {}
        try {
            let  newurl = link
            if (q.value)
                newurl = link
                    .split('?')[0]+'?q='+q.value

            console.log(newurl)
            const res = await axiosService.get(newurl)
            data.value = res.data
            eurrors.value = {}
        }catch (e) {
            console.log(e.response)
            eurrors.value = e.response
        }finally {
            isLoading.value = false
        }
    }

    return { data, isLoading, getData,eurrors,q }
}
