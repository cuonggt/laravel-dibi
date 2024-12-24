<template>
    <div class="w-full h-full flex">
        <div
            id="editor"
            class="flex-1 overflow-hidden"
        />
    </div>
</template>

<script>
import loader from '@monaco-editor/loader';

export default {
    name: 'SqlEditor',

    props: ['value'],

    async mounted() {
        const monaco = await loader.init();

        const editor = monaco.editor.create(document.getElementById('editor'), {
            value: this.value,
            language: 'sql',
            minimap: { enabled: false },
            automaticLayout: true,
            scrollBeyondLastLine: false,
            wordWrap: 'on',
            wrappingStrategy: 'advanced',
            overviewRulerLanes: 0,
        });

        editor.onDidChangeModelContent(() => {
            this.$emit('input', editor.getValue());
        });
    },
};
</script>
