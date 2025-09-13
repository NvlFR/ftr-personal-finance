<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Wrench } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Financial Goals',
        href: 'financial-goals',
    },
];
</script>

<template>
    <Head title="Financial Goals" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6 lg:p-8">
            <Card class="text-center">
                <CardHeader>
                    <CardTitle class="text-2xl">Financial Goals</CardTitle>
                    <CardDescription> Buat dan lacak tujuan keuangan Anda. </CardDescription>
                </CardHeader>
                <CardContent class="flex flex-col items-center justify-center p-12">
                    <Wrench class="mb-4 h-16 w-16 text-muted-foreground" />
                    <h3 class="text-lg font-semibold">Segera Hadir</h3>
                    <p class="text-muted-foreground">Halaman ini sedang dalam tahap pengembangan.</p>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
