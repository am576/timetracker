<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import moment from "moment";
import axios from 'axios';
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
        addProject() {
            if (this.project_rules.every(rule => rule(this.projectAddress) && this.projectAddress)) {
                try {
                    const url = '/dashboard/projects';
                    const data = {
                        name: this.projectAddress
                    }
                    axios.post(url, data)
                    .then(response => {

                        this.closeProjectModal();
                        let project = response.data.project;
                        project.tasks = [];
                        this.projectsProp.unshift(project)
                        this.projectAddress = '';
                    })
                    
                } catch (error) {
                    console.error(error);
                }
            }
        },
        deleteProject(id) {
            if (confirm('Вы действительно хотите удалить проект?')) {
            axios.delete(`/dashboard/projects/${id}`)
                .then(response => {
                    console.log('Project deleted successfully', response.data);
                    this.projectsProp = this.projectsProp.filter(project => project.id !== id);
                })
                .catch(error => {
                    console.error('An error occurred while deleting the project', error);
                });
            }
        },
        deleteTask(project_index, task_index) {
            if (confirm('Вы действительно хотите удалить задачу?')) {
                const project_id = this.projectsProp[project_index].id;
                const task_id = this.projectsProp[project_index].tasks[task_index].id;
                axios.delete(`/dashboard/projects/${project_id}/${task_id}`)
                    .then(response => {
                        console.log('Task deleted successfully', response.data);
                        this.projectsProp[project_index].tasks.splice(task_index, 1);
                    })
                    .catch(error => {
                        console.error('An error occurred while deleting the task', error);
                    });
                }
        },
        startTask(task_id) {
            axios.post(`/api/tasks/${task_id}/start`)
            .then(response => {
                let projectIndex = this.projectsProp.findIndex(project => project.tasks.some(task => task.id === task_id));
                if (projectIndex !== -1) {
                    let taskIndex = this.projectsProp[projectIndex].tasks.findIndex(task => task.id === task_id);
                    if (taskIndex !== -1) {
                    this.projectsProp[projectIndex].tasks[taskIndex].is_active = 1;
                    }
                }
            })
        },
        stopTask(task_id) {
            axios.post(`/api/tasks/${task_id}/stop`)
            .then(response => {
                let projectIndex = this.projectsProp.findIndex(project => project.tasks.some(task => task.id === task_id));
                if (projectIndex !== -1) {
                    let taskIndex = this.projectsProp[projectIndex].tasks.findIndex(task => task.id === task_id);
                    if (taskIndex !== -1) {
                    this.projectsProp[projectIndex].tasks[taskIndex].is_active = 0;
                    }
                }
            })
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
        addTask(project_index) {
            console.log(project_index)
            if (this.task_rules.every(rule => rule(this.newTaskName) && this.newTaskName)) {
                try {
                    const project_id = this.projectsProp[project_index].id;
                    const url = `/dashboard/projects/${project_id}/add-task`;
                    const data = {
                        name: this.newTaskName
                    }
                    axios.post(url, data)
                    .then(response => {
                        this.closeTaskModal();
                        this.projectsProp[project_index].tasks.unshift(response.data.task)    
                        console.log(this.projectsProp[project_index].tasks)
                    })
                    
                } catch (error) {
                    console.error(error);
                }
            }
        },
        formatTime(seconds) {
            return moment.utc(seconds * 1000).format('HH:mm:ss');
        },
        startTimer() {
            if(this.activeTask !== null) {
                setInterval(() => {
                    if(this.activeTask !== null) {
                        axios.get(`/api/tasks/${this.activeTask.id}/time-spent`)
                .then(response => {
                    if(this.activeTask !== null) {
                        this.activeTask.time_spent = response.data.time_spent;
                    }
                })
                .catch(error => {
                    console.error(error);
                });
                    }
                
            }, 1000);
            }
            
        }
    },
    computed: {
        activeProject() {
            return this.projects.find(project => project.tasks.some(task => task.is_active === 1));
        },
        activeTask() {
            if (this.activeProject) {
            return this.activeProject.tasks.find(task => task.is_active === 1);
            }
            return null;
        }
    },
    created() {
        this.startTimer();
    },

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
                        autocomplete="off"
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
                    <div class="projects-container p-6 text-gray-900 dark:text-gray-100" v-if="projects.length">
                        <v-card v-for="(project, projectIndex) in projectsProp " :key="project.id"
                        elevation="16"
                        color="indigo"
                        @mouseover="setHoveredProject(projectIndex)"
                        @mouseout="clearHoveredProject"
                        >
                            <v-card-item>
                                <v-card-title>
                                    <div class="flex gap-4">
                                        {{ project.name }}
                                        <v-btn density="compact" icon v-show="projectHovered === projectIndex" @click="deleteProject(project.id)">
                                            <v-icon
                                                color="amber-600"
                                                icon="mdi-close"/>
                                        </v-btn>
                                    </div>
                                    
                                    <v-btn density="compact" icon="mdi-plus" >
                                        <v-icon @click="showTaskModal(projectIndex)">
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
                                        <span v-if="(activeTask && activeTask.id === task.id) || task.time_spent > 0">
                                            {{formatTime(task.time_spent)}}
                                        </span>
                                        <div class="task-controls" v-show="projectHovered === projectIndex && taskHovered === taskIndex">
                                            <v-btn density="compact" icon v-if="!activeTask">
                                                <v-icon
                                                color="green-500"
                                                icon="mdi-play"
                                                @click="startTask(task.id)"
                                            />
                                            </v-btn>
                                            <v-btn density="compact" icon v-if="activeTask && activeTask.id === task.id">
                                                <v-icon
                                                icon="mdi-stop"
                                                @click="stopTask(task.id)"
                                            />
                                            </v-btn>
                                            <v-btn density="compact" icon @click="deleteTask(projectIndex, taskIndex)">
                                                <v-icon
                                                color="amber-600"
                                                icon="mdi-close"/>
                                            </v-btn>
                                        </div>
                                    </div>
                                </div>
                            </v-card-text>
                        </v-card>
                    </div>
                    <div v-else class="flex justify-center items-center h-full p-6">
                        Вы ещё не добавили ни одного проекта.
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