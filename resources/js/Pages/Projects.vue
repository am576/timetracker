<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import moment from "moment";
</script>
<script>
    export default {
  props: {
    projects: Array
  },
  data() {
    return {
      projectsProp: this.projects,
      projectAddress: '',
      newTaskName: '',
      projectToAdd: null,
      creatingProject: false,
      creatingTask: false,
      project_rules: [
         value => !!value || 'Адрес не должен быть пустым'
      ],
      task_rules: [
         value => !!value || 'Название не должно быть пустым'
      ],
      projectHovered: null,
      taskHovered: null
    };
  },
  methods: {
    closeProjectModal() {
        this.creatingProject = false;
    },
    showProjectModal() {
        this.creatingProject = true;
    },
    closeTaskModal() {
        this.creatingTask = false;
        this.projectToAdd = null;
    },
    showTaskModal(project_index) {
        this.creatingTask = true;
        this.projectToAdd = project_index;
    },
    async addProject() {
        if (this.project_rules.every(rule => rule(this.projectAddress) && this.projectAddress)) {
            try {
                const url = '/dashboard/projects';
                const data = {
                    name: this.projectAddress
                }
                const response = await axios.post(url, data);
                this.closeProjectModal();
                this.projectsProp.unshift({
                    name: this.projectAddress
                })
                this.projectAddress = '';
            } catch (error) {
                console.error(error);
            }
        }
    },
    setHoveredProject(index) {
      this.projectHovered = index;
    },
    clearHoveredProject() {
      this.projectHovered = null;
    },
    setHoveredTask(index) {
      this.taskHovered = index;
    },
    clearHoveredTask() {
      this.taskHovered = null;
    },
    async addTask(project_index) {
        if (this.task_rules.every(rule => rule(this.newTaskName) && this.newTaskName)) {
            try {
                const project_id = this.projectsProp[project_index].id;
                const url = `/dashboard/projects/${project_id}/add-task`;
                const data = {
                    name: this.newTaskName
                }
                const response = await axios.post(url, data);
                this.closeTaskModal();
                this.projectsProp[project_index].tasks.unshift({
                    name: this.newTaskName
                })
            } catch (error) {
                console.error(error);
            }
        }
    }
  }
};
</script>
<template>
    <Head title="Управление" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Проекты
                <a href="#" class="text-green-500 hover:text-green-600" @click="showProjectModal">
                    <v-icon
                    icon="mdi-plus"/>
                </a>
            </h2>
        </template>

        <Modal :show="creatingProject" :maxWidth="'md'" @close="closeProjectModal">
            <v-card
            title="Новый проект"
            >
                <v-card-text>
                    <v-form @submit.prevent="addProject">
                        <v-text-field
                        v-model="projectAddress"
                        label="Адрес"
                        :rules="project_rules"
                        ></v-text-field>
                        <v-btn color="#5865f2" type="submit">Добавить</v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </Modal>
        <Modal :show="creatingTask" :maxWidth="'md'" @close="closeTaskModal">
            <v-card
            title="Новая задача"
            >
                <v-card-text>
                    <v-form @submit.prevent="addTask(projectToAdd)">
                        <v-text-field
                        v-model="newTaskName"
                        label="Задача"
                        :rules="rules"
                        ></v-text-field>
                        <v-btn color="#5865f2" type="submit">Добавить</v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </Modal>
        <div class="main-panel py-12">
            <div class="max-w-70xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="projects-container p-6 text-gray-900 dark:text-gray-100">
                        <v-card v-for="(project, projectIndex) in projectsProp " :key="project.id"
                        elevation="16"
                        color="indigo"
                        @mouseover="setHoveredProject(projectIndex)"
                        @mouseout="clearHoveredProject"
                        >
                            <v-card-item>
                                <v-card-title>
                                    {{ project.name }}
                                    <v-btn density="compact" icon="mdi-plus">
                                        <v-icon @click="showTaskModal(projectIndex)">
                                            mdi-plus
                                          </v-icon>
                                        <v-tooltip
                                            activator="parent"
                                            location="top"
                                        >Добавить задачу</v-tooltip>
                                    </v-btn>
                                </v-card-title>
                                <v-card-subtitle>
                                    {{ moment(project.created_at).utc().format("DD.MM.YYYY HH:mm") }}
                                </v-card-subtitle>
                            </v-card-item>
                            <v-card-text>
                                <div class="flex flex-col gap-4">
                                    <div class="task-card" v-for="(task, taskIndex) in project.tasks" :key="task.id" @mouseover="setHoveredTask(taskIndex)"
                                    @mouseout="clearHoveredTask">
                                        <span>{{ task.name }}</span>
                                        <div class="task-controls" v-if="taskHovered === taskIndex">
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
    .v-card-title {
        display: flex;
        justify-content: space-between;
    }
}
</style>