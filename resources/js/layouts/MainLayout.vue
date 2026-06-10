<script setup>
import FlashToaster from "../components/ui/FlashToaster.vue";
import { useFlash } from "../composables/useFlash";
import NavBar from "../components/ui/NavigationBars/NavBar.vue";
useFlash();
</script>

<template>
    <div class="main-layout">
        <FlashToaster />
        <header class="main-layout__header">
            <NavBar />
        </header>
        <main class="main-layout__content">
            <slot />
        </main>
    </div>
</template>

<style lang="scss" scoped>
.main-layout {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background-color: var(--color-third);

    &__header {
        flex-shrink: 0;
    }

    &__content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
}
</style>
