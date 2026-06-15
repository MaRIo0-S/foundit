<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import FlashToaster from "../components/ui/FlashToaster.vue";
import { useFlash } from "../composables/useFlash";
import NavBar from "../components/ui/NavigationBars/NavBar.vue";
import AdminSideBar from "../components/ui/NavigationBars/AdminSideBar.vue";

useFlash();

const page = usePage();
const isAdminArea = computed(
    () => page.props.auth?.is_admin && page.url.startsWith("/admin"),
);
</script>

<template>
    <div class="main-layout" :class="{ 'main-layout--admin': isAdminArea }">
        <FlashToaster />
        <header class="main-layout__header">
            <NavBar />
        </header>

        <div class="main-layout__body">
            <AdminSideBar v-if="isAdminArea" />
            <main class="main-layout__content">
                <slot />
            </main>
        </div>
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

    &__body {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    &--admin &__body {
        flex-direction: row;
        align-items: stretch;
    }

    &__content {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    &--admin &__content {
        background-color: #f8f9fb;
        overflow-x: hidden;
    }
}
</style>
