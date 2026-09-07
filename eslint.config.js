/**
 * ESLint configuration.
 *
 * Deliberately small: this project has no framework and no build step, so the
 * only thing worth enforcing is that the browser will actually run the file.
 */

export default [
    {
        files: ['public/assets/js/**/*.js'],
        languageOptions: {
            ecmaVersion: 2024,
            sourceType: 'module',
            globals: {
                clearInterval: 'readonly',
                console: 'readonly',
                document: 'readonly',
                localStorage: 'readonly',
                setInterval: 'readonly',
                window: 'readonly',
            },
        },
        rules: {
            'no-unused-vars': 'error',
            'no-undef': 'error',
            'prefer-const': 'error',
            eqeqeq: 'error',
        },
    },
];
