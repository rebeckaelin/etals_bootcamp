<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Chirp from "@/Components/Chirp.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Modal from "@/Components/Modal.vue"; // Lägg till en modal-komponent
import { useForm, Head } from "@inertiajs/vue3";
import { ref } from "vue";
import axios from "axios";

defineProps(["chirps"]);

const form = useForm({
    message: "",
});

const aiChirps = ref([]); // För att hålla de genererade AI-chirps
const isGenerating = ref(false); // För att hantera laddningstillstånd
const showModal = ref(false); // För att visa eller dölja modal

// Funktion för att generera chirps via OpenAI
const generateAIChirps = async () => {
    isGenerating.value = true;
    aiChirps.value = [];
    try {
        const response = await axios.post(route("chirps.generate"));
        console.log("AI Chirps response:", response.data);
        aiChirps.value = response.data.chirps;
        showModal.value = true;
    } catch (error) {
        console.error("Error generating chirps:", error);
    } finally {
        isGenerating.value = false;
    }
};

// Funktion för att posta ett valt chirp
const postAIChirp = (chirp) => {
    const cleanedChirp = chirp.replace(/^\d+\.\s*/, "");
    form.message = cleanedChirp;
    form.post(route("chirps.store"), {
        onSuccess: () => {
            form.reset();
            showModal.value = false;
        },
    });
};
</script>

<template>
    <Head title="Chirps" />

    <AuthenticatedLayout>
        <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
            <form
                @submit.prevent="
                    form.post(route('chirps.store'), {
                        onSuccess: () => form.reset(),
                    })
                "
            >
                <textarea
                    v-model="form.message"
                    placeholder="What's on your mind?"
                    class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                ></textarea>
                <InputError :message="form.errors.message" class="mt-2" />
                <div class="flex justify-between items-center mt-4">
                    <PrimaryButton>Chirp</PrimaryButton>
                    <button
                        type="button"
                        @click.prevent="generateAIChirps"
                        :disabled="isGenerating"
                        class="bg-white-500 text-black px-4 py-2 rounded shadow hover:bg-grey-700 disabled:opacity-50"
                    >
                        <span v-if="!isGenerating">Generate AI Chirp</span>
                        <span v-else>Generating...</span>
                    </button>
                </div>
            </form>

            <div class="mt-6 bg-white shadow-sm rounded-lg divide-y">
                <Chirp v-for="chirp in chirps" :key="chirp.id" :chirp="chirp" />
            </div>
        </div>

        <!-- Modal för att visa AI-genererade chirps -->
        <Modal :show="showModal">
            <div class="space-y-4 p-4">
                <div
                    v-for="(chirp, index) in aiChirps"
                    :key="index"
                    class="p-4 bg-yellow-100 rounded shadow"
                >
                    <p>{{ chirp }}</p>
                    <button
                        @click="postAIChirp(chirp)"
                        class="mt-3 bg-yellow-200 text-black px-4 py-2 rounded shadow hover:bg-yellow-300"
                    >
                        Post this Chirp
                    </button>
                </div>
                <div class="flex justify-end">
                    <button
                        @click="showModal = false"
                        class="mt-2 bg-red-500 text-white px-4 py-2 rounded shadow hover:bg-red-700"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
