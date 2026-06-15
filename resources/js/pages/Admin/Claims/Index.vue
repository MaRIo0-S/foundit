<script setup>
import { ref, watch } from "vue";
import { Link, router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import StatusBadge from "../../../components/ui/StatusBadge.vue";
import EmptyState from "../../../components/ui/EmptyState.vue";

const props = defineProps({
    claims: Object,
    filters: Object,
    pendingCount: Number,
});

const search = ref(props.filters?.search ?? "");
const status = ref(props.filters?.status ?? "");
const sort = ref(props.filters?.sort ?? "newest");

let timeout = null;

function applyFilters() {
    router.get(
        route("admin.claims.index"),
        {
            search: search.value || undefined,
            status: status.value || undefined,
            sort: sort.value !== "newest" ? sort.value : undefined,
        },
        { preserveState: true, preserveScroll: true },
    );
}

watch([status, sort], applyFilters);

function onSearch() {
    clearTimeout(timeout);
    timeout = setTimeout(applyFilters, 400);
}

function hoursSince(dateStr) {
    const diff = Date.now() - new Date(dateStr).getTime();
    const hours = Math.floor(diff / 3600000);
    if (hours < 24) return `${hours}h`;
    return `${Math.floor(hours / 24)}j`;
}

function priorityClass(dateStr, claimStatus) {
    if (claimStatus !== "pending") return "";
    const hours = (Date.now() - new Date(dateStr).getTime()) / 3600000;
    if (hours > 48) return "priority--high";
    if (hours > 24) return "priority--medium";
    return "";
}
</script>

<template>
    <div class="admin-page">
        <header class="admin-page__header">
            <h1 class="admin-page__title">Réclamations</h1>
            <p class="admin-page__subtitle">
                <span v-if="pendingCount > 0" class="claims-index__pending">
                    ● {{ pendingCount }} en attente de traitement
                </span>
                <span v-else>Toutes les réclamations sont traitées</span>
            </p>
        </header>

        <div class="admin-filters filters">
            <div class="filters__search-wrap">
                <i class="material-symbols-rounded filters__icon">search</i>
                <input
                    v-model="search"
                    class="filters__input"
                    type="text"
                    placeholder="Rechercher objet, réclamant..."
                    @input="onSearch"
                />
            </div>
            <select v-model="status" class="filters__select">
                <option value="">Tous les statuts</option>
                <option value="pending">En attente</option>
                <option value="approved">Approuvées</option>
                <option value="rejected">Rejetées</option>
            </select>
            <select v-model="sort" class="filters__select">
                <option value="newest">Plus récentes</option>
                <option value="oldest">Plus anciennes (SLA)</option>
            </select>
        </div>

        <EmptyState
            v-if="!claims.data?.length"
            icon="hand_gesture"
            title="Aucune réclamation trouvée"
            description="Modifiez vos filtres ou attendez de nouvelles réclamations."
        />

        <div v-else class="admin-table-wrap table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Priorité</th>
                        <th>Objet</th>
                        <th>Réclamant</th>
                        <th>Notes</th>
                        <th>Statut</th>
                        <th>Soumise</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="claim in claims.data"
                        :key="claim.id"
                        :class="priorityClass(claim.created_at, claim.status)"
                    >
                        <td>
                            <span
                                v-if="claim.status === 'pending'"
                                class="priority-dot"
                                :class="priorityClass(claim.created_at, claim.status)"
                            ></span>
                        </td>
                        <td>
                            <div class="item-cell">
                                <img
                                    v-if="claim.item?.image_path"
                                    :src="`/storage/items/${claim.item.image_path}`"
                                    :alt="claim.item.name"
                                    class="item-cell__img"
                                />
                                <div>
                                    <span class="item-cell__name">{{ claim.item?.name }}</span>
                                    <span class="item-cell__meta">
                                        {{ claim.item?.category?.name }} · {{ claim.item?.location?.name }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="user-cell">{{ claim.user?.name }}</span>
                            <span class="user-cell__email">{{ claim.user?.email }}</span>
                        </td>
                        <td class="notes-cell">
                            {{ claim.claim_notes?.slice(0, 60) }}{{ claim.claim_notes?.length > 60 ? '…' : '' }}
                        </td>
                        <td><StatusBadge :status="claim.status" /></td>
                        <td>
                            {{ new Date(claim.created_at).toLocaleDateString('fr-FR') }}
                            <span v-if="claim.status === 'pending'" class="sla">
                                ({{ hoursSince(claim.created_at) }})
                            </span>
                        </td>
                        <td>
                            <Link
                                v-if="claim.status === 'pending'"
                                :href="route('admin.claims.review', claim.id)"
                                class="btn-action btn-action--primary"
                            >
                                Traiter
                            </Link>
                            <Link
                                v-else
                                :href="route('admin.claims.review', claim.id)"
                                class="btn-action"
                            >
                                Voir
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="claims.last_page > 1" class="pagination">
                <Link
                    v-for="link in claims.links"
                    :key="link.label"
                    :href="link.url"
                    class="pagination__btn"
                    :class="{ 'is-active': link.active, 'is-disabled': !link.url }"
                    v-html="link.label"
                />
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.claims-index__pending {
    color: var(--color-secondary);
    font-weight: 600;
    opacity: 1;
}

.filters {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;

    &__search-wrap {
        position: relative;
        flex: 1;
        min-width: 200px;
    }

    &__icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.05rem;
        opacity: 0.35;
    }

    &__input,
    &__select {
        height: 40px;
        border: 1.5px solid rgba(15, 43, 76, 0.12);
        border-radius: var(--radius-sm);
        background: #fff;
        font-family: var(--font-body);
        font-size: 0.84rem;
        color: var(--color-text);
        outline: none;

        &:focus {
            border-color: var(--color-main);
        }
    }

    &__input {
        width: 100%;
        padding: 0 0.75rem 0 2.4rem;
    }

    &__select {
        padding: 0 0.75rem;
    }
}

.table-wrap {
    overflow: hidden;
}

.data-table {
    width: 100%;
    border-collapse: collapse;

    th {
        font-size: 0.68rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--color-main);
        text-align: left;
        padding: 0.75rem 1rem;
        background: rgba(15, 43, 76, 0.02);
        border-bottom: 1.5px solid rgba(15, 43, 76, 0.08);
    }

    td {
        font-size: 0.82rem;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(15, 43, 76, 0.05);
        vertical-align: middle;
    }

    tbody tr:hover {
        background: rgba(15, 43, 76, 0.02);
    }

    tbody tr.priority--high {
        background: rgba(220, 38, 38, 0.03);
    }

    tbody tr.priority--medium {
        background: rgba(234, 179, 8, 0.04);
    }
}

