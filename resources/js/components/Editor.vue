<template>
    <div>
        <div
            id="editor"
            style="width: 500px; height: 500px"
        />
    </div>
</template>
<script>
import loader from '@monaco-editor/loader';
export default {
    name: 'Editor',
    async mounted() {
        loader.init().then((monaco) => {
            const editorOptions = {
                language: 'sql',
                minimap: { enabled: false },
            };

            monaco.editor.create(document.getElementById('editor'), editorOptions);
        });
    },
};
</script>
