<template>
    <div class="font-sans antialiased h-screen flex overflow-hidden bg-gray-200">
        <div class="flex shrink-0">
            <div class="flex flex-col w-64">
                <div class="flex flex-col h-0 flex-1">
                    <a :href="window.Dibi.path">
                        <div class="flex items-center h-16 px-4 bg-gray-900 text-xl text-white font-medium">
                            <svg
                                fill="#25C4F2"
                                viewBox="0 0 20 20"
                                class="w-12 h-12"
                            >
                                <path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z" />
                                <path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z" />
                                <path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z" />
                            </svg>
                            <div
                                class="ml-2"
                                style="padding-top: 2px;"
                            >
                                Dibi
                            </div>
                        </div>
                    </a>

                    <side-navigation />
                </div>
            </div>
        </div>

        <router-view :key="$route.name + ($route.params.tableName || '')" />

        <div id="modal-target" />
    </div>
</template>

<script>
import { useToast } from 'vue-toastification';

export default {
    mounted() {
        const toast = useToast();
        this._errorHandler = (message) => {
            toast.error(message);
        };
        Bus.on('error', this._errorHandler);
    },
    beforeUnmount() {
        Bus.off('error', this._errorHandler);
    },
};
</script>
