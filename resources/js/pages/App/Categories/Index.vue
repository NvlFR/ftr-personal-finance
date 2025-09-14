<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { MoreHorizontal, PlusCircle, Pencil, Trash2 } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kategori',
        href: '/categories',
    },
];

// Tipe data berdasarkan struktur dari backend
interface Category {
  id: number;
  user_id: number;
  name: string;
  icon: string;
  type: 'income' | 'expense';
  created_at: string;
  updated_at: string;
  transactions_count: number;
}

// Data dari backend
const props = defineProps<{
    categories: Category[];
}>();

// Hapus mock data, langsung gunakan props dari backend
// const categories = props.categories && props.categories.length > 0 ? props.categories : mockCategories;
</script>

<template>
    <Head title="Kategori" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Manajemen Kategori</h1>
                    <p class="text-muted-foreground">Kelola semua kategori pemasukan dan pengeluaran Anda.</p>
                </div>
                <Button as-child>
                    <Link href="/categories/create">
                        <PlusCircle class="w-4 h-4 mr-2" />
                        Buat Kategori Baru
                    </Link>
                </Button>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Daftar Kategori</CardTitle>
                    <CardDescription>Semua kategori yang telah Anda buat akan tampil di sini.</CardDescription>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-20">Ikon</TableHead>
                                <TableHead>Nama Kategori</TableHead>
                                <TableHead>Tipe</TableHead>
                                <TableHead>Jumlah Transaksi</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="category in props.categories" :key="category.id">
                                <TableCell class="text-2xl">{{ category.icon }}</TableCell>
                                <TableCell class="font-medium">{{ category.name }}</TableCell>
                                <TableCell>
                                    <Badge :variant="category.type === 'income' ? 'default' : 'destructive'" class="capitalize">
                                        {{ category.type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                    </Badge>
                                </TableCell>
                                <TableCell>{{ category.transactions_count }} transaksi</TableCell>
                                <TableCell class="text-right">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button variant="ghost" size="icon">
                                                <MoreHorizontal class="w-4 h-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent>
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
                             <TableRow v-if="props.categories.length === 0">
                                <TableCell colspan="5" class="text-center py-10 text-muted-foreground">
                                    Belum ada kategori yang dibuat.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
