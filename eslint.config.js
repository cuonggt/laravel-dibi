const pluginVue = require('eslint-plugin-vue');

module.exports = [
    {
        ignores: [
            'node_modules/',
            'public/',
            'vendor/',
        ],
    },
    ...pluginVue.configs['flat/recommended'],
    {
        rules: {
            'vue/multi-word-component-names': ['off'],
            'vue/require-prop-types': ['off'],
            'vue/require-default-prop': ['off'],
            'vue/html-indent': ['error', 4],
            'indent': ['error', 4],
            'semi': ['error', 'always'],
            'quotes': ['error', 'single'],
            'comma-dangle': ['error', 'always-multiline'],
            'object-curly-spacing': ['error', 'always'],
            'comma-spacing': ['error', { 'before': false, 'after': true }],
        },
    },
];
