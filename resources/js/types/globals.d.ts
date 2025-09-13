import { PageProps as InertiaPageProps, Page, Router } from '@inertiajs/core';
import { createHeadManager } from '@inertiajs/vue3';
import { AxiosInstance } from 'axios';
import { route as ziggyRoute } from 'ziggy-js';
import { AppPageProps } from '@/types/index';

// Extend ImportMeta interface for Vite... (Milik Anda, sudah benar)
declare module 'vite/client' {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME: string;
        [key: string]: string | boolean | undefined;
    }

    interface ImportMeta {
        readonly env: ImportMetaEnv;
        readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
    }
}

// Ditambahkan: Blok baru untuk mendefinisikan fungsi route() secara global
declare global {
    interface Window {
        axios: AxiosInstance;
    }

    const route: typeof ziggyRoute;
}

// Milik Anda: Augmentasi modul Inertia (sudah benar)
declare module '@inertiajs/core' {
    interface PageProps extends InertiaPageProps, AppPageProps {}
}

// Digabungkan: Augmentasi modul Vue
declare module 'vue' {
    interface ComponentCustomProperties {
        // Properti yang sudah ada
        $inertia: typeof Router;
        $page: Page;
        $headManager: ReturnType<typeof createHeadManager>;

        // Ditambahkan: Properti baru untuk helper route()
        route: typeof ziggyRoute;
    }
}

declare module 'ziggy-js';
