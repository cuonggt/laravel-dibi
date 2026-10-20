<template>
    <button
        :type="type"
        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-xs hover:text-gray-500 focus:outline-hidden focus:border-blue-300 focus:ring-3 focus:ring-blue-500 active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150"
    >
        <slot />
    </button>
</template>

<script>
export default {
    props: {
        type: {
            type: String,
            default: 'button',
        },
    },
};
</script>
