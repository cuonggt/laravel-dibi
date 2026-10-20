<template>
    <input
        ref="input"
        class="border-gray-300 focus:border-indigo-300 focus:ring-3 focus:ring-indigo-200/50 rounded-md shadow-xs"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    >
</template>

<script>
export default {
    props: {
        modelValue: {
            type: String,
            default: null,
        },
    },

    emits: ['update:modelValue'],

    methods: {
        focus() {
            this.$refs.input.focus();
        },
    },
};
</script>
