<script setup lang="ts">
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Wrench } from 'lucide-vue-next'
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transaksi Berulang',
        href: 'recurring-transactions',
    },
];
</script>

<template>
    <Head title="Utang & Piutang" />
    <AppLayout :breadcrumbs="breadcrumbs">
  <div class="p-4 sm:p-6 lg:p-8">
    <Card class="text-center">
      <CardHeader>
        <CardTitle class="text-2xl">Pencatatan Utang & Piutang</CardTitle>
        <CardDescription>
          Catat dan kelola semua utang dan piutang Anda.
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
