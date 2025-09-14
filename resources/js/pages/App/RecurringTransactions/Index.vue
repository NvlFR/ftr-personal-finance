<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { ArrowUpRight, ArrowDownLeft, MoreHorizontal, PlusCircle, Eye, Pencil, Trash2, PauseCircle, PlayCircle } from 'lucide-vue-next';
import { ref } from 'vue'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transaksi Berulang',
        href: 'recurring-transactions',
    },
];

// Tipe data untuk props
interface Account {
    id: number;
    name: string;
}

interface Category {
    id: number;
    name: string;
}

interface RecurringTransaction {
    id: number;
    type: 'income' | 'expense';
    amount: string;
    description: string;
    frequency: 'daily' | 'weekly' | 'monthly' | 'yearly';
    interval: number;
    start_date: string;
    end_date: string | null;
    next_due_date: string;
    is_active: boolean;
    account: Account;
    category: Category;
}

interface Props {
    recurringTransactions: RecurringTransaction[];
}

// Menerima data dari backend
const props = defineProps<Props>();

// Fungsi untuk format mata uang
const formatCurrency = (value: string) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(parseFloat(value));
};

const formatFrequency = (frequency: string, interval: number) => {
    const intervals = {
        'daily': 'Hari',
        'weekly': 'Minggu',
        'monthly': 'Bulan',
        'yearly': 'Tahun',
    };
    const frequencyText = intervals[frequency as keyof typeof intervals];
    return `Setiap ${interval} ${frequencyText}`;
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', { dateStyle: 'medium' });
};

</script>

<template>
<Head title="Transaksi Berulang" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Daftar Transaksi Berulang</h1>
                    <p class="text-muted-foreground">Lihat dan kelola semua transaksi yang berulang.</p>
                </div>
                <Button as-child>
                    <Link :href="route('recurring-transactions.create')">
                        <PlusCircle class="w-4 h-4 mr-2" />
                        Buat Transaksi Berulang
                    </Link>
                </Button>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Jadwal Transaksi</CardTitle>
                    <CardDescription>Menampilkan semua transaksi berulang yang telah Anda buat.</CardDescription>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Tipe</TableHead>
                                <TableHead>Deskripsi</TableHead>
                                <TableHead>Jumlah</TableHead>
                                <TableHead>Frekuensi</TableHead>
                                <TableHead>Tanggal Berikutnya</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="transaction in props.recurringTransactions" :key="transaction.id">
                                <TableCell>
                                    <Badge :variant="transaction.type === 'income' ? 'default' : 'destructive'" class="capitalize flex items-center gap-1 w-fit">
                                        <ArrowUpRight v-if="transaction.type === 'income'" class="w-3 h-3" />
                                        <ArrowDownLeft v-else class="w-3 h-3" />
                                        {{ transaction.type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="font-medium">{{ transaction.description }}</TableCell>
                                <TableCell :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'">
                                    {{ formatCurrency(transaction.amount) }}
                                </TableCell>
                                <TableCell>{{ formatFrequency(transaction.frequency, transaction.interval) }}</TableCell>
                                <TableCell>{{ formatDate(transaction.next_due_date) }}</TableCell>
                                <TableCell>
                                    <Badge :variant="transaction.is_active ? 'default' : 'secondary'">
                                        <span v-if="transaction.is_active" class="flex items-center gap-1">
                                            <PlayCircle class="w-3 h-3" /> Aktif
                                        </span>
                                        <span v-else class="flex items-center gap-1">
                                            <PauseCircle class="w-3 h-3" /> Di-pause
                                        </span>
                                    </Badge>
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
                                                <Link :href="route('recurring-transactions.show', transaction.id)" class="flex items-center">
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
