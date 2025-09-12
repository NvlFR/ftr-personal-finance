<script setup lang="ts">
import { ref } from 'vue'
import { Calendar as CalendarIcon } from 'lucide-vue-next'
import { Calendar } from '@/components/ui/calendar'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { cn } from '@/lib/utils'
import { format } from 'date-fns'
import { id } from 'date-fns/locale'

// State untuk form
const transactionType = ref<'Pemasukan' | 'Pengeluaran'>()
const transactionDate = ref<Date>()
const description = ref('')
const category = ref('')
const account = ref('')
const amount = ref<number | null>(null)

// Data tiruan untuk dropdown
const categories = ref([
  { value: 'gaji', label: 'Gaji' },
  { value: 'makanan', label: 'Makanan & Minuman' },
  { value: 'transportasi', label: 'Transportasi' },
  { value: 'tagihan', label: 'Tagihan' },
  { value: 'hiburan', label: 'Hiburan' },
  { value: 'belanja', label: 'Belanja' },
])

const accounts = ref([
  { value: 'bca', label: 'BCA' },
  { value: 'mandiri', label: 'Mandiri' },
  { value: 'gopay', label: 'Gopay' },
  { value: 'ovo', label: 'OVO' },
  { value: 'jenius', label: 'Jenius' },
])

// Handler untuk submit form
const handleSubmit = () => {
  if (!transactionType.value || !transactionDate.value || !description.value || !category.value || !account.value || !amount.value) {
    alert('Harap isi semua field yang wajib diisi.');
    return;
  }

  const newTransaction = {
    type: transactionType.value,
    date: format(transactionDate.value, 'yyyy-MM-dd'),
    description: description.value,
    category: category.value,
    account: account.value,
    amount: amount.value
  }

  console.log('Data Transaksi Baru:', newTransaction)
  alert('Transaksi berhasil disimpan! (Cek console untuk melihat data)')
  // Di aplikasi nyata, di sini kita akan mengirim data ke backend Laravel
}
</script>

<template>
  <div class="p-4 sm:p-6 lg:p-8 flex justify-center">
    <Card class="w-full max-w-2xl">
      <CardHeader>
        <CardTitle class="text-2xl">Buat Transaksi Baru</CardTitle>
        <CardDescription>
          Catat pemasukan atau pengeluaran baru Anda di sini.
        </CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="handleSubmit" class="grid gap-6">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <Label for="type">Tipe Transaksi</Label>
               <Select v-model="transactionType">
                <SelectTrigger id="type">
                  <SelectValue placeholder="Pilih tipe" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="Pemasukan">Pemasukan</SelectItem>
                  <SelectItem value="Pengeluaran">Pengeluaran</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div>
              <Label for="date">Tanggal</Label>
              <Popover>
                <PopoverTrigger as-child>
                  <Button
                    id="date"
                    :variant="'outline'"
                    :class="cn(
                      'w-full justify-start text-left font-normal',
                      !transactionDate && 'text-muted-foreground',
                    )"
                  >
                    <CalendarIcon class="mr-2 h-4 w-4" />
                    <span>{{ transactionDate ? format(transactionDate, 'PPP', { locale: id }) : "Pilih tanggal" }}</span>
                  </Button>
                </PopoverTrigger>
                <PopoverContent class="w-auto p-0">
                  <Calendar v-model="transactionDate" />
                </PopoverContent>
              </Popover>
            </div>
          </div>

          <div>
            <Label for="description">Deskripsi</Label>
            <Input id="description" v-model="description" placeholder="Contoh: Beli Makan Siang" />
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <Label for="category">Kategori</Label>
              <Select v-model="category">
                <SelectTrigger id="category">
                  <SelectValue placeholder="Pilih kategori" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem v-for="cat in categories" :key="cat.value" :value="cat.label">
                    {{ cat.label }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div>
              <Label for="account">Akun/Dompet</Label>
              <Select v-model="account">
                <SelectTrigger id="account">
                  <SelectValue placeholder="Pilih akun" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem v-for="acc in accounts" :key="acc.value" :value="acc.label">
                    {{ acc.label }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>

          <div>
            <Label for="amount">Jumlah (Rp)</Label>
            <Input id="amount" v-model.number="amount" type="number" placeholder="Contoh: 50000" />
          </div>
        </form>
      </CardContent>
      <CardFooter class="flex justify-end gap-2">
        <!-- Tombol Batal akan mengarah kembali ke halaman Index.vue -->
        <Button variant="outline">Batal</Button>
        <Button @click="handleSubmit">Simpan Transaksi</Button>
      </CardFooter>
    </Card>
  </div>
</template>
