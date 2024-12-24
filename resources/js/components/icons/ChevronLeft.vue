<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        :class="sizeClass"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15 19l-7-7 7-7"
        />
    </svg>
</template>

<script>
import Sizable from './Sizable';

export default {
    mixins: [Sizable],
};
</script>
