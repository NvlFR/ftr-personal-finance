<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Progress } from '@/components/ui/progress';
import {
    PlusCircle,
    MoreHorizontal,
    Eye,
    Pencil,
    Trash2,
} from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Sasaran Keuangan',
        href: 'financial-goals',
    },
];

// Tipe data berdasarkan struktur dari backend
interface Goal {
    id: number;
    name: string;
    description: string | null;
    target_amount: string;
    current_amount: string;
    target_date: string;
    status: 'in_progress' | 'completed';
    percentage: number;
    remaining_amount: string;
}

interface Account {
    id: number;
    name: string;
    balance: string;
}

// Mendefinisikan props yang diterima dari controller
const props = defineProps<{
    goals: Goal[];
    accounts: Account[];
}>();

// Fungsi untuk format mata uang
const formatCurrency = (value: string) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(parseFloat(value));
};

const getStatusBadgeVariant = (status: string) => {
    switch (status) {
        case 'in_progress':
            return 'default';
        case 'completed':
            return 'success';
        default:
            return 'secondary';
    }
};

const getStatusText = (status: string) => {
    switch (status) {
        case 'in_progress':
            return 'Dalam Proses';
        case 'completed':
            return 'Selesai';
        default:
            return 'Tidak Dikenal';
    }
};

</script>

<template>
    <Head title="Sasaran Keuangan" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Daftar Sasaran Keuangan</h1>
                    <p class="text-muted-foreground">Rencanakan dan pantau kemajuan sasaran finansial Anda.</p>
                </div>
                <Button as-child>
                    <Link :href="route('financial-goals.create')">
                        <PlusCircle class="w-4 h-4 mr-2" />
                        Buat Sasaran Baru
                    </Link>
                </Button>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Daftar Sasaran</CardTitle>
                    <CardDescription>Menampilkan semua sasaran keuangan yang telah Anda buat.</CardDescription>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nama</TableHead>
                                <TableHead>Target</TableHead>
                                <TableHead>Terkumpul</TableHead>
                                <TableHead>Persentase</TableHead>
                                <TableHead>Tanggal Target</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="goal in goals" :key="goal.id">
                                <TableCell class="font-medium">{{ goal.name }}</TableCell>
                                <TableCell>{{ formatCurrency(goal.target_amount) }}</TableCell>
                                <TableCell>{{ formatCurrency(goal.current_amount) }}</TableCell>
                                <TableCell>
                                    <div class="flex items-center gap-2">
                                        <Progress :model-value="goal.percentage" class="w-[100px]" />
                                        <span>{{ goal.percentage?.toFixed(0) }}%</span>
                                    </div>
                                </TableCell>
                                <TableCell>{{ goal.target_date }}</TableCell>
                                <TableCell>
                                    <Badge :variant="getStatusBadgeVariant(goal.status)" class="capitalize">
                                        {{ getStatusText(goal.status) }}
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
                                                <Link :href="route('financial-goals.show', goal.id)" class="flex items-center">
                                                    <Eye class="w-4 h-4 mr-2" />Lihat
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem as-child>
                                                <Link :href="route('financial-goals.edit', goal.id)" class="flex items-center">
                                                    <Pencil class="w-4 h-4 mr-2" />Edit
                                                </Link>
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
