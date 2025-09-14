<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { ArrowLeft, Pencil } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Budgeting',
        href: '/budgets',
    },
    {
        title: 'Detail Budget',
        href: '', // Akan diganti dengan ID dinamis
    },
];

// Tipe data berdasarkan struktur dari backend
interface Budget {
    category_id: number;
    category_name: string;
    category_icon: string;
    budgeted_amount: string;
    actual_spending: string;
    remaining: string;
}

// Mendefinisikan props yang diterima dari controller
const props = defineProps<{
    budget: Budget;
}>();

// Fungsi untuk format mata uang
const formatCurrency = (value: string) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(parseFloat(value));
};
</script>

<template>
    <Head title="Detail Budget" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 max-w-2xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <Button variant="outline" as-child>
                    <Link :href="route('budgets.index')">
                        <ArrowLeft class="w-4 h-4 mr-2" />
                        Kembali ke Daftar Budget
                    </Link>
                </Button>
                <Button>
                    <Pencil class="w-4 h-4 mr-2" />
                    Edit Budget
                </Button>
            </div>
            <Card>
                <CardHeader>
                    <CardTitle>Detail Anggaran</CardTitle>
                    <CardDescription>Rincian lengkap dari anggaran yang Anda tetapkan.</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Kategori</span>
                            <span class="font-medium flex items-center gap-2">
                                <span>{{ props.budget.category_icon }}</span>
                                <span>{{ props.budget.category_name }}</span>
                            </span>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Jumlah Anggaran</span>
                            <span class="font-medium">{{ formatCurrency(props.budget.budgeted_amount) }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Realisasi Pengeluaran</span>
                            <span class="font-medium">{{ formatCurrency(props.budget.actual_spending) }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Sisa Anggaran</span>
                            <span class="font-bold text-xl" :class="parseFloat(props.budget.remaining) < 0 ? 'text-red-600' : 'text-green-600'">
                                {{ formatCurrency(props.budget.remaining) }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
