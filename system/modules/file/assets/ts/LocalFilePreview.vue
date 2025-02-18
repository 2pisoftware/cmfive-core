<script setup lang="ts">
    import { computed } from "vue";

    const props = defineProps<{
        file: File
    }>();

    const src = computed(() => URL.createObjectURL(props.file));
</script>

<template>
    <img v-if="props.file.type.startsWith('image')" :src="src" :alt="props.file.name" height="200" class="img-thumbnail h-100"/>
    <audio v-else-if="props.file.type.startsWith('audio')" :src="src" :title="props.file.name" controls></audio>
    <video v-else-if="props.file.type.startsWith('video')" :src="src" :title="props.file.name" height="200" controls></video>
    <a v-else rel="noreferer" target="_blank" :href="src">{{ props.file.name }}</a>
</template>