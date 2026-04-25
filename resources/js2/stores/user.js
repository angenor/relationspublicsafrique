// stores/counter.js
import { defineStore } from 'pinia'

export const useUserStore = defineStore('user', {
    state: () => ({
        user: {

        },

        isEditing:false
    }),
    getters: {
        postsdatas: (state) =>    state.posts['data'],
    },
    // could also be defined as
    // state: () => ({ count: 0 })
    actions: {
        setPosts(data) {
            this.posts = data;

        },
        setEditing(etat){
            this.isEditing = etat
        },

        setPost(post){
            this.post = post
        }

    },
    persist: true,
})
