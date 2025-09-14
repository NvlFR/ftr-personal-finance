<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Switch } from '@/components/ui/switch';
import { ArrowLeft, CheckCircle2, CircleOff } from 'lucide-vue-next';
import { Link, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

// Breadcrumbs untuk navigasi
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manajemen Akun',
        href: '/accounts',
    },
    {
        title: 'Buat Akun Baru',
        href: '/accounts/create',
    },
];

// Menggunakan useForm dari Inertia untuk state management
const form = useForm({
    name: '',
    balance: undefined as number | undefined,
    icon: '🏦',
    color: '#000000',
    is_active: true,
});

// Fungsi untuk submit form
const submit = () => {
    form.post(route('accounts.store'));
};
</script>

<template>
<Head title="Buat Akun Baru" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6 max-w-3xl mx-auto">
            <div class="mb-6">
                <Button variant="outline" as-child>
                    <Link :href="route('accounts.index')">
                        <ArrowLeft class="w-4 h-4 mr-2" />
                        Kembali ke Daftar Akun
                    </Link>
                </Button>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Buat Akun Baru</CardTitle>
                    <CardDescription>Tambahkan akun bank, dompet digital, atau uang tunai.</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="name">Nama Akun</Label>
                            <Input id="name" v-model="form.name" type="text" placeholder="Contoh: Rekening BCA" required />
                            <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="balance">Saldo Awal</Label>
                            <Input id="balance" v-model="form.balance" type="number" step="0.01" placeholder="Contoh: 1250000" required />
                            <div v-if="form.errors.balance" class="text-red-500 text-xs mt-1">{{ form.errors.balance }}</div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="icon">Ikon Akun</Label>
                                <Input id="icon" v-model="form.icon" type="text" placeholder="Contoh: 🏦" />
                                <div v-if="form.errors.icon" class="text-red-500 text-xs mt-1">{{ form.errors.icon }}</div>
                            </div>
                            <div class="space-y-2">
                                <Label for="color">Warna Tanda</Label>
                                <Input id="color" v-model="form.color" type="color" class="h-10 w-full" />
                                <div v-if="form.errors.color" class="text-red-500 text-xs mt-1">{{ form.errors.color }}</div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between space-x-2 rounded-md border p-4">
                            <Label for="is_active">Aktif</Label>
                            <div class="flex items-center space-x-2">
                                <div class="text-sm font-medium text-muted-foreground">{{ form.is_active ? 'Aktif' : 'Non-aktif' }}</div>
                                <Switch id="is_active" v-model:checked="form.is_active" />
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 pt-4">
                            <Button variant="outline" as-child type="button">
                                <Link :href="route('accounts.index')">Batal</Link>
                            </Button>
                            <Button type="submit" :disabled="form.processing">Simpan Akun</Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
