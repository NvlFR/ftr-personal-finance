<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { MoreHorizontal, PlusCircle, Eye, Pencil, Trash2 } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Budgeting',
        href: 'budgets',
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

interface FilterData {
    month: number;
    year: number;
}

// Mendefinisikan props yang diterima dari controller
const props = defineProps<{
    budgetData: Budget[];
    filters: FilterData;
}>();

// Fungsi untuk format mata uang
const formatCurrency = (value: string) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(parseFloat(value));
};

const getMonthName = (monthNumber: number) => {
    const date = new Date();
    date.setMonth(monthNumber - 1);
    return new Intl.DateTimeFormat('id-ID', { month: 'long' }).format(date);
};
</script>

<template>
    <Head title="Budgeting" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Rencana Anggaran</h1>
                    <p class="text-muted-foreground">
                        Lihat dan kelola anggaran Anda untuk bulan {{ getMonthName(props.filters.month) }} {{ props.filters.year }}.
                    </p>
                </div>
                <Button as-child>
                    <Link :href="route('budgets.create')">
                        <PlusCircle class="w-4 h-4 mr-2" />
                        Buat Budget Baru
                    </Link>
                </Button>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Daftar Budget</CardTitle>
                    <CardDescription>Menampilkan semua anggaran yang telah ditetapkan.</CardDescription>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Kategori</TableHead>
                                <TableHead>Jumlah Anggaran</TableHead>
                                <TableHead>Realisasi Pengeluaran</TableHead>
                                <TableHead>Sisa Anggaran</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="budget in props.budgetData" :key="budget.category_id">
                                <TableCell class="font-medium flex items-center gap-2">
                                    <span>{{ budget.category_icon }}</span>
                                    <span>{{ budget.category_name }}</span>
                                </TableCell>
                                <TableCell>{{ formatCurrency(budget.budgeted_amount) }}</TableCell>
                                <TableCell>{{ formatCurrency(budget.actual_spending) }}</TableCell>
                                <TableCell :class="parseFloat(budget.remaining) < 0 ? 'text-red-600' : 'text-green-600'">
                                    {{ formatCurrency(budget.remaining) }}
                                </TableCell>
                                <TableCell class="text-right">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button variant="ghost" size="icon">
                                                <MoreHorizontal class="w-4 h-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent>
                                            <DropdownMenuItem as-child>
                                                <Link :href="`/budgets/${budget.category_id}`" class="flex items-center">
                                                    <Eye class="w-4 h-4 mr-2" />Lihat
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem class="flex items-center">
                                                <Pencil class="w-4 h-4 mr-2" />Edit
                                            </DropdownMenuItem>
                                            <DropdownMenuItem class="flex items-center text-red-500">
                                                <Trash2 class="w-4 h-4 mr-2" />Hapus
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
