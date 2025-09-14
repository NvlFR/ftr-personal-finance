<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, ArrowUpRight, ArrowDownLeft, Pencil } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { id as localeID } from 'date-fns/locale';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Utang & Piutang',
        href: '/debts',
    },
    {
        title: 'Detail',
        href: '#', // Ganti dengan ID dinamis jika perlu
    },
];

// Tipe data sama seperti di halaman Index
interface Party {
    id: number;
    name: string;
}

interface Debt {
    id: number;
    type: 'debt' | 'receivable';
    description: string;
    amount: string;
    paid_amount: string;
    due_date: string;
    status: 'unpaid' | 'partially_paid' | 'paid';
    remaining_amount: string;
    party: Party;
}

const props = defineProps<{
    debt: Debt;
}>();

// Fungsi untuk format mata uang
const formatCurrency = (value: string) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(parseFloat(value));
};

const getStatusText = (status: string) => {
    switch (status) {
        case 'unpaid':
            return 'Belum Lunas';
        case 'partially_paid':
            return 'Lunas Sebagian';
        case 'paid':
            return 'Lunas';
        default:
            return status;
    }
};

const getStatusVariant = (status: string) => {
    switch (status) {
        case 'unpaid':
            return 'destructive';
        case 'partially_paid':
            return 'secondary';
        case 'paid':
            return 'default';
        default:
            return 'default';
    }
};
</script>

<template>
    <Head title="Detail Utang & Piutang" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 max-w-2xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <Button variant="outline" as-child>
                    <Link :href="route('debts.index')">
                        <ArrowLeft class="w-4 h-4 mr-2" />
                        Kembali ke Daftar Utang & Piutang
                    </Link>
                </Button>
                <Button>
                    <Pencil class="w-4 h-4 mr-2" />
                    Edit Catatan
                </Button>
            </div>
            <Card>
                <CardHeader>
                    <CardTitle>Detail Utang & Piutang</CardTitle>
                    <CardDescription>Rincian lengkap dari catatan yang Anda pilih.</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Tipe</span>
                            <Badge :variant="debt.type === 'receivable' ? 'default' : 'destructive'" class="capitalize flex items-center gap-1">
                                <ArrowUpRight v-if="debt.type === 'receivable'" class="w-3 h-3" />
                                <ArrowDownLeft v-else class="w-3 h-3" />
                                {{ debt.type === 'receivable' ? 'Piutang' : 'Utang' }}
                            </Badge>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Jumlah</span>
                            <span class="font-bold text-xl">{{ formatCurrency(debt.amount) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Sisa yang Belum Dibayar</span>
                            <span class="font-bold text-xl" :class="debt.remaining_amount === '0.00' ? 'text-green-600' : 'text-red-600'">
                                {{ formatCurrency(debt.remaining_amount) }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Deskripsi</span>
                            <span class="font-medium text-right">{{ debt.description }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Pihak</span>
                            <span class="font-medium">{{ debt.party.name }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Tanggal Jatuh Tempo</span>
                            <span class="font-medium">{{ format(new Date(debt.due_date), "PPP", { locale: localeID }) }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Status</span>
                            <Badge :variant="getStatusVariant(debt.status)" class="capitalize">
                                {{ getStatusText(debt.status) }}
                            </Badge>
                        </div>

                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