.priority-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #16a34a;

    &.priority--medium {
        background: #eab308;
    }

    &.priority--high {
        background: #dc2626;
    }
}

.item-cell {
    display: flex;
    align-items: center;
    gap: 0.65rem;

    &__img {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-sm);
        object-fit: cover;
    }

    &__name {
        display: block;
        font-weight: 600;
        color: var(--color-main);
    }

    &__meta {
        display: block;
        font-size: 0.72rem;
        opacity: 0.45;
    }
}

.user-cell {
    display: block;
    font-weight: 500;

    &__email {
        display: block;
        font-size: 0.72rem;
        opacity: 0.45;
    }
}

.notes-cell {
    max-width: 200px;
    opacity: 0.7;
}

.sla {
    font-size: 0.72rem;
    opacity: 0.5;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    height: 32px;
    padding: 0 0.85rem;
    font-family: var(--font-display);
    font-size: 0.76rem;
    font-weight: 700;
    border-radius: var(--radius-sm);
    border: 1.5px solid rgba(15, 43, 76, 0.15);
    color: var(--color-main);

    &--primary {
        background: var(--color-secondary);
        border-color: var(--color-secondary);
        color: #fff;
    }
}

.pagination {
    display: flex;
    gap: 0.35rem;
    padding: 1.25rem 1rem;
    justify-content: center;
    flex-wrap: wrap;

    &__btn {
        min-width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid rgba(15, 43, 76, 0.12);
        border-radius: var(--radius-sm);
        font-size: 0.82rem;
        color: var(--color-main);

        &.is-active {
            background: var(--color-main);
            color: #fff;
            border-color: var(--color-main);
        }

        &.is-disabled {
            opacity: 0.35;
            pointer-events: none;
        }
    }
}
</style>
