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
import { Checkbox } from '@/components/ui/checkbox';
import { cn } from '@/lib/utils';
import { Calendar as CalendarIcon, ArrowLeft } from 'lucide-vue-next';
import { Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { format } from 'date-fns';
import { id as localeID } from 'date-fns/locale';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transaksi Berulang',
        href: '/recurring-transactions',
    },
    {
        title: 'Tambah Transaksi',
        href: '/recurring-transactions/create',
    },
];

interface Category {
    id: number;
    name: string;
    type: 'income' | 'expense';
}
interface Account {
    id: number;
    name: string;
}

const props = defineProps<{
    categories: Category[];
    accounts: Account[];
}>();

const form = useForm({
    type: 'expense' as 'income' | 'expense',
    amount: undefined as number | undefined,
    description: '',
    frequency: 'monthly' as 'daily' | 'weekly' | 'monthly' | 'yearly',
    interval: 1,
    start_date: new Date(),
    end_date: undefined as Date | undefined,
    is_active: true,
    category_id: undefined as number | undefined,
    account_id: undefined as number | undefined,
});

const isEndDateEnabled = ref(false);

const filteredCategories = computed(() => {
    return props.categories.filter(category => category.type === form.type);
});

const submit = () => {
    form.post(route('recurring-transactions.store'));
};
</script>

<template>
<Head title="Buat Transaksi Berulang" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6 max-w-3xl mx-auto">
            <div class="mb-6">
                <Button variant="outline" as-child>
                    <Link :href="route('recurring-transactions.index')">
                        <ArrowLeft class="w-4 h-4 mr-2" />
                        Kembali ke Daftar Transaksi Berulang
                    </Link>
                </Button>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Buat Transaksi Berulang Baru</CardTitle>
                    <CardDescription>Jadwalkan pemasukan atau pengeluaran yang terjadi secara berkala.</CardDescription>
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
                            <div v-if="form.errors.amount" class="text-red-500 text-xs mt-1">{{ form.errors.amount }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Deskripsi</Label>
                            <Textarea id="description" v-model="form.description" placeholder="Contoh: Bayar listrik bulanan" />
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="frequency">Frekuensi</Label>
                                <Select v-model="form.frequency">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Pilih frekuensi" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="daily">Harian</SelectItem>
                                        <SelectItem value="weekly">Mingguan</SelectItem>
                                        <SelectItem value="monthly">Bulanan</SelectItem>
                                        <SelectItem value="yearly">Tahunan</SelectItem>
                                    </SelectContent>
                                </Select>
                                <div v-if="form.errors.frequency" class="text-red-500 text-xs mt-1">{{ form.errors.frequency }}</div>
                            </div>
                            <div class="space-y-2">
                                <Label for="interval">Interval (setiap N {{ form.frequency }})</Label>
                                <Input id="interval" v-model="form.interval" type="number" min="1" required />
                                <div v-if="form.errors.interval" class="text-red-500 text-xs mt-1">{{ form.errors.interval }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="start_date">Tanggal Mulai</Label>
                                <Popover>
                                    <PopoverTrigger as-child>
                                        <Button
                                            variant="outline"
                                            :class="cn('w-full justify-start text-left font-normal', !form.start_date && 'text-muted-foreground')"
                                        >
                                            <CalendarIcon class="mr-2 h-4 w-4" />
                                            {{ form.start_date ? format(form.start_date, "PPP", { locale: localeID }) : "Pilih tanggal mulai" }}
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-auto p-0">
                                        <Calendar v-model="form.start_date" initial-focus />
                                    </PopoverContent>
                                </Popover>
                                <div v-if="form.errors.start_date" class="text-red-500 text-xs mt-1">{{ form.errors.start_date }}</div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <Checkbox id="enable-end-date" v-model:checked="isEndDateEnabled" />
                                    <Label for="enable-end-date">Tanggal Selesai (Opsional)</Label>
                                </div>
                                <Popover>
                                    <PopoverTrigger as-child>
                                        <Button
                                            variant="outline"
                                            :disabled="!isEndDateEnabled"
                                            :class="cn('w-full justify-start text-left font-normal', !form.end_date && 'text-muted-foreground')"
                                        >
                                            <CalendarIcon class="mr-2 h-4 w-4" />
                                            {{ form.end_date ? format(form.end_date, "PPP", { locale: localeID }) : "Pilih tanggal selesai" }}
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-auto p-0">
                                        <Calendar v-model="form.end_date" :min-date="form.start_date" initial-focus />
                                    </PopoverContent>
                                </Popover>
                                <div v-if="form.errors.end_date" class="text-red-500 text-xs mt-1">{{ form.errors.end_date }}</div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <Checkbox id="is-active" v-model:checked="form.is_active" />
                                <Label for="is-active">Aktifkan Transaksi</Label>
                            </div>
                            <p class="text-xs text-muted-foreground">Jika tidak diaktifkan, transaksi ini tidak akan dijalankan.</p>
                        </div>

                        <div class="flex justify-end gap-2 pt-4">
                            <Button variant="outline" as-child type="button">
                                <Link :href="route('recurring-transactions.index')">Batal</Link>
                            </Button>
                            <Button type="submit" :disabled="form.processing">Simpan Transaksi Berulang</Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
