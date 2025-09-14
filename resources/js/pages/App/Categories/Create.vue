<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { ArrowLeft } from 'lucide-vue-next';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kategori',
        href: '/categories',
    },
    {
        title: 'Buat Kategori',
        href: '/categories/create',
    },
];

// Menggunakan useForm dari Inertia untuk state management
const form = useForm({
    name: '',
    icon: '',
    type: 'expense', // default value
});

const submit = () => {
    // Logika untuk mengirim data ke backend
    // form.post('/categories');
    console.log('Form data submitted:', form.data());
};
</script>

<template>
    <Head title="Transaksi" />
    <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 max-w-2xl mx-auto">
        <div class="mb-6">
             <Button variant="outline" as-child>
                <Link href="/categories">
                    <ArrowLeft class="w-4 h-4 mr-2" />
                    Kembali ke Daftar Kategori
                </Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Buat Kategori Baru</CardTitle>
                <CardDescription>Tambahkan kategori baru untuk transaksi Anda.</CardDescription>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-2">
                        <Label for="name">Nama Kategori</Label>
                        <Input id="name" v-model="form.name" type="text" placeholder="Contoh: Belanja Bulanan" required />
                    </div>

                    <div class="space-y-2">
                        <Label for="icon">Ikon (Emoji)</Label>
                        <Input id="icon" v-model="form.icon" type="text" placeholder="Contoh: 🛒" required />
                         <p class="text-xs text-muted-foreground">
                           Gunakan emoji untuk representasi visual kategori Anda.
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label>Tipe Kategori</Label>
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

                    <div class="flex justify-end gap-2 pt-4">
                        <Button variant="outline" as-child>
                            <Link href="/categories">Batal</Link>
                        </Button>
                        <Button type="submit">Simpan Kategori</Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
    </AppLayout>
</template>
