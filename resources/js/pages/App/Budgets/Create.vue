<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft } from 'lucide-vue-next';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Budgeting',
        href: '/budgets',
    },
    {
        title: 'Buat Budget Baru',
        href: '/budgets/create',
    },
];

interface Category {
    id: number;
    name: string;
    icon: string;
    type: 'income' | 'expense';
}

interface Account {
    id: number;
    name: string;
    current_balance: string;
}

const props = defineProps<{
    categories: Category[];
}>();

const form = useForm({
    budgeted_amount: undefined as number | undefined,
    category_id: undefined as number | undefined,
});

const submit = () => {
    // Sesuaikan dengan route Inertia Anda
    form.post(route('budgets.store'));
};
</script>

<template>
    <Head title="Buat Budget Baru" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6 max-w-3xl mx-auto">
            <div class="mb-6">
                <Button variant="outline" as-child>
                    <Link :href="route('budgets.index')">
                        <ArrowLeft class="w-4 h-4 mr-2" />
                        Kembali ke Daftar Budget
                    </Link>
                </Button>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Buat Budget Baru</CardTitle>
                    <CardDescription>Tetapkan anggaran bulanan untuk kategori pengeluaran Anda.</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="category">Kategori</Label>
                            <Select v-model="form.category_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Pilih kategori pengeluaran" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="category in props.categories" :key="category.id" :value="category.id">
                                        {{ category.icon }} {{ category.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <div v-if="form.errors.category_id" class="text-red-500 text-xs mt-1">{{ form.errors.category_id }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="amount">Jumlah Anggaran</Label>
                            <Input id="amount" v-model="form.budgeted_amount" type="number" placeholder="Contoh: 1500000" required />
                            <div v-if="form.errors.budgeted_amount" class="text-red-500 text-xs mt-1">{{ form.errors.budgeted_amount }}</div>
                        </div>

                        <div class="flex justify-end gap-2 pt-4">
                            <Button variant="outline" as-child type="button">
                                <Link :href="route('budgets.index')">Batal</Link>
                            </Button>
                            <Button type="submit" :disabled="form.processing">Simpan Budget</Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
