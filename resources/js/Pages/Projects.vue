<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import moment from "moment";
import { ref } from 'vue';

const creatingProject = ref(false);
const closeModal = () => {
    creatingProject.value = false;

    form.reset();
};
const showModal = () => {
    creatingProject.value = true;
}
defineProps({
    projects: Array
})
</script>

<template>
    <Head title="Управление" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Проекты
                <a href="#" class="text-green-500 hover:text-green-600" @click="showModal">
                    <v-icon
                    icon="mdi-plus"/>
                </a>
            </h2>
        </template>

        <Modal :show="creatingProject" @close="closeModal">
            
        </Modal>
        <div class="main-panel py-12">
            <div class="max-w-70xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="projects-container p-6 text-gray-900 dark:text-gray-100">
                        <v-card v-for="project in projects"
                        elevation="16"
                        color="indigo"
                        >
                            <v-card-item>
                                <v-card-title>
                                    {{ project.name }}
                                </v-card-title>
                                <v-card-subtitle>
                                    {{ moment(project.created_at).utc().format("DD.MM.YYYY HH:mm") }}
                                </v-card-subtitle>
                            </v-card-item>
                            <v-card-text>
                                <div class="flex flex-col gap-4">
                                    <div class="task-card" v-for="task in project.tasks">
                                        <span>{{ task.name }}</span>
                                        <div class="task-controls">
                                            <a href="#" class="text-green-500 hover:text-green-600">
                                                <v-icon
                                                icon="mdi-play"/>
                                            </a>
                                            <a href="#" class="text-gray-400 hover:text-gray-500">
                                                <v-icon
                                                icon="mdi-pencil"/>
                                            </a>
                                            <a href="#" class="text-amber-600 hover:text-amber-700">
                                                <v-icon
                                                icon="mdi-close"/>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </v-card-text>
                        </v-card>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style lang="scss">
main {
    overflow: auto;
}
.main-panel {
    min-width: 100vw;
    max-width: 10000px;
    display: flex;
    flex-wrap: nowrap;
}
.projects-container {
    display: flex;
    gap: 40px;
    flex-wrap: nowrap;
    overflow-x: auto;
    div.v-card {
        flex: 1;
        width: 400px;
    }
    .task-card {
        font-size: 18px;
        line-height: 24px;
        display: flex;
        justify-content: space-between;
    }
}
</style>