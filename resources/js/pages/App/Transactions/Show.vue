<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, ArrowUpRight, ArrowDownLeft, Pencil } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

// Tipe data sama seperti di halaman Index
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

// Asumsikan data transaksi ini dikirim sebagai props dari controller
const props = defineProps<{
    transaction: Transaction;
}>();

// Data tiruan jika props tidak tersedia
const mockTransaction: Transaction = {
    id: 1,
    type: "expense",
    amount: "50000.00",
    description: "Makan siang di warteg",
    transaction_date: "2025-09-12",
    category: { id: 3, name: "Makanan & Minuman", icon: "🍔" },
    account: { id: 1, name: "Dompet Tunai", current_balance: "150000.00" }
};

const transaction = props.transaction || mockTransaction;

// Fungsi untuk format mata uang
const formatCurrency = (value: string) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
  }).format(parseFloat(value));
};
</script>

<template>
    <div class="p-6 max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <Button variant="outline" as-child>
                <Link href="/transactions">
                    <ArrowLeft class="w-4 h-4 mr-2" />
                    Kembali ke Daftar Transaksi
                </Link>
            </Button>
            <Button>
                <Pencil class="w-4 h-4 mr-2" />
                Edit Transaksi
            </Button>
        </div>
        <Card>
            <CardHeader>
                <CardTitle>Detail Transaksi</CardTitle>
                <CardDescription>Rincian lengkap dari transaksi yang Anda pilih.</CardDescription>
            </CardHeader>
            <CardContent>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 border rounded-lg">
                        <span class="text-muted-foreground">Tipe</span>
                        <Badge :variant="transaction.type === 'income' ? 'default' : 'destructive'" class="capitalize flex items-center gap-1">
                            <ArrowUpRight v-if="transaction.type === 'income'" class="w-3 h-3" />
                            <ArrowDownLeft v-else class="w-3 h-3" />
                            {{ transaction.type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                        </Badge>
                    </div>

                     <div class="flex justify-between items-center p-4 border rounded-lg">
                        <span class="text-muted-foreground">Jumlah</span>
                        <span class="font-bold text-xl" :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'">
                           {{ formatCurrency(transaction.amount) }}
                        </span>
                    </div>

                     <div class="flex justify-between items-center p-4 border rounded-lg">
                        <span class="text-muted-foreground">Deskripsi</span>
                        <span class="font-medium text-right">{{ transaction.description }}</span>
                    </div>

                     <div class="flex justify-between items-center p-4 border rounded-lg">
                        <span class="text-muted-foreground">Tanggal</span>
                        <span class="font-medium">{{ new Date(transaction.transaction_date).toLocaleDateString('id-ID', { dateStyle: 'long' }) }}</span>
                    </div>

                     <div class="flex justify-between items-center p-4 border rounded-lg">
                        <span class="text-muted-foreground">Kategori</span>
                        <span class="font-medium">{{ transaction.category.icon }} {{ transaction.category.name }}</span>
                    </div>

                     <div class="flex justify-between items-center p-4 border rounded-lg">
                        <span class="text-muted-foreground">Sumber Akun</span>
                        <span class="font-medium">{{ transaction.account.name }}</span>
                    </div>

                </div>
            </CardContent>
        </Card>
    </div>
</template>
