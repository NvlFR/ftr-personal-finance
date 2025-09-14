<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>


<!-- <script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const successMessage = computed(() => page.props.flash.success);
const errorMessage = computed(() => page.props.flash.error);

// ... kode lainnya
</script>

<template>
    <div v-if="successMessage" class="bg-green-500 text-white p-4 rounded mb-4">
        {{ successMessage }}
    </div>
    <div v-if="errorMessage" class="bg-red-500 text-white p-4 rounded mb-4">
        {{ errorMessage }}
    </div>
    </template> -->
