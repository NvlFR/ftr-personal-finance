<script setup lang="ts">
import { ref, computed } from 'vue'
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
  CardFooter
} from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { ArrowLeft, Pencil, Tag, Wallet, Calendar, FileText } from 'lucide-vue-next'

// Tipe data untuk transaksi
interface Transaction {
  id: string
  date: string
  description: string
  category: string
  account: string
  type: 'Pemasukan' | 'Pengeluaran'
  amount: number
}

// Data tiruan untuk satu transaksi
// Di aplikasi nyata, data ini akan didapat dari API berdasarkan ID di URL
const transaction = ref<Transaction>({
  id: 'TRX003',
  date: '2024-05-11',
  description: 'Langganan Netflix Premium per bulan',
  category: 'Hiburan',
  account: 'Jenius',
  type: 'Pengeluaran',
  amount: 186000
})

// Computed property untuk format
const formattedAmount = computed(() => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
  }).format(transaction.value.amount)
})

const formattedDate = computed(() => {
    return new Date(transaction.value.date).toLocaleDateString('id-ID', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    })
})
</script>

<template>
  <div class="p-4 sm:p-6 lg:p-8 flex justify-center">
    <Card class="w-full max-w-3xl">
      <CardHeader>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <div>
            <CardTitle class="text-2xl">Detail Transaksi</CardTitle>
            <CardDescription>
              Detail lengkap untuk transaksi #{{ transaction.id }}
            </CardDescription>
          </div>
          <div class="flex gap-2">
             <!-- Tombol Kembali akan mengarah ke halaman Index.vue -->
            <Button variant="outline">
              <ArrowLeft class="w-4 h-4 mr-2" />
              Kembali
            </Button>
            <!-- Tombol Edit akan mengarah ke halaman Edit.vue -->
            <Button>
              <Pencil class="w-4 h-4 mr-2" />
              Edit
            </Button>
          </div>
        </div>
      </CardHeader>
      <CardContent class="grid gap-6 text-base">
        <div class="flex items-center justify-between">
          <span class="text-muted-foreground">Jumlah</span>
          <span 
            :class="[
              'font-bold text-2xl',
              transaction.type === 'Pemasukan' ? 'text-green-600' : 'text-red-600'
            ]"
          >
            {{ transaction.type === 'Pengeluaran' ? '-' : '+' }} {{ formattedAmount }}
          </span>
        </div>
        
        <div class="border-t pt-6 grid sm:grid-cols-2 gap-x-8 gap-y-6">
          <div class="flex items-start gap-3">
            <FileText class="w-5 h-5 mt-1 text-muted-foreground" />
            <div>
              <p class="text-sm text-muted-foreground">Deskripsi</p>
              <p class="font-medium">{{ transaction.description }}</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <Calendar class="w-5 h-5 mt-1 text-muted-foreground" />
            <div>
              <p class="text-sm text-muted-foreground">Tanggal</p>
              <p class="font-medium">{{ formattedDate }}</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <Tag class="w-5 h-5 mt-1 text-muted-foreground" />
            <div>
              <p class="text-sm text-muted-foreground">Kategori</p>
              <p class="font-medium">{{ transaction.category }}</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <Wallet class="w-5 h-5 mt-1 text-muted-foreground" />
            <div>
              <p class="text-sm text-muted-foreground">Akun/Dompet</p>
              <p class="font-medium">{{ transaction.account }}</p>
            </div>
          </div>
        </div>
      </CardContent>
       <CardFooter class="border-t pt-4">
          <Badge :variant="transaction.type === 'Pemasukan' ? 'default' : 'destructive'">
            {{ transaction.type }}
          </Badge>
        </CardFooter>
    </Card>
  </div>
</template>
