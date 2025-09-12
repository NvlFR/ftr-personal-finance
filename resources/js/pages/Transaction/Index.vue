<script setup lang="ts">
import { ref } from 'vue'
import { MoreHorizontal, PlusCircle } from 'lucide-vue-next'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'

// Definisikan tipe data untuk transaksi
interface Transaction {
  id: string
  date: string
  description: string
  category: string
  account: string
  type: 'Pemasukan' | 'Pengeluaran'
  amount: number
}

// Data tiruan (mock data) untuk transaksi
const transactions = ref<Transaction[]>([
  { id: 'TRX001', date: '2024-05-10', description: 'Gaji Bulanan', category: 'Gaji', account: 'BCA', type: 'Pemasukan', amount: 10000000 },
  { id: 'TRX002', date: '2024-05-10', description: 'Beli Kopi Susu', category: 'Makanan & Minuman', account: 'Gopay', type: 'Pengeluaran', amount: 25000 },
  { id: 'TRX003', date: '2024-05-11', description: 'Langganan Netflix', category: 'Hiburan', account: 'Jenius', type: 'Pengeluaran', amount: 186000 },
  { id: 'TRX004', date: '2024-05-12', description: 'Bonus Proyek', category: 'Bonus', account: 'BCA', type: 'Pemasukan', amount: 2500000 },
  { id: 'TRX005', date: '2024-05-13', description: 'Bayar Listrik', category: 'Tagihan', account: 'BCA', type: 'Pengeluaran', amount: 450000 },
])

// Fungsi untuk memformat mata uang
const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(value)
}

// Fungsi placeholder untuk aksi
const viewTransaction = (id: string) => alert(`Melihat transaksi ${id}`)
const editTransaction = (id: string) => alert(`Mengedit transaksi ${id}`)
const deleteTransaction = (id: string) => alert(`Menghapus transaksi ${id}`)
</script>

<template>
  <div class="p-4 sm:p-6 lg:p-8">
    <Card>
      <CardHeader>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <div>
            <CardTitle class="text-2xl">Daftar Transaksi</CardTitle>
            <CardDescription>
              Lihat dan kelola semua pemasukan dan pengeluaran Anda.
            </CardDescription>
          </div>
          <!-- Tombol ini akan mengarah ke halaman Create.vue -->
          <Button>
            <PlusCircle class="w-4 h-4 mr-2" />
            Tambah Transaksi
          </Button>
        </div>
      </CardHeader>
      <CardContent>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Tanggal</TableHead>
              <TableHead>Deskripsi</TableHead>
              <TableHead>Kategori</TableHead>
              <TableHead>Akun</TableHead>
              <TableHead>Tipe</TableHead>
              <TableHead class="text-right">Jumlah</TableHead>
              <TableHead class="text-center">Aksi</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-if="transactions.length === 0">
              <TableCell :colspan="7" class="text-center h-24">
                Belum ada transaksi.
              </TableCell>
            </TableRow>
            <TableRow v-for="transaction in transactions" :key="transaction.id">
              <TableCell>{{ transaction.date }}</TableCell>
              <TableCell class="font-medium">{{ transaction.description }}</TableCell>
              <TableCell>{{ transaction.category }}</TableCell>
              <TableCell>{{ transaction.account }}</TableCell>
              <TableCell>
                <Badge :variant="transaction.type === 'Pemasukan' ? 'default' : 'destructive'">
                  {{ transaction.type }}
                </Badge>
              </TableCell>
              <TableCell 
                :class="[
                  'text-right font-semibold',
                  transaction.type === 'Pemasukan' ? 'text-green-600' : 'text-red-600'
                ]"
              >
                {{ formatCurrency(transaction.amount) }}
              </TableCell>
              <TableCell class="text-center">
                <DropdownMenu>
                  <DropdownMenuTrigger as-child>
                    <Button variant="ghost" class="h-8 w-8 p-0">
                      <span class="sr-only">Buka menu</span>
                      <MoreHorizontal class="h-4 w-4" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end">
                    <DropdownMenuLabel>Aksi</DropdownMenuLabel>
                    <DropdownMenuItem @click="viewTransaction(transaction.id)">Lihat Detail</DropdownMenuItem>
                    <DropdownMenuItem @click="editTransaction(transaction.id)">Edit</DropdownMenuItem>
                    <DropdownMenuItem @click="deleteTransaction(transaction.id)" class="text-red-600">Hapus</DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </CardContent>
    </Card>
  </div>
</template>
