<script setup>
import { useFlash } from "../composables/useFlash";
import FlashToaster from "../components/ui/FlashToaster.vue";
useFlash();
</script>

<template>
    <div class="landing-layout">
        <FlashToaster />
        <slot />
    </div>
</template>

<style lang="scss" scoped>
.landing-layout {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background-color: var(--color-third);
}
</style>
