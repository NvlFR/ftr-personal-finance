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
import { ref } from 'vue'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transaksi',
        href: 'transactions',
    },
];

// Tipe data berdasarkan struktur dari backend
interface Category {
  id: number;
  name: string;
  icon: string;
}

interface Account {
  id: number;
  name: string;
  current_balance: string;
}

interface Transaction {
  id: number;
  type: 'income' | 'expense';
  amount: string;
  description: string;
  transaction_date: string;
  category: Category;
  account: Account;
}

// Data tiruan (mock data) untuk halaman index
const transactions = ref<Transaction[]>([
  {
    id: 1,
    type: "expense",
    amount: "50000.00",
    description: "Makan siang di warteg",
    transaction_date: "2025-09-12",
    category: { id: 3, name: "Makanan & Minuman", icon: "🍔" },
    account: { id: 1, name: "Dompet Tunai", current_balance: "150000.00" }
  },
  {
    id: 2,
    type: "income",
    amount: "2500000.00",
    description: "Gaji bulanan",
    transaction_date: "2025-09-01",
    category: { id: 1, name: "Gaji", icon: "💰" },
    account: { id: 2, name: "Rekening Bank", current_balance: "10000000.00" }
  },
  {
    id: 3,
    type: "expense",
    amount: "75000.00",
    description: "Beli bensin motor",
    transaction_date: "2025-09-10",
    category: { id: 2, name: "Transportasi", icon: "🚗" },
    account: { id: 1, name: "Dompet Tunai", current_balance: "150000.00" }
  },
]);

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
<Head title="Transaksi" />
    <AppLayout :breadcrumbs="breadcrumbs">
            <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Daftar Transaksi</h1>
                <p class="text-muted-foreground">Lihat dan kelola semua pemasukan dan pengeluaran Anda.</p>
            </div>
            <Button as-child>
                <Link href="/transactions/create">
                    <PlusCircle class="w-4 h-4 mr-2" />
                    Buat Transaksi Baru
                </Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Riwayat Transaksi</CardTitle>
                <CardDescription>Menampilkan semua transaksi yang pernah tercatat.</CardDescription>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Tipe</TableHead>
                            <TableHead>Deskripsi</TableHead>
                            <TableHead>Jumlah</TableHead>
                            <TableHead>Kategori</TableHead>
                            <TableHead>Akun</TableHead>
                            <TableHead>Tanggal</TableHead>
                            <TableHead class="text-right">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="transaction in transactions" :key="transaction.id">
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
                            <TableCell>{{ transaction.category.icon }} {{ transaction.category.name }}</TableCell>
                            <TableCell>{{ transaction.account.name }}</TableCell>
                            <TableCell>{{ transaction.transaction_date }}</TableCell>
                            <TableCell class="text-right">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" size="icon">
                                            <MoreHorizontal class="w-4 h-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent>
                                        <DropdownMenuItem as-child>
                                            <Link :href="`/transactions/${transaction.id}`" class="flex items-center">
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
