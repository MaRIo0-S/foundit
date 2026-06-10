<script setup>
import { Link } from "@inertiajs/vue3";
defineProps({
    lien: String,
    content: String,
});
</script>
<template>
    <Link class="main-link" :href="lien">
        {{ content }}
        <i class="material-symbols-rounded">arrow_forward</i>
    </Link>
</template>
<style lang="scss" scoped>
.main-link {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-family: var(--font-body);
    font-size: 0.85rem;
    font-weight: 600;
    letter-spacing: 0.01em;
    color: #fff;
    background-color: var(--color-secondary);
    text-decoration: none;
    padding: 0.55rem 1.1rem;
    border-radius: var(--radius-sm);
    border: 1.5px solid var(--color-secondary);
    transition:
        background-color var(--transition),
        color var(--transition),
        border-color var(--transition),
        transform var(--transition);
    cursor: pointer;
    white-space: nowrap;

    i {
        font-size: 0.9rem;
        transition: transform var(--transition);
    }

    &:hover {
        background-color: var(--color-main);
        border-color: var(--color-main);
        color: #fff;

        i {
            transform: translateX(3px);
        }
    }

    &:active {
        transform: scale(0.97);
    }

    @media (max-width: 480px) {
        width: 100%;
        justify-content: center;
    }
}
</style>
