<template>
    <portal to="modal">
        <transition leave-active-class="duration-200">
            <div
                v-show="show"
                class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0"
            >
                <transition
                    enter-active-class="ease-out duration-300"
                    enter-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-200"
                    leave-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div
                        v-show="show"
                        class="fixed inset-0 transform transition-all"
                        @click="close"
                    >
                        <div class="absolute inset-0 bg-gray-500 opacity-75" />
                    </div>
                </transition>

                <transition
                    enter-active-class="ease-out duration-300"
                    enter-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="ease-in duration-200"
                    leave-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    <div
                        v-show="show"
                        class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto"
                        :class="maxWidthClass"
                    >
                        <slot />
                    </div>
                </transition>
            </div>
        </transition>
    </portal>
</template>

<script>
export default {
    props: {
        show: {
            type: Boolean,
            default: false,
        },
        maxWidth: {
            type: String,
            default: '2xl',
        },
        closeable: {
            type: Boolean,
            default: true,
        },
    },

    computed: {
        maxWidthClass() {
            return {
                'sm': 'sm:max-w-sm',
                'md': 'sm:max-w-md',
                'lg': 'sm:max-w-lg',
                'xl': 'sm:max-w-xl',
                '2xl': 'sm:max-w-2xl',
                '3xl': 'sm:max-w-3xl',
                '4xl': 'sm:max-w-4xl',
                '5xl': 'sm:max-w-5xl',
                '6xl': 'sm:max-w-6xl',
                '7xl': 'sm:max-w-7xl',
            }[this.maxWidth];
        },
    },

    watch: {
        show: {
            immediate: true,
            handler(show) {
                if (show) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = null;
                }
            },
        },
    },

    created() {
        const closeOnEscape = (e) => {
            if (e.key === 'Escape' && this.show) {
                this.close();
            }
        };

        document.addEventListener('keydown', closeOnEscape);

        this.$once('hook:destroyed', () => {
            document.removeEventListener('keydown', closeOnEscape);
        });
    },

    methods: {
        close() {
            if (this.closeable) {
                this.$emit('close');
            }
        },
    },
};
</script>
