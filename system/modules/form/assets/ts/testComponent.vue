<script setup>
    import { defineProps } from 'vue';
    const props = defineProps(['test']);
</script>
<template>
    <div>Hello {{props.test}}</div>
</template>