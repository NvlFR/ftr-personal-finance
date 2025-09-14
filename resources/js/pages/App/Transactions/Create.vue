<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Calendar } from '@/components/ui/calendar';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { cn } from '@/lib/utils';
import { Calendar as CalendarIcon, ArrowLeft } from 'lucide-vue-next';
import { Link, useForm, } from '@inertiajs/vue3';
import { computed } from 'vue';
import { format } from 'date-fns';
import { id as localeID } from 'date-fns/locale';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { route } from 'ziggy-js'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transaksi',
        href: '/transactions',
    },
    {
        title: 'Tambah Transaksi',
        href: '/transactions/create',
    },
];

// Tipe data props dari backend
interface Category {
  id: number;
  name: string;
  type: 'income' | 'expense';
}
interface Account {
  id: number;
  name: string;
}

// Mendefinisikan props yang diterima dari controller
const props = defineProps<{
    categories: Category[];
    accounts: Account[];
}>();

// Menggunakan useForm dari Inertia untuk state management
const form = useForm({
    type: 'expense' as 'income' | 'expense',
    // 2. Mengganti 'null' dengan 'undefined' untuk menghindari error tipe
    amount: undefined as number | undefined,
    description: '',
    // Beri nilai awal tanggal hari ini untuk UX yang lebih baik
    transaction_date: new Date(),
    category_id: undefined as number | undefined,
    account_id: undefined as number | undefined,
});

// Filter kategori secara dinamis berdasarkan tipe transaksi yang dipilih
const filteredCategories = computed(() => {
    return props.categories.filter(category => category.type === form.type);
});

// 3. Mengaktifkan fungsi submit untuk mengirim data ke backend
const submit = () => {
    form.post(route('transactions.store'));
};
</script>

<template>
<Head title="Tambah Transaksi" />

    <AppLayout :breadcrumbs="breadcrumbs">

    <div class="p-4 sm:p-6 max-w-3xl mx-auto">
        <div class="mb-6">
             <Button variant="outline" as-child>
                <!-- 4. Menggunakan helper route() untuk URL yang dinamis -->
                <Link :href="route('transactions.index')">
                    <ArrowLeft class="w-4 h-4 mr-2" />
                    Kembali ke Daftar Transaksi
                </Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Buat Transaksi Baru</CardTitle>
                <CardDescription>Catat pemasukan atau pengeluaran baru Anda.</CardDescription>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-2">
                        <Label>Tipe Transaksi</Label>
                        <RadioGroup v-model="form.type" default-value="expense" class="flex gap-4">
                             <div class="flex items-center space-x-2">
                                <RadioGroupItem id="r-expense" value="expense" />
                                <Label for="r-expense">Pengeluaran</Label>
                            </div>
                            <div class="flex items-center space-x-2">
                                <RadioGroupItem id="r-income" value="income" />
                                <Label for="r-income">Pemasukan</Label>
                            </div>
                        </RadioGroup>
                    </div>

                    <div class="space-y-2">
                        <Label for="amount">Jumlah</Label>
                        <Input id="amount" v-model="form.amount" type="number" placeholder="Contoh: 50000" required />
                        <!-- 5. Menambahkan tampilan untuk error validasi -->
                        <div v-if="form.errors.amount" class="text-red-500 text-xs mt-1">{{ form.errors.amount }}</div>
                    </div>

                    <div class="space-y-2">
                        <Label for="description">Deskripsi</Label>
                        <Textarea id="description" v-model="form.description" placeholder="Contoh: Makan siang" />
                        <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <Label for="category">Kategori</Label>
                             <Select v-model="form.category_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Pilih kategori" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="category in filteredCategories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <div v-if="form.errors.category_id" class="text-red-500 text-xs mt-1">{{ form.errors.category_id }}</div>
                        </div>
                        <div class="space-y-2">
                            <Label for="account">Akun/Dompet</Label>
                             <Select v-model="form.account_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Pilih akun" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="account in props.accounts" :key="account.id" :value="account.id">
                                        {{ account.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <div v-if="form.errors.account_id" class="text-red-500 text-xs mt-1">{{ form.errors.account_id }}</div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="transaction_date">Tanggal Transaksi</Label>
                        <Popover>
                            <PopoverTrigger as-child>
                                <Button
                                    variant="outline"
                                    :class="cn('w-full justify-start text-left font-normal', !form.transaction_date && 'text-muted-foreground')"
                                >
                                    <CalendarIcon class="mr-2 h-4 w-4" />
                                    {{ form.transaction_date ? format(form.transaction_date, "PPP", { locale: localeID }) : "Pilih tanggal" }}
                                </Button>
                            </PopoverTrigger>
                            <PopoverContent class="w-auto p-0">
                                <Calendar v-model="form.transaction_date" initial-focus />
                            </PopoverContent>
                        </Popover>
                        <div v-if="form.errors.transaction_date" class="text-red-500 text-xs mt-1">{{ form.errors.transaction_date }}</div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4">
                        <Button variant="outline" as-child type="button">
                            <Link :href="route('transactions.index')">Batal</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">Simpan Transaksi</Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
    </AppLayout>
</template>

