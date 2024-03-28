<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import moment from "moment";

defineProps({
    stats: Array
})
</script>
<script>
export default {
  data() {
    return {
        page: 1,
        itemsPerPage: 10,
        headers: [
            { title: 'Проект', key: 'name' },
            { title: 'Время', key: 'time_spent' },
        ],
    };
  },
  computed: {
    pageCount () {
        return Math.ceil(this.stats.length / this.itemsPerPage)
      },
  }
};
</script>
<template>
    <Head title="Управление - Статистика" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Статистика</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <v-data-table
                        v-model:page="page" 
                        :items="stats"
                        :headers="headers"
                        :items-per-page="itemsPerPage"
                    >
                        <template v-slot:item.time_spent="{ value }">
                            {{ moment.utc(value * 1000).format('HH:mm:ss') }}
                        </template>
                        <template v-slot:bottom>
                            <div class="text-center pt-2">
                              <v-pagination
                                v-model="page"
                                :length="pageCount"
                              ></v-pagination>
                            </div>
                          </template>
                    </v-data-table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
