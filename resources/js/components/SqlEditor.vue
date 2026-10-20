<template>
    <div class="w-full h-full flex">
        <div
            v-show="!failed"
            ref="editor"
            class="flex-1 overflow-hidden"
        />
        <div
            v-if="failed"
            class="flex-1 flex flex-col overflow-hidden"
        >
            <p class="px-4 py-2 text-sm text-red-600 bg-red-50 border-b border-red-200">
                The SQL editor could not be loaded, so syntax highlighting is unavailable. Please re-run <code>php artisan dibi:publish</code> to republish the Dibi assets.
            </p>
            <textarea
                :value="modelValue"
                class="flex-1 w-full px-4 py-2 font-mono text-sm border-0 resize-none focus:ring-0"
                spellcheck="false"
                @input="$emit('update:modelValue', $event.target.value)"
            />
        </div>
    </div>
</template>

<script>
export default {
    name: 'SqlEditor',

    props: ['modelValue'],

    emits: ['update:modelValue'],

    data() {
        return {
            failed: false,
        };
    },

    async mounted() {
        let monaco;

        try {
            monaco = (await import('../monaco')).default;
        } catch (e) {
            // Fall back to a plain textarea rather than leaving an empty pane
            // behind, which is what the editor used to do when it failed to load.
            console.error('Dibi: failed to load the SQL editor.', e);
            this.failed = true;

            return;
        }

        this.editor = monaco.editor.create(this.$refs.editor, {
            value: this.modelValue,
            language: 'sql',
            minimap: { enabled: false },
            automaticLayout: true,
            scrollBeyondLastLine: false,
            wordWrap: 'on',
            wrappingStrategy: 'advanced',
            overviewRulerLanes: 0,
        });

        this.editor.onDidChangeModelContent(() => {
            this.$emit('update:modelValue', this.editor.getValue());
        });
    },

    beforeUnmount() {
        this.editor?.dispose();
    },
};
</script>
