<script setup>
import FlashToaster from "../components/ui/FlashToaster.vue";
import { useFlash } from "../composables/useFlash";
useFlash();
</script>

<template>
    <div class="register-layout">
        <FlashToaster />
        <slot />
    </div>
</template>

<style lang="scss" scoped>
.register-layout {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}
</style>
