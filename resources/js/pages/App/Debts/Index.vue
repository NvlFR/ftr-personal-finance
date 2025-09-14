<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { ArrowUpRight, ArrowDownLeft, MoreHorizontal, PlusCircle, Eye, Pencil, Trash2 } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Utang & Piutang',
        href: 'debts',
    },
];

// Tipe data berdasarkan struktur dari backend
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
    debts: Debt[];
}>();

// Fungsi untuk format mata uang
const formatCurrency = (value: string) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(parseFloat(value));
};

// Fungsi untuk mengonversi status ke teks yang bisa dibaca
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
    <Head title="Utang & Piutang" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Daftar Utang & Piutang</h1>
                    <p class="text-muted-foreground">Lihat dan kelola semua catatan utang dan piutang Anda.</p>
                </div>
                <Button as-child>
                    <Link href="/debts/create">
                        <PlusCircle class="w-4 h-4 mr-2" />
                        Catat Utang/Piutang Baru
                    </Link>
                </Button>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Riwayat Utang & Piutang</CardTitle>
                    <CardDescription>Menampilkan semua utang dan piutang yang pernah tercatat.</CardDescription>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Tipe</TableHead>
                                <TableHead>Deskripsi</TableHead>
                                <TableHead>Pihak</TableHead>
                                <TableHead>Jatuh Tempo</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="text-right">Jumlah</TableHead>
                                <TableHead class="text-right">Sisa</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="debt in props.debts" :key="debt.id">
                                <TableCell>
                                    <Badge :variant="debt.type === 'receivable' ? 'default' : 'destructive'" class="capitalize flex items-center gap-1 w-fit">
                                        <ArrowUpRight v-if="debt.type === 'receivable'" class="w-3 h-3" />
                                        <ArrowDownLeft v-else class="w-3 h-3" />
                                        {{ debt.type === 'receivable' ? 'Piutang' : 'Utang' }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="font-medium">{{ debt.description }}</TableCell>
                                <TableCell>{{ debt.party.name }}</TableCell>
                                <TableCell>{{ debt.due_date }}</TableCell>
                                <TableCell>
                                     <Badge :variant="getStatusVariant(debt.status)" class="capitalize">
                                        {{ getStatusText(debt.status) }}
                                     </Badge>
                                </TableCell>
                                <TableCell class="text-right">{{ formatCurrency(debt.amount) }}</TableCell>
                                <TableCell class="text-right font-semibold">{{ formatCurrency(debt.remaining_amount) }}</TableCell>
                                <TableCell class="text-right">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button variant="ghost" size="icon">
                                                <MoreHorizontal class="w-4 h-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent>
                                            <DropdownMenuItem as-child>
                                                <Link :href="`/debts/${debt.id}`" class="flex items-center">
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
