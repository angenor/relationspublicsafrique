<template>
    <div class="card shadow-lg border-0 overflow-hidden mt-5 mb-5">
        <div class="position-relative">
            <img src="/images/front/ban.jpg" alt="Bannière" class="img-fluid w-100"
                style="height: 300px; object-fit: cover;">
            <div class="position-absolute top-0 start-0 w-100 h-100"
                style="background: linear-gradient(to top, rgba(113, 61, 11, 0.8), transparent);"></div>
        </div>

        <div class="d-flex flex-column align-items-center justify-content-center position-relative">
            <div class="position-relative" style="margin-top: -120px;">
                <img :src="form.image || '/images/user2.jpg'" alt="Photo de profil"
                    class="img-fluid rounded-circle border border-5 border-white shadow-lg"
                    style="width: 200px; height: 200px; object-fit: cover;">
                <label class="btn btn-light btn-sm position-absolute bottom-0 end-0 rounded-circle shadow"
                    style="cursor: pointer;">
                    <input type="file" @change="uploadImage" class="d-none" accept="image/*">
                    <i class="fas fa-camera"></i>
                </label>
            </div>

            <div class="w-100 d-flex justify-content-center pb-4"
                style="background-color: rgba(var(--theme-color-rgb), 0.1);">
                <div class="d-flex align-items-center flex-column">
                    <div class="mt-2">
                        <h2 class="h3 fw-bold text-theme mb-0">{{ form.prenom }} {{ form.nom }}</h2>
                    </div>
                    <div class="mt-2">
                        <h3 class="h5 text-muted mb-0">{{ form.fonction || 'Membre' }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-5">
            <div v-if="eurrors.length" class="alert alert-danger mb-4">
                <ul class="mb-0">
                    <li v-for="eurror in eurrors" :key="eurror">
                        {{ Object.values(eurror).join('\n') }}
                    </li>
                </ul>
            </div>

            <form @submit.prevent="updateProfile" class="needs-validation" novalidate>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" v-model="form.nom"
                            :class="{'is-invalid': eurrorsApi.nom}" required>
                        <div v-if="eurrorsApi.nom" class="invalid-feedback">
                            {{ eurrorsApi.nom[0] }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" v-model="form.prenom"
                            :class="{'is-invalid': eurrorsApi.prenom}" required>
                        <div v-if="eurrorsApi.prenom" class="invalid-feedback">
                            {{ eurrorsApi.prenom[0] }}
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" v-model="form.email"
                            :class="{'is-invalid': eurrorsApi.email}" required>
                        <div v-if="eurrorsApi.email" class="invalid-feedback">
                            {{ eurrorsApi.email[0] }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="tel" class="form-label">Téléphone</label>
                        <input type="text" class="form-control" id="tel" name="tel" v-model="form.tel"
                            :class="{'is-invalid': eurrorsApi.tel}">
                        <div v-if="eurrorsApi.tel" class="invalid-feedback">
                            {{ eurrorsApi.tel[0] }}
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="fonction" class="form-label">Fonction</label>
                        <input type="text" class="form-control" id="fonction" name="fonction" v-model="form.fonction"
                            :class="{'is-invalid': eurrorsApi.fonction}">
                        <div v-if="eurrorsApi.fonction" class="invalid-feedback">
                            {{ eurrorsApi.fonction[0] }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="domaine" class="form-label">Domaine</label>
                        <input type="text" class="form-control" id="domaine" name="domaine" v-model="form.domaine"
                            :class="{'is-invalid': eurrorsApi.domaine}">
                        <div v-if="eurrorsApi.domaine" class="invalid-feedback">
                            {{ eurrorsApi.domaine[0] }}
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="adresse" class="form-label">Adresse</label>
                    <input type="text" class="form-control" id="adresse" name="adresse" v-model="form.adresse"
                        :class="{'is-invalid': eurrorsApi.adresse}">
                    <div v-if="eurrorsApi.adresse" class="invalid-feedback">
                        {{ eurrorsApi.adresse[0] }}
                    </div>
                </div>

                <div class="mb-4">
                    <label for="bio" class="form-label">Biographie</label>
                    <textarea v-model="form.bio" rows="4" class="form-control" id="bio" name="bio"
                        placeholder="Parlez-nous de vous..."></textarea>
                </div>

                <hr class="my-5">
                <h3 class="h5 fw-bold text-theme mb-4">
                    <i class="fas fa-share-alt me-2"></i>
                    Réseaux sociaux
                </h3>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="facebook" class="form-label">Facebook</label>
                        <input type="url" class="form-control" id="facebook" name="facebook" v-model="form.facebook"
                            :class="{'is-invalid': eurrorsApi.facebook}"
                            placeholder="https://facebook.com/votre-profil">
                        <div v-if="eurrorsApi.facebook" class="invalid-feedback">
                            {{ eurrorsApi.facebook[0] }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="twitter" class="form-label">Twitter</label>
                        <input type="url" class="form-control" id="twitter" name="twitter" v-model="form.twitter"
                            :class="{'is-invalid': eurrorsApi.twitter}" placeholder="https://twitter.com/votre-profil">
                        <div v-if="eurrorsApi.twitter" class="invalid-feedback">
                            {{ eurrorsApi.twitter[0] }}
                        </div>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="youtube" class="form-label">YouTube</label>
                        <input type="url" class="form-control" id="youtube" name="youtube" v-model="form.youtube"
                            :class="{'is-invalid': eurrorsApi.youtube}" placeholder="https://youtube.com/votre-chaine">
                        <div v-if="eurrorsApi.youtube" class="invalid-feedback">
                            {{ eurrorsApi.youtube[0] }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="linkding" class="form-label">LinkedIn</label>
                        <input type="url" class="form-control" id="linkding" name="linkding" v-model="form.linkding"
                            :class="{'is-invalid': eurrorsApi.linkding}"
                            placeholder="https://linkedin.com/in/votre-profil">
                        <div v-if="eurrorsApi.linkding" class="invalid-feedback">
                            {{ eurrorsApi.linkding[0] }}
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="site" class="form-label">Site Web</label>
                    <input type="url" class="form-control" id="site" name="site" v-model="form.site"
                        :class="{'is-invalid': eurrorsApi.site}" placeholder="https://votre-site.com">
                    <div v-if="eurrorsApi.site" class="invalid-feedback">
                        {{ eurrorsApi.site[0] }}
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input type="checkbox" v-model="form.online" id="online" class="form-check-input">
                    <label for="online" class="form-check-label text-theme fw-bold">
                        <i class="fas fa-globe me-1"></i>
                        Rendre mon profil public
                    </label>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <button type="button" @click="resetForm" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>
                        Annuler
                    </button>
                    <button v-if="!saveLoading" type="submit" class="btn text-white bg-theme">
                        <i class="fas fa-save me-2"></i>
                        Mettre à jour
                    </button>
                    <button v-else disabled type="submit" class="btn text-white bg-theme">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Mise à jour...
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Alert } from 'flowbite-vue'
import { useToast } from "vue-toastification";
import { reactive, ref, onMounted } from "vue";
import { useSavedata } from '../composable/useSavedata'

const toast = useToast();
const { save, isLoading: saveLoading, data: saveData, etat, eurrors, message, eurrorsApi } = useSavedata()

const form = reactive({
    nom: '',
    prenom: '',
    name: '',
    email: '',
    tel: '',
    fonction: '',
    domaine: '',
    adresse: '',
    bio: '',
    facebook: '',
    twitter: '',
    youtube: '',
    linkding: '',
    site: '',
    contact: '',
    title: '',
    online: false,
    image: ''
})

const originalForm = ref({})

onMounted(async () => {
    // Charger les données du profil existant
    try {
        const response = await fetch('/user/api/profil')
        if (response.ok) {
            const data = await response.json()
            if (data.profil) {
                Object.keys(form).forEach(key => {
                    if (data.profil[key] !== undefined) {
                        form[key] = data.profil[key]
                    }
                })
                form.email = data.email || ''
                form.name = data.name || ''
            }
            // Sauvegarder l'état original
            originalForm.value = { ...form }
        }
    } catch (error) {
        console.error('Erreur lors du chargement du profil:', error)
    }
})

const uploadImage = async (event) => {
    const file = event.target.files[0]
    if (!file) return

    const formData = new FormData()
    formData.append('image', file)

    try {
        const response = await fetch('/user/saveimage', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })

        const result = await response.json()
        if (result.success) {
            form.image = result.image
            toast.success('Image mise à jour avec succès!')
        } else {
            toast.error('Erreur lors du téléchargement de l\'image')
        }
    } catch (error) {
        toast.error('Erreur lors du téléchargement de l\'image')
    }
}

const updateProfile = async () => {
    // Préparer les données
    form.name = form.prenom
    form.title = `${form.prenom} ${form.nom}`

    // Ajouter l'ID de l'utilisateur pour la mise à jour
    if (!form.id && userData.value && userData.value.id) {
        form.id = userData.value.id
    }

    // Utiliser la route simple POST
    await save('/user/profil-update', form, 'POST')

    if (!etat.value) {
        toast.error(message.value)
        console.log(eurrors.value)
    } else {
        toast.success('Profil mis à jour avec succès!')
        // Mettre à jour l'état original
        originalForm.value = { ...form }
    }
}

const resetForm = () => {
    Object.keys(originalForm.value).forEach(key => {
        form[key] = originalForm.value[key]
    })
    toast.info('Modifications annulées')
}
</script>

<style scoped>
/* Styles personnalisés pour le composant de profil */
.card {
    border-radius: 1rem;
}

.form-control:focus {
    border-color: var(--theme-color);
    box-shadow: 0 0 0 0.2rem rgba(var(--theme-color-rgb), 0.25);
}

.form-check-input:checked {
    background-color: var(--theme-color);
    border-color: var(--theme-color);
}

.btn:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease-in-out;
}

/* Animation pour le spinner */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}
</style>
