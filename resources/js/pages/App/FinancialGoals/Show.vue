<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Pencil } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Sasaran Keuangan',
        // Ganti dengan route() helper
        href: route('financial-goals.index'),
    },
    {
        title: 'Detail Sasaran',
        // Ganti dengan route() helper
        href: route('financial-goals.show'),
    },
];

interface Goal {
    id: number;
    name: string;
    description: string | null;
    target_amount: string;
    current_amount: string;
    target_date: string;
    status: 'in_progress' | 'completed';
    percentage: number;
    remaining_amount: string;
}

const props = defineProps<{
    goal: Goal;
}>();

const formatCurrency = (value: string) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(parseFloat(value));
};

const getStatusBadgeVariant = (status: string) => {
    switch (status) {
        case 'in_progress':
            return 'default';
        case 'completed':
            return 'success';
        default:
            return 'secondary';
    }
};

const getStatusText = (status: string) => {
    switch (status) {
        case 'in_progress':
            return 'Dalam Proses';
        case 'completed':
            return 'Selesai';
        default:
            return 'Tidak Dikenal';
    }
};
</script>

<template>
    <Head :title="`Detail ${goal.name}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl space-y-6 p-6">
            <div class="flex items-center justify-between">
                <Button variant="outline" as-child>
                    <Link :href="route('financial-goals.index')">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Kembali ke Daftar Sasaran
                    </Link>
                </Button>
                <Button as-child>
                    <Link :href="route('financial-goals.edit', goal.id)">
                        <Pencil class="mr-2 h-4 w-4" />
                        Edit Sasaran
                    </Link>
                </Button>
            </div>
            <Card>
                <CardHeader>
                    <CardTitle>{{ goal.name }}</CardTitle>
                    <CardDescription>Rincian lengkap dari sasaran keuangan Anda.</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between rounded-lg border p-4">
                            <span class="text-muted-foreground">Deskripsi</span>
                            <span class="text-right font-medium">{{ goal.description || '-' }}</span>
                        </div>

                        <div class="flex items-center justify-between rounded-lg border p-4">
                            <span class="text-muted-foreground">Jumlah Target</span>
                            <span class="text-lg font-bold">{{ formatCurrency(goal.target_amount) }}</span>
                        </div>

                        <div class="flex items-center justify-between rounded-lg border p-4">
                            <span class="text-muted-foreground">Jumlah Terkumpul</span>
                            <span class="text-lg font-bold">{{ formatCurrency(goal.current_amount) }}</span>
                        </div>

                        <div class="flex items-center justify-between rounded-lg border p-4">
                            <span class="text-muted-foreground">Sisa Jumlah</span>
                            <span class="text-lg font-bold">{{ formatCurrency(goal.remaining_amount) }}</span>
                        </div>

                        <div class="space-y-2 rounded-lg border p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-muted-foreground">Persentase</span>
                                <span class="font-medium">{{ goal.percentage.toFixed(0) }}%</span>
                            </div>
                            <Progress :model-value="goal.percentage" />
                        </div>

                        <div class="flex items-center justify-between rounded-lg border p-4">
                            <span class="text-muted-foreground">Tanggal Target</span>
                            <span class="font-medium">{{ new Date(goal.target_date).toLocaleDateString('id-ID', { dateStyle: 'long' }) }}</span>
                        </div>

                        <div class="flex items-center justify-between rounded-lg border p-4">
                            <span class="text-muted-foreground">Status</span>
                            <Badge :variant="getStatusBadgeVariant(goal.status)" class="capitalize">
                                {{ getStatusText(goal.status) }}
                            </Badge>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
