<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, Pencil } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// Tipe data sama seperti di halaman Index
interface Account {
    id: number;
    user_id: number;
    name: string;
    balance: string; // Dikirim sebagai string untuk presisi
    icon: string;
    color: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

// Mendefinisikan props yang diterima dari controller
const props = defineProps<{
    account: Account;
}>();

// Breadcrumbs dinamis berdasarkan nama akun
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Manajemen Akun',
        href: '/accounts',
    },
    {
        title: props.account.name,
        href: `/accounts/${props.account.id}`,
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
<Head :title="`Detail Akun - ${account.name}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 max-w-2xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <Button variant="outline" as-child>
                    <Link :href="route('accounts.index')">
                        <ArrowLeft class="w-4 h-4 mr-2" />
                        Kembali ke Daftar Akun
                    </Link>
                </Button>
                <Button>
                    <Pencil class="w-4 h-4 mr-2" />
                    Edit Akun
                </Button>
            </div>
            <Card>
                <CardHeader>
                    <CardTitle>Detail Akun</CardTitle>
                    <CardDescription>Rincian lengkap dari akun yang Anda pilih.</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Nama Akun</span>
                            <div class="flex items-center gap-2 font-medium">
                                <span class="text-2xl">{{ account.icon }}</span>
                                <span>{{ account.name }}</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Saldo Saat Ini</span>
                            <span class="font-bold text-xl text-primary">{{ formatCurrency(account.balance) }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Status</span>
                            <Badge :variant="account.is_active ? 'default' : 'secondary'">
                                {{ account.is_active ? 'Aktif' : 'Non-aktif' }}
                            </Badge>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Warna Tanda</span>
                            <div class="flex items-center gap-2">
                                <div :style="{ backgroundColor: account.color }" class="w-6 h-6 rounded-full border"></div>
                                <span>{{ account.color }}</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Tanggal Dibuat</span>
                            <span class="font-medium">{{ new Date(account.created_at).toLocaleDateString('id-ID', { dateStyle: 'long' }) }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
