<template>
    <div class="card shadow-lg border-0">
        <div class="card-header bg-gradient text-white"
            style="background: linear-gradient(45deg, #713d0b, #8b5a2b) !important;">
            <h3 class="h4 mb-0 text-center">
                <i class="fas fa-user-edit me-2"></i>
                Mon Profil
            </h3>
        </div>

        <div class="card-body p-4">
            <div v-if="eurrors.length" class="alert alert-danger">
                <ul class="mb-0">
                    <li v-for="eurror in eurrors" :key="eurror">
                        {{ Object.values(eurror).join('\n') }}
                    </li>
                </ul>
            </div>

            <form @submit.prevent="updateProfile">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <binput name="nom" label="Nom" type="text" v-model="form.nom" :formerrors="eurrorsApi" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <binput name="prenom" label="Prénom" type="text" v-model="form.prenom"
                                :formerrors="eurrorsApi" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <binput name="email" label="Email" type="email" v-model="form.email"
                                :formerrors="eurrorsApi" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <binput name="tel" label="Téléphone" type="text" v-model="form.tel"
                                :formerrors="eurrorsApi" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <binput name="fonction" label="Fonction" type="text" v-model="form.fonction"
                                :formerrors="eurrorsApi" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <binput name="domaine" label="Domaine" type="text" v-model="form.domaine"
                                :formerrors="eurrorsApi" />
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <binput name="adresse" label="Adresse" type="text" v-model="form.adresse"
                        :formerrors="eurrorsApi" />
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Biographie</label>
                    <textarea v-model="form.bio" rows="4" class="form-control"
                        placeholder="Parlez-nous de vous..."></textarea>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-share-alt me-2"></i>
                            Réseaux sociaux
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <binput name="facebook" label="Facebook" type="url" v-model="form.facebook"
                                        :formerrors="eurrorsApi" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <binput name="twitter" label="Twitter" type="url" v-model="form.twitter"
                                        :formerrors="eurrorsApi" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <binput name="youtube" label="YouTube" type="url" v-model="form.youtube"
                                        :formerrors="eurrorsApi" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <binput name="linkding" label="LinkedIn" type="url" v-model="form.linkding"
                                        :formerrors="eurrorsApi" />
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <binput name="site" label="Site Web" type="url" v-model="form.site"
                                :formerrors="eurrorsApi" />
                        </div>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input type="checkbox" v-model="form.online" id="online" class="form-check-input">
                    <label for="online" class="form-check-label fw-bold">
                        <i class="fas fa-globe me-1"></i>
                        Profil public
                    </label>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <button type="button" @click="resetForm" class="vs-btn style4">
                        <i class="fas fa-times me-2"></i>
                        Annuler
                    </button>
                    <button v-if="!saveLoading" type="submit" class="vs-btn style3">
                        <i class="fas fa-save me-2"></i>
                        Mettre à jour
                    </button>
                    <button v-else disabled type="submit" class="vs-btn style3">
                        <apploading></apploading>
                        Mise à jour...
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
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
    form.name = form.prenom
    form.title = `${form.prenom} ${form.nom}`

    await save('/user/profil', form, 'PUT')

    if (!etat.value) {
        toast.error(message.value)
        console.log(eurrors.value)
    } else {
        toast.success('Profil mis à jour avec succès!')
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
.vs-btn.style3 {
    background: linear-gradient(45deg, #713d0b, #8b5a2b);
    border: none;
    color: white;
    padding: 12px 30px;
    border-radius: 5px;
    font-weight: 600;
    text-transform: uppercase;
    transition: all 0.3s ease;
}

.vs-btn.style3:hover {
    background: linear-gradient(45deg, #5a2f08, #713d0b);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(113, 61, 11, 0.3);
}

.vs-btn.style4 {
    background: transparent;
    border: 2px solid #713d0b;
    color: #713d0b;
    padding: 10px 25px;
    border-radius: 5px;
    font-weight: 600;
    text-transform: uppercase;
    transition: all 0.3s ease;
}

.vs-btn.style4:hover {
    background: #713d0b;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(113, 61, 11, 0.3);
}

.card {
    border-radius: 15px;
    overflow: hidden;
}

.form-control:focus {
    border-color: #713d0b;
    box-shadow: 0 0 0 0.2rem rgba(113, 61, 11, 0.25);
}

.form-check-input:checked {
    background-color: #713d0b;
    border-color: #713d0b;
}

.bg-gradient {
    background: linear-gradient(45deg, #713d0b, #8b5a2b) !important;
}
</style>
