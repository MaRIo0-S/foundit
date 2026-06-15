<script setup>
import { computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { route } from "ziggy-js";

const page = usePage();
const pendingClaims = computed(() => page.props.adminNav?.pendingClaims ?? 0);

const items = [
    {
        name: "Tableau de bord",
        route: "admin.dashboard",
        icon: "dashboard",
        match: (url) => url === "/admin" || url === "/admin/",
    },
    {
        name: "Réclamations",
        route: "admin.claims.index",
        icon: "hand_gesture",
        badge: pendingClaims,
        match: (url) => url.startsWith("/admin/claims"),
    },
];

function isActive(item) {
    return item.match(page.url);
}
</script>

<template>
    <aside class="admin-sidebar">
        <div class="admin-sidebar__head">
            <i class="material-symbols-rounded">admin_panel_settings</i>
            <span>Administration</span>
        </div>

        <nav class="admin-sidebar__nav">
            <Link
                v-for="item in items"
                :key="item.route"
                :href="route(item.route)"
                class="admin-sidebar__link"
                :class="{ 'is-active': isActive(item) }"
            >
                <i class="material-symbols-rounded">{{ item.icon }}</i>
                <span>{{ item.name }}</span>
                <span v-if="item.badge > 0" class="admin-sidebar__badge">
                    {{ item.badge }}
                </span>
            </Link>
        </nav>

        <div class="admin-sidebar__footer">
            <Link :href="route('home')" class="admin-sidebar__back">
                <i class="material-symbols-rounded">arrow_back</i>
                Retour à l'application
            </Link>
        </div>
    </aside>
</template>

<style lang="scss" scoped>
.admin-sidebar {
    width: 240px;
    flex-shrink: 0;
    min-height: calc(100vh - 58px);
    background-color: var(--color-main);
    display: flex;
    flex-direction: column;
    border-right: 1px solid rgba(255, 255, 255, 0.06);

    @media (max-width: 900px) {
        width: 200px;
    }

    @media (max-width: 768px) {
        display: none;
    }

    &__head {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 1.25rem 1.15rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        font-family: var(--font-display);
        font-size: 0.88rem;
        font-weight: 700;
        color: #fff;

        i {
            font-size: 1.15rem;
            color: var(--color-secondary);
        }
    }

    &__nav {
        flex: 1;
        overflow-y: auto;
        padding: 0.85rem 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    &__link {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.65rem 0.8rem;
        border-radius: var(--radius-sm);
        border-left: 3px solid transparent;
        font-family: var(--font-body);
        font-size: 0.84rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.78);
        transition:
            background-color var(--transition),
            color var(--transition);

        i {
            font-size: 1.1rem;
            flex-shrink: 0;
            opacity: 0.8;
        }

        span:nth-child(2) {
            flex: 1;
        }

        &:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        &.is-active {
            background: rgba(232, 65, 10, 0.15);
            border-left-color: var(--color-secondary);
            color: #fff;

            i {
                opacity: 1;
            }
        }
    }

    &__badge {
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        border-radius: 10px;
        background: var(--color-secondary);
        color: #fff;
        font-size: 0.65rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    &__footer {
        padding: 1rem 0.85rem;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
    }

    &__back {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.55rem 0.7rem;
        font-family: var(--font-body);
        font-size: 0.78rem;
        color: rgba(255, 255, 255, 0.55);
        border-radius: var(--radius-sm);
        transition:
            color var(--transition),
            background-color var(--transition);

        i {
            font-size: 1rem;
        }

        &:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.06);
        }
    }
}
</style>
