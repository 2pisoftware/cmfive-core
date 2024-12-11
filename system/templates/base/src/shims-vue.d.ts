declare module '*.vue' {
    import type { defineComponent } from 'vue3';
    const component: defineComponent<{}, {}, any>;
    export default component;
}