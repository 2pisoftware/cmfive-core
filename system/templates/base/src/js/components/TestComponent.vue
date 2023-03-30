<template>
    <p>{{ hello }}</p>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';

@Component({
    name: 'test-component'
})
export default class extends Vue {
    public hello: string = 'HELLO';
}
</script>

<style lang="scss" scoped>

</style>