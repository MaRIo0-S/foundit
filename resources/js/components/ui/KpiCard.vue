<script setup>
defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], required: true },
    icon: { type: String, default: "analytics" },
    href: { type: String, default: null },
    accent: { type: Boolean, default: false },
    hint: { type: String, default: null },
});
</script>

<template>
    <component
        :is="href ? 'a' : 'div'"
        :href="href"
        class="kpi-card"
        :class="{ 'kpi-card--accent': accent }"
    >
        <div class="kpi-card__top">
            <span class="kpi-card__icon">
                <i class="material-symbols-rounded">{{ icon }}</i>
            </span>
        </div>
        <span class="kpi-card__value">{{ value }}</span>
        <span class="kpi-card__label">{{ label }}</span>
        <span class="kpi-card__hint">{{ hint || "" }}</span>
    </component>
</template>

<style lang="scss" scoped>
.kpi-card {
    background: #fff;
    border: 1.5px solid rgba(15, 43, 76, 0.08);
    border-radius: var(--radius-md);
    padding: 1.35rem 1.5rem;
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 132px;
    gap: 0.15rem;
    text-decoration: none;
    color: inherit;
    transition:
        box-shadow var(--transition),
        border-color var(--transition);

    &:hover {
        border-color: rgba(15, 43, 76, 0.15);
        box-shadow: 0 4px 18px rgba(15, 43, 76, 0.07);
    }

    &--accent {
        border-color: rgba(232, 65, 10, 0.25);
        background: linear-gradient(
            135deg,
            rgba(232, 65, 10, 0.04) 0%,
            #fff 100%
        );
    }

    &__top {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.35rem;
    }

    &__icon {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-sm);
        background: rgba(15, 43, 76, 0.06);
        display: flex;
        align-items: center;
        justify-content: center;

        i {
            font-size: 1.1rem;
            color: var(--color-main);
            opacity: 0.7;
        }
    }

    &--accent &__icon {
        background: rgba(232, 65, 10, 0.1);

        i {
            color: var(--color-secondary);
            opacity: 1;
        }
    }

    &__value {
        font-family: var(--font-display);
        font-size: 1.85rem;
        font-weight: 800;
        color: var(--color-main);
        line-height: 1;
    }

    &--accent &__value {
        color: var(--color-secondary);
    }

    &__label {
        font-family: var(--font-body);
        font-size: 0.78rem;
        color: var(--color-text);
        opacity: 0.5;
    }

    &__hint {
        font-family: var(--font-body);
        font-size: 0.68rem;
        color: var(--color-secondary);
        font-weight: 500;
        margin-top: auto;
        padding-top: 0.15rem;
        min-height: 0.85rem;

        &:empty {
            visibility: hidden;
        }
    }
}
</style>
