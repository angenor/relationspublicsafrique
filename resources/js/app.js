/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import "./bootstrap";
import { createApp } from "vue";
import "flowbite";
/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});

import ExampleComponent from "./components/ExampleComponent.vue";
import MyAuth from "./components/MyAuth.vue";
import MyModal from "./components/MyModal.vue";
import Login from "./components/Login.vue";
import inscription from "./components/inscription.vue";
import apploading from "./components/apploading.vue";
import cnx from "./components/cnx.vue";
import binput from "./components/binput.vue";
import InputText from "./components/InputText.vue";
import connexion from "./components/connexion.vue";
import formAddPofil from "./components/form-add-profil.vue";
import menuavatar from "./components/profil/menuavatar.vue";
import userProfilAvatar from "./components/profil/user-profil-avatar.vue";
import indexuserblog from "./components/userblog/indexuserblog.vue";
import blugUploadImage from "./components/userblog/blug-upload-image.vue";
import rechercherProfil from "./components/rechercher-profil.vue";
import blogPostsList from "./components/blog-posts-list.vue";

app.component("example-component", ExampleComponent);
app.component("my-modal", MyModal);
app.component("my-auth", MyAuth);
app.component("login", Login);
app.component("inscription", inscription);
app.component("apploading", apploading);
app.component("cnx", cnx);
app.component("binput", binput);
app.component("tinput", InputText);
app.component("connexion", connexion);
app.component("form-add-profil", formAddPofil);
app.component("menu-avatar", menuavatar);
app.component("user-profil-avatar", userProfilAvatar);
app.component("indexuserblog", indexuserblog);
app.component("blug-upload-image", blugUploadImage);
app.component("rechercher-profil", rechercherProfil);
app.component("blog-posts-list", blogPostsList);

import Toast from "vue-toastification";
// Import the CSS or use your own!
import "vue-toastification/dist/index.css";
const options = {};
import { createPinia } from "pinia";
import piniaPluginPersistedstate from "pinia-plugin-persistedstate";
const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);

app.use(pinia);
app.use(Toast, options);
app.mount("#app");
