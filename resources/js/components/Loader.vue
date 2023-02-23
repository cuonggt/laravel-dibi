<template>
    <div>
        <div
            class="flex items-center mt-4"
            style="height: 150px"
        >
            <IconLoader
                :class="`mx-auto block text-${color}-500`"
                :style="{ width: `${width}px` }"
                viewBox="0 0 120 30"
                :fill="fillColor"
            />
        </div>

        <div class="mt-4 text-center max-w-xl mx-auto">
            <slot />
        </div>
    </div>
</template>

<script>
import IconLoader from './icons/Loader.vue';

export default {
    name: 'Loader',
    components: {IconLoader},
    props: {
        color: {
            type: [String],
            required: false,
            default: 'gray',
        },
        width: {
            type: [Number, String],
            required: false,
            default: 50,
        },
        fillColor: {
            type: String,
            required: false,
            default: 'currentColor',
        },
    },
};
</script>
