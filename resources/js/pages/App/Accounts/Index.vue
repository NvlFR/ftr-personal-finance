<script setup lang="ts">
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Wrench } from 'lucide-vue-next'
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manajemen Akun & Dompet',
        href: 'accounts',
    },
];
</script>

<template>
    <Head title="Manajemen Akun & Dompet" />
    <AppLayout :breadcrumbs="breadcrumbs">
  <div class="p-4 sm:p-6 lg:p-8">
    <Card class="text-center">
      <CardHeader>
        <CardTitle class="text-2xl">Manajemen Akun & Dompet</CardTitle>
        <CardDescription>
          Kelola semua akun bank dan dompet digital Anda.
        </CardDescription>
      </CardHeader>
      <CardContent class="flex flex-col items-center justify-center p-12">
        <Wrench class="h-16 w-16 text-muted-foreground mb-4" />
        <h3 class="text-lg font-semibold">Segera Hadir</h3>
        <p class="text-muted-foreground">
          Halaman ini sedang dalam tahap pengembangan.
        </p>
      </CardContent>
    </Card>
  </div>
  </AppLayout>
</template>