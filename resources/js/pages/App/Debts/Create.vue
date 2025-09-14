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

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Utang & Piutang',
        href: '/debts',
    },
    {
        title: 'Buat Baru',
        href: '/debts/create',
    },
];

interface Party {
    id: number;
    name: string;
}
interface Account {
    id: number;
    name: string;
}

const props = defineProps<{
    parties: Party[];
    accounts: Account[];
}>();

const form = useForm({
    type: 'debt' as 'debt' | 'receivable',
    amount: undefined as number | undefined,
    description: '',
    due_date: new Date(),
    party_id: undefined as number | undefined,
    account_id: undefined as number | undefined,
});

const submit = () => {
    form.post(route('debts.store'));
};
</script>

<template>
    <Head title="Buat Utang & Piutang Baru" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6 max-w-3xl mx-auto">
            <div class="mb-6">
                <Button variant="outline" as-child>
                    <Link :href="route('debts.index')">
                        <ArrowLeft class="w-4 h-4 mr-2" />
                        Kembali ke Daftar Utang & Piutang
                    </Link>
                </Button>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Buat Utang & Piutang Baru</CardTitle>
                    <CardDescription>Catat utang atau piutang baru Anda.</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label>Tipe Catatan</Label>
                            <RadioGroup v-model="form.type" default-value="debt" class="flex gap-4">
                                <div class="flex items-center space-x-2">
                                    <RadioGroupItem id="r-debt" value="debt" />
                                    <Label for="r-debt">Saya Berutang</Label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <RadioGroupItem id="r-receivable" value="receivable" />
                                    <Label for="r-receivable">Pihak Berutang ke Saya</Label>
                                </div>
                            </RadioGroup>
                        </div>

                        <div class="space-y-2">
                            <Label for="amount">Jumlah</Label>
                            <Input id="amount" v-model="form.amount" type="number" placeholder="Contoh: 5000000" required />
                            <div v-if="form.errors.amount" class="text-red-500 text-xs mt-1">{{ form.errors.amount }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Deskripsi</Label>
                            <Textarea id="description" v-model="form.description" placeholder="Contoh: Pinjam untuk DP motor" />
                            <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="party">Pihak</Label>
                                <Select v-model="form.party_id">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Pilih pihak" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="party in props.parties" :key="party.id" :value="party.id">
                                            {{ party.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <div v-if="form.errors.party_id" class="text-red-500 text-xs mt-1">{{ form.errors.party_id }}</div>
                            </div>
                            <div class="space-y-2">
                                <Label for="account">Akun/Dompet (Opsional)</Label>
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
                            <Label for="due_date">Tanggal Jatuh Tempo</Label>
                            <Popover>
                                <PopoverTrigger as-child>
                                    <Button
                                        variant="outline"
                                        :class="cn('w-full justify-start text-left font-normal', !form.due_date && 'text-muted-foreground')"
                                    >
                                        <CalendarIcon class="mr-2 h-4 w-4" />
                                        {{ form.due_date ? format(form.due_date, "PPP", { locale: localeID }) : "Pilih tanggal" }}
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="w-auto p-0">
                                    <Calendar v-model="form.due_date" initial-focus />
                                </PopoverContent>
                            </Popover>
                            <div v-if="form.errors.due_date" class="text-red-500 text-xs mt-1">{{ form.errors.due_date }}</div>
                        </div>

                        <div class="flex justify-end gap-2 pt-4">
                            <Button variant="outline" as-child type="button">
                                <Link :href="route('debts.index')">Batal</Link>
                            </Button>
                            <Button type="submit" :disabled="form.processing">Simpan Catatan</Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
