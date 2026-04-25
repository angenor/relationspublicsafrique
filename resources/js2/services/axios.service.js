// const API_URL = 'http://127.0.0.1:8000/api/v1';
const API_URL = '';
// const API_URL = 'https://apinovissi.appsridge.com/api/v1';
// const API_URL = 'http://damssan.com:8000/api/v1';
// const API_URL = process.env.VUE_APP_URL
const data =  JSON.parse( localStorage.getItem('vuex') )

import axios from 'axios';
import {ref} from 'vue';


const storage = ref(  data?.auth || {})

const axiosService = axios.create({
    baseURL: API_URL,
    timeout: 10000,
    headers: {
        'Accept': 'application/json',
        // 'Content-Type': 'application/json',
        // "Content-Type": "multipart/form-date",

        // 'X-Requested-With': 'XMLHttpRequest',
        // 'Access-Control-Allow-Origin': '*'
    }
})
if (storage && storage.value.token) {
    axiosService.defaults.headers.common["Authorization"] = 'Bearer ' +storage.value.token;
} else {
    delete axiosService.defaults.headers.common["Authorization"];
}
export { axiosService }
