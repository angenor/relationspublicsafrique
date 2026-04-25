import {ref} from "vue";


export  function useAuth() {

    const  currentUser = ref(null)
    const  islogin = ref(false)

    const setUser = (user) => {
      currentUser.value = user
    }
    const setIslogin = (etat) => {
        islogin.value = etat
    }


    return {currentUser,islogin,setIslogin,setUser}

}
