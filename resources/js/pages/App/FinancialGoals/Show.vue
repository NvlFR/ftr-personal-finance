<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Progress } from '@/components/ui/progress';
import { ArrowLeft, Pencil } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

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

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Sasaran Keuangan',
        href: '/goals',
    },
    {
        title: 'Detail Sasaran',
        href: `/goals/${props.goal.id}`,
    },
];

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
        <div class="p-6 max-w-2xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <Button variant="outline" as-child>
                    <Link :href="route('goals.index')">
                        <ArrowLeft class="w-4 h-4 mr-2" />
                        Kembali ke Daftar Sasaran
                    </Link>
                </Button>
                <Button as-child>
                    <Link :href="route('goals.edit', goal.id)">
                        <Pencil class="w-4 h-4 mr-2" />
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
                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Deskripsi</span>
                            <span class="font-medium text-right">{{ goal.description || '-' }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Jumlah Target</span>
                            <span class="font-bold text-lg">{{ formatCurrency(goal.target_amount) }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Jumlah Terkumpul</span>
                            <span class="font-bold text-lg">{{ formatCurrency(goal.current_amount) }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Sisa Jumlah</span>
                            <span class="font-bold text-lg">{{ formatCurrency(goal.remaining_amount) }}</span>
                        </div>
                        
                        <div class="space-y-2 p-4 border rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-muted-foreground">Persentase</span>
                                <span class="font-medium">{{ goal.percentage.toFixed(0) }}%</span>
                            </div>
                            <Progress :model-value="goal.percentage" />
                        </div>

                        <div class="flex justify-between items-center p-4 border rounded-lg">
                            <span class="text-muted-foreground">Tanggal Target</span>
                            <span class="font-medium">{{ new Date(goal.target_date).toLocaleDateString('id-ID', { dateStyle: 'long' }) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center p-4 border rounded-lg">
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
