<script setup>
defineProps({
    icon: { type: String, default: "inbox" },
    title: { type: String, required: true },
    description: { type: String, default: null },
});
</script>

<template>
    <div class="empty-state">
        <span class="empty-state__icon">
            <i class="material-symbols-rounded">{{ icon }}</i>
        </span>
        <p class="empty-state__title">{{ title }}</p>
        <p v-if="description" class="empty-state__desc">{{ description }}</p>
        <slot />
    </div>
</template>

<style lang="scss" scoped>
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 3rem 2rem;
    background: #fff;
    border-radius: var(--radius-md);
    border: 1.5px solid rgba(15, 43, 76, 0.08);

    &__icon {
        width: 52px;
        height: 52px;
        border-radius: var(--radius-md);
        background: rgba(15, 43, 76, 0.05);
        display: flex;
        align-items: center;
        justify-content: center;

        i {
            font-size: 1.5rem;
            color: var(--color-main);
            opacity: 0.3;
        }
    }

    &__title {
        font-family: var(--font-display);
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--color-main);
    }

    &__desc {
        font-family: var(--font-body);
        font-size: 0.82rem;
        color: var(--color-text);
        opacity: 0.45;
        text-align: center;
        max-width: 360px;
    }
}
</style>
