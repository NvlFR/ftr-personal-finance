<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';

// Mengimpor library chart.js
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js'
import { Bar } from 'vue-chartjs'

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)

const props = defineProps<{
    summary: {
        totalBalance: string;
        incomeThisMonth: string;
        expenseThisMonth: string;
    };
    accounts: {
        name: string;
        balance: string;
        icon: string;
        color: string;
    }[];
    latestTransactions: {
        id: number;
        description: string;
        amount: string;
        type: 'expense' | 'income';
        transaction_date: string;
        category: {
            name: string;
            icon: string;
        };
        account: {
            name: string;
        };
    }[];
    activeGoals: {
        id: number;
        name: string;
        current_amount: string;
        target_amount: string;
        percentage: number;
    }[];
    expenseChartData: {
        labels: string[];
        data: number[];
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

// Data dan opsi untuk Chart.js
const chartData = computed(() => ({
    labels: props.expenseChartData.labels,
    datasets: [{
        label: 'Pengeluaran',
        backgroundColor: '#ef4444',
        data: props.expenseChartData.data,
    }]
}));

const chartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        },
        tooltip: {
            callbacks: {
                label: function(context: any) {
                    let label = context.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    if (context.parsed.y !== null) {
                        label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                    }
                    return label;
                }
            }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: function(value: any) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
                },
            },
        },
    },
});

// Fungsi untuk memformat mata uang
const formatCurrency = (value: string | number) => {
    const number = typeof value === 'string' ? parseFloat(value) : value;
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(number);
};

// Fungsi untuk memformat tanggal
const formatDate = (dateString: string) => {
    const options: Intl.DateTimeFormatOptions = { day: 'numeric', month: 'long', year: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
};

// Simulasikan data yang hilang untuk melihat efeknya
// const hasGoals = computed(() => props.activeGoals && props.activeGoals.length > 0);
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
            <!-- Summary Cards -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-gray-300 dark:border-gray-700 p-6 shadow-md flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Saldo</p>
                        <h2 class="text-2xl font-bold mt-1 text-gray-900 dark:text-gray-100">
                            {{ formatCurrency(summary.totalBalance) }}
                        </h2>
                    </div>
                    <span class="text-4xl text-blue-500">💰</span>
                </div>
                <div class="rounded-xl border border-gray-300 dark:border-gray-700 p-6 shadow-md flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Pemasukan Bulan Ini</p>
                        <h2 class="text-2xl font-bold mt-1 text-green-600 dark:text-green-400">
                            {{ formatCurrency(summary.incomeThisMonth) }}
                        </h2>
                    </div>
                    <span class="text-4xl text-green-500">📈</span>
                </div>
                <div class="rounded-xl border border-gray-300 dark:border-gray-700 p-6 shadow-md flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Pengeluaran Bulan Ini</p>
                        <h2 class="text-2xl font-bold mt-1 text-red-600 dark:text-red-400">
                            {{ formatCurrency(summary.expenseThisMonth) }}
                        </h2>
                    </div>
                    <span class="text-4xl text-red-500">📉</span>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-4">
                <!-- Latest Transactions Widget -->
                <div class="lg:col-span-2 rounded-xl border border-gray-300 dark:border-gray-700 p-6 shadow-md">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Aktivitas Terakhir</h3>
                    <ul v-if="latestTransactions.length" class="space-y-4">
                        <li v-for="transaction in latestTransactions" :key="transaction.id" class="flex items-center justify-between py-2 border-b last:border-b-0 border-gray-200 dark:border-gray-600">
                            <div class="flex items-center space-x-4">
                                <div :class="[transaction.type === 'expense' ? 'bg-red-100 dark:bg-red-900' : 'bg-green-100 dark:bg-green-900', 'p-2 rounded-full']">
                                    <span class="text-xl">{{ transaction.category.icon }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ transaction.description }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ transaction.account.name }} · {{ formatDate(transaction.transaction_date) }}</p>
                                </div>
                            </div>
                            <span :class="[transaction.type === 'expense' ? 'text-red-600' : 'text-green-600', 'font-semibold']">
                                {{ formatCurrency(transaction.amount) }}
                            </span>
                        </li>
                    </ul>
                    <div v-else class="text-center text-gray-500 dark:text-gray-400 py-10">
                        Tidak ada transaksi terbaru.
                    </div>
                </div>

                <!-- Account Balances Widget -->
                <div class="rounded-xl border border-gray-300 dark:border-gray-700 p-6 shadow-md">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Saldo Akun</h3>
                    <ul v-if="accounts.length" class="space-y-4">
                        <li v-for="account in accounts" :key="account.name" class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <span class="text-2xl">{{ account.icon }}</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ account.name }}</p>
                            </div>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ formatCurrency(account.balance) }}</span>
                        </li>
                    </ul>
                    <div v-else class="text-center text-gray-500 dark:text-gray-400 py-10">
                        Belum ada akun yang terdaftar.
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-2 gap-4">
                <!-- Financial Goals Widget -->
                <div class="rounded-xl border border-gray-300 dark:border-gray-700 p-6 shadow-md">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Tujuan Keuangan Aktif</h3>
                    <ul v-if="activeGoals.length" class="space-y-6">
                        <li v-for="goal in activeGoals" :key="goal.id">
                            <div class="flex justify-between items-center mb-1">
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ goal.name }}</p>
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">{{ goal.percentage.toFixed(0) }}%</p>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-2.5">
                                <div
                                    class="bg-blue-600 h-2.5 rounded-full"
                                    :style="{ width: `${goal.percentage}%` }"
                                ></div>
                            </div>
                            <div class="flex justify-between items-center text-sm text-gray-500 dark:text-gray-400 mt-1">
                                <span>Terkumpul: {{ formatCurrency(goal.current_amount) }}</span>
                                <span>Target: {{ formatCurrency(goal.target_amount) }}</span>
                            </div>
                        </li>
                    </ul>
                    <div v-else class="text-center text-gray-500 dark:text-gray-400 py-10">
                        Tidak ada tujuan keuangan aktif.
                    </div>
                </div>
                
                <!-- Expense Chart Widget -->
                <div class="rounded-xl border border-gray-300 dark:border-gray-700 p-6 shadow-md">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Grafik Pengeluaran (7 Hari Terakhir)</h3>
                    <div class="h-64">
                        <Bar
                            id="expense-chart"
                            :data="chartData"
                            :options="chartOptions"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Anda bisa menambahkan gaya kustom di sini jika diperlukan */
</style>
