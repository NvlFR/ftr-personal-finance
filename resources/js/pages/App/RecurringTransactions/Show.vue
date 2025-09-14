<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, ArrowUpRight, ArrowDownLeft, Pencil, PauseCircle, PlayCircle } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transaksi Berulang',
        href: '/recurring-transactions',
    },
    {
        title: 'Detail Transaksi',
        href: `/recurring-transactions/${props.recurringTransaction.id}`,
    },
];

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

const props = defineProps<{
    recurringTransaction: RecurringTransaction;
}>();

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
        'daily': 'Harian',
        'weekly': 'Mingguan',
        'monthly': 'Bulanan',
        'yearly': 'Tahunan',
    };
    const frequencyText = intervals[frequency as keyof typeof intervals];
    return `${interval === 1 ? frequencyText : 'Setiap ' + interval + ' ' + frequencyText}`;
};

const formatDate = (date: string | null) => {
    if (!date) return 'Tidak ada';
    return new Date(date).toLocaleDateString('id-ID', { dateStyle: 'long' });
};
</script>

<template>
    <Head title="Detail Transaksi Berulang" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 max-w-2xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <Button variant="outline" as-child>
                    <Link :href="route('recurring-transactions.index')">
                        <ArrowLeft class="w-4 h-4 mr-2" />
                        Kembali ke Daftar Transaksi Berulang
                    </Link>
                </Button>
                <Button as-child>
                    <Link :href="route('recurring-transactions.edit', recurringTransaction.id)">
                        <Pencil class="w-4 h-4 mr-2" />
                        Edit Transaksi
                    </Link>
                </Button>
            </div>
            <Card>
                <CardHeader>
                    <CardTitle>Detail Transaksi Berulang</CardTitle>
                    <CardDescription>Rincian lengkap dari transaksi berulang yang Anda pilih.</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Tipe</span>
                            <Badge :variant="recurringTransaction.type === 'income' ? 'default' : 'destructive'" class="capitalize flex items-center gap-1">
                                <ArrowUpRight v-if="recurringTransaction.type === 'income'" class="w-3 h-3" />
                                <ArrowDownLeft v-else class="w-3 h-3" />
                                {{ recurringTransaction.type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                            </Badge>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Jumlah</span>
                            <span class="font-bold text-xl" :class="recurringTransaction.type === 'income' ? 'text-green-600' : 'text-red-600'">
                                {{ formatCurrency(recurringTransaction.amount) }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Deskripsi</span>
                            <span class="font-medium text-right">{{ recurringTransaction.description }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Frekuensi</span>
                            <span class="font-medium">{{ formatFrequency(recurringTransaction.frequency, recurringTransaction.interval) }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Tanggal Mulai</span>
                            <span class="font-medium">{{ formatDate(recurringTransaction.start_date) }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Tanggal Selesai</span>
                            <span class="font-medium">{{ formatDate(recurringTransaction.end_date) }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Jadwal Berikutnya</span>
                            <span class="font-medium">{{ formatDate(recurringTransaction.next_due_date) }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Status</span>
                            <Badge :variant="recurringTransaction.is_active ? 'default' : 'secondary'">
                                <span v-if="recurringTransaction.is_active" class="flex items-center gap-1">
                                    <PlayCircle class="w-3 h-3" /> Aktif
                                </span>
                                <span v-else class="flex items-center gap-1">
                                    <PauseCircle class="w-3 h-3" /> Di-pause
                                </span>
                            </Badge>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Kategori</span>
                            <span class="font-medium">{{ recurringTransaction.category.name }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Sumber Akun</span>
                            <span class="font-medium">{{ recurringTransaction.account.name }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
