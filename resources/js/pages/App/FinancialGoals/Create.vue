<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Calendar } from '@/components/ui/calendar';
import { cn } from '@/lib/utils';
import { Calendar as CalendarIcon, ArrowLeft } from 'lucide-vue-next';
import { Link, useForm, Head } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { id as localeID } from 'date-fns/locale';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Sasaran Keuangan',
        // Ganti dengan route() helper
        href: route('financial-goals.index'),
    },
    {
        title: 'Buat Sasaran',
        // Ganti dengan route() helper
        href: route('financial-goals.create'),
    },
];

interface Account {
    id: number;
    name: string;
}

const props = defineProps<{
    accounts: Account[];
}>();

const form = useForm({
    name: '',
    description: '',
    target_amount: undefined as number | undefined,
    // Tetap gunakan Date untuk binding, dan format saat submit
    target_date: undefined as Date | undefined,
    initial_deposit: undefined as number | undefined,
    source_account_id: undefined as number | undefined,
});

const submit = () => {
    // Gunakan cara ini, Inertia akan mengirimkan form yang sudah dimodifikasi
    if (form.target_date) {
        form.target_date = format(form.target_date, 'yyyy-MM-dd');
    }
    form.post(route('financial-goals.store'));
};
</script>

<template>
    <Head title="Buat Sasaran Keuangan" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6 max-w-3xl mx-auto">
            <div class="mb-6">
                <Button variant="outline" as-child>
                    <Link :href="route('financial-goals.index')">
                        <ArrowLeft class="w-4 h-4 mr-2" />
                        Kembali ke Daftar Sasaran
                    </Link>
                </Button>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Buat Sasaran Keuangan Baru</CardTitle>
                    <CardDescription>Rencanakan sasaran finansial Anda, seperti dana darurat atau liburan impian.</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="name">Nama Sasaran</Label>
                            <Input id="name" v-model="form.name" type="text" placeholder="Contoh: Dana Darurat" required />
                            <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Deskripsi</Label>
                            <Textarea id="description" v-model="form.description" placeholder="Contoh: Untuk kebutuhan tak terduga." />
                            <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="target_amount">Jumlah Target</Label>
                                <Input id="target_amount" v-model="form.target_amount" type="number" placeholder="Contoh: 10000000" required />
                                <div v-if="form.errors.target_amount" class="text-red-500 text-xs mt-1">{{ form.errors.target_amount }}</div>
                            </div>

                            <div class="space-y-2">
                                <Label for="target_date">Tanggal Target</Label>
                                <Popover>
                                    <PopoverTrigger as-child>
                                        <Button
                                            variant="outline"
                                            :class="cn('w-full justify-start text-left font-normal', !form.target_date && 'text-muted-foreground')"
                                        >
                                            <CalendarIcon class="mr-2 h-4 w-4" />
                                            {{ form.target_date ? format(form.target_date, "PPP", { locale: localeID }) : "Pilih tanggal target" }}
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-auto p-0">
                                        <Calendar v-model="form.target_date" initial-focus />
                                    </PopoverContent>
                                </Popover>
                                <div v-if="form.errors.target_date" class="text-red-500 text-xs mt-1">{{ form.errors.target_date }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="initial_deposit">Setoran Awal (Opsional)</Label>
                                <Input id="initial_deposit" v-model="form.initial_deposit" type="number" placeholder="Contoh: 2500000" />
                                <div v-if="form.errors.initial_deposit" class="text-red-500 text-xs mt-1">{{ form.errors.initial_deposit }}</div>
                            </div>

                            <div class="space-y-2">
                                <Label for="source_account">Sumber Akun</Label>
                                <Select v-model="form.source_account_id">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Pilih akun" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="account in props.accounts" :key="account.id" :value="account.id">
                                            {{ account.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <div v-if="form.errors.source_account_id" class="text-red-500 text-xs mt-1">{{ form.errors.source_account_id }}</div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 pt-4">
                            <Button variant="outline" as-child type="button">
                                <Link :href="route('financial-goals.index')">Batal</Link>
                            </Button>
                            <Button type="submit" :disabled="form.processing">Simpan Sasaran</Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
