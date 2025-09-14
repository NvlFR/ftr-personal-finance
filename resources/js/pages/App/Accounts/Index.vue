<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { MoreHorizontal, PlusCircle, Eye, Pencil, Trash2 } from 'lucide-vue-next';

// Breadcrumbs untuk navigasi
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manajemen Akun',
        href: '/accounts',
    },
];

// Tipe data berdasarkan struktur dari backend
interface Account {
    id: number;
    user_id: number;
    name: string;
    balance: string; // Dikirim sebagai string untuk presisi
    icon: string;
    color: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

// Mendefinisikan props yang diterima dari controller
const props = defineProps<{
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
</script>

<template>
<Head title="Manajemen Akun" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Manajemen Akun/Dompet</h1>
                    <p class="text-muted-foreground">Kelola semua akun dan dompet Anda di sini.</p>
                </div>
                <Button as-child>
                    <Link href="/accounts/create">
                        <PlusCircle class="w-4 h-4 mr-2" />
                        Buat Akun Baru
                    </Link>
                </Button>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Daftar Akun</CardTitle>
                    <CardDescription>Menampilkan semua akun yang telah Anda tambahkan.</CardDescription>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Ikon</TableHead>
                                <TableHead>Nama Akun</TableHead>
                                <TableHead>Saldo</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="account in props.accounts" :key="account.id">
                                <TableCell>{{ account.icon }}</TableCell>
                                <TableCell class="font-medium">
                                    <div class="flex items-center gap-2">
                                        <div :style="{ backgroundColor: account.color }" class="w-2 h-2 rounded-full"></div>
                                        <span>{{ account.name }}</span>
                                    </div>
                                </TableCell>
                                <TableCell>{{ formatCurrency(account.balance) }}</TableCell>
                                <TableCell>
                                    <Badge :variant="account.is_active ? 'default' : 'secondary'">
                                        {{ account.is_active ? 'Aktif' : 'Non-aktif' }}
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
                                                <Link :href="`/accounts/${account.id}`" class="flex items-center">
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
