<script setup>
import { Link } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import KpiCard from "../../components/ui/KpiCard.vue";
import StatusBadge from "../../components/ui/StatusBadge.vue";
import EmptyState from "../../components/ui/EmptyState.vue";

defineProps({
    stats: Object,
    itemsByCategory: Array,
    itemsByLocation: Array,
    recentItems: Array,
    pendingClaims: Array,
    declarationsTrend: Array,
});

const maxCategory = (items) =>
    Math.max(...items.map((i) => i.count), 1);

const maxLocation = (items) =>
    Math.max(...items.map((i) => i.count), 1);

const maxTrend = (trend) =>
    Math.max(...trend.map((t) => t.count), 1);

const trendTicks = (trend) => {
    const max = maxTrend(trend);
    if (max <= 1) return [1, 0];
    const mid = Math.ceil(max / 2);
    return [...new Set([max, mid, 0])].sort((a, b) => b - a);
};
</script>

<template>
    <div class="admin-page">
        <header class="admin-page__header">
            <h1 class="admin-page__title">Tableau de bord</h1>
            <p class="admin-page__subtitle">
                Vue d'ensemble de l'activité FoundIt
            </p>
        </header>

        <div class="admin-grid admin-grid--kpis">
            <KpiCard
                label="Objets total"
                :value="stats.items_total"
                icon="inventory_2"
                :href="route('dashboard')"
            />
            <KpiCard
                label="Disponibles"
                :value="stats.items_available"
                icon="check_circle"
            />
            <KpiCard
                label="En attente"
                :value="stats.claims_pending"
                icon="hand_gesture"
                :href="route('admin.claims.index', { status: 'pending' })"
                accent
                hint="Réclamations à traiter"
            />
            <KpiCard
                label="Restitués"
                :value="stats.items_returned"
                icon="task_alt"
            />
            <KpiCard
                label="Taux restitution"
                :value="`${stats.return_rate}%`"
                icon="trending_up"
            />
            <KpiCard
                label="Collaborateurs"
                :value="stats.users_total"
                icon="groups"
            />
        </div>

        <div class="admin-grid admin-grid--2 admin-grid--sections">
            <section class="admin-panel">
                <h2 class="admin-panel__title">
                    <i class="material-symbols-rounded">show_chart</i>
                    Déclarations (30 jours)
                </h2>
                <div class="trend-chart">
                    <div class="trend-chart__y-axis">
                        <span
                            v-for="tick in trendTicks(declarationsTrend)"
                            :key="tick"
                            >{{ tick }}</span
                        >
                    </div>
                    <div class="trend-chart__body">
                        <div class="trend-chart__grid">
                            <div
                                v-for="point in declarationsTrend"
                                :key="point.date"
                                class="trend-chart__col"
                                :title="`${point.label} : ${point.count} déclaration(s)`"
                            >
                                <span
                                    v-if="point.count > 0"
                                    class="trend-chart__value"
                                    >{{ point.count }}</span
                                >
                                <div
                                    class="trend-chart__bar"
                                    :class="{
                                        'trend-chart__bar--empty':
                                            point.count === 0,
                                    }"
                                    :style="{
                                        height: `${(point.count / maxTrend(declarationsTrend)) * 100}%`,
                                    }"
                                ></div>
                            </div>
                        </div>
                        <div class="trend-chart__labels">
                            <span
                                v-for="(point, index) in declarationsTrend"
                                :key="`label-${point.date}`"
                                class="trend-chart__label"
                            >
                                {{
                                    index % 5 === 0 || index === declarationsTrend.length - 1
                                        ? point.label
                                        : ""
                                }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="admin-panel">
                <h2 class="admin-panel__title">
                    <i class="material-symbols-rounded">category</i>
                    Par catégorie
                </h2>
                <ul class="bar-list">
                    <li
                        v-for="cat in itemsByCategory"
                        :key="cat.name"
                        class="bar-list__item"
                    >
                        <span class="bar-list__label">{{ cat.name }}</span>
                        <div class="bar-list__track">
                            <div
                                class="bar-list__fill"
                                :style="{
                                    width: `${(cat.count / maxCategory(itemsByCategory)) * 100}%`,
                                }"
                            ></div>
                        </div>
                        <span class="bar-list__count">{{ cat.count }}</span>
                    </li>
                </ul>
            </section>

            <section class="admin-panel">
                <h2 class="admin-panel__title">
                    <i class="material-symbols-rounded">location_on</i>
                    Par lieu
                </h2>
                <ul class="bar-list">
                    <li
                        v-for="loc in itemsByLocation"
                        :key="loc.name"
                        class="bar-list__item"
                    >
                        <span class="bar-list__label">{{ loc.name }}</span>
                        <div class="bar-list__track">
                            <div
                                class="bar-list__fill bar-list__fill--secondary"
                                :style="{
                                    width: `${(loc.count / maxLocation(itemsByLocation)) * 100}%`,
                                }"
                            ></div>
                        </div>
                        <span class="bar-list__count">{{ loc.count }}</span>
                    </li>
                </ul>
            </section>
        </div>

        <section class="admin-panel">
            <div class="admin-panel__head">
                    <h2 class="admin-panel__title">
                        <i class="material-symbols-rounded">schedule</i>
                        Réclamations en attente
                    </h2>
                    <Link
                        v-if="pendingClaims.length"
                        :href="
                            route('admin.claims.index', { status: 'pending' })
                        "
                        class="admin-panel__link"
                    >
                        Traiter →
                    </Link>
                </div>
                <EmptyState
                    v-if="!pendingClaims.length"
                    icon="check_circle"
                    title="Aucune réclamation en attente"
                    description="Toutes les réclamations sont traitées."
                />
                <table v-else class="data-table">
                    <thead>
                        <tr>
                            <th>Objet</th>
                            <th>Réclamant</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="claim in pendingClaims" :key="claim.id">
                            <td>{{ claim.item?.name }}</td>
                            <td>{{ claim.user?.name }}</td>
                            <td>
                                {{
                                    new Date(
                                        claim.created_at,
                                    ).toLocaleDateString("fr-FR")
                                }}
                            </td>
                            <td>
                                <Link
                                    :href="
                                        route('admin.claims.review', claim.id)
                                    "
                                    class="data-table__action"
                                >
                                    Traiter
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
        </section>

        <section class="admin-panel">
            <div class="admin-panel__head">
                <h2 class="admin-panel__title">
                    <i class="material-symbols-rounded">inventory</i>
                    Dernières déclarations
                </h2>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Objet</th>
                        <th>Catégorie</th>
                        <th>Lieu</th>
                        <th>Statut</th>
                        <th>Déclarant</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in recentItems" :key="item.id">
                        <td>{{ item.name }}</td>
                        <td>{{ item.category?.name }}</td>
                        <td>{{ item.location?.name }}</td>
                        <td><StatusBadge :status="item.status" /></td>
                        <td>{{ item.user?.name }}</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
</template>

<style lang="scss" scoped>
.trend-chart {
    display: grid;
    grid-template-columns: 28px 1fr;
    gap: 0.65rem;
    align-items: stretch;

    &__y-axis {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 148px;
        padding-bottom: 1.35rem;
        font-family: var(--font-body);
        font-size: 0.58rem;
        color: var(--color-text);
        opacity: 0.4;
        text-align: right;
    }

    &__body {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        min-width: 0;
    }

    &__grid {
        display: grid;
        grid-template-columns: repeat(30, minmax(0, 1fr));
        gap: 5px;
        height: 148px;
        align-items: end;
        padding: 0 0.15rem;
        border-bottom: 1.5px solid rgba(15, 43, 76, 0.08);
    }

    &__col {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        height: 100%;
        gap: 0.2rem;
        min-width: 0;
    }

    &__value {
        font-family: var(--font-display);
        font-size: 0.55rem;
        font-weight: 700;
        color: var(--color-main);
        line-height: 1;
    }

    &__bar {
        width: 100%;
        max-width: 12px;
        background: var(--color-main);
        border-radius: 4px 4px 0 0;
        opacity: 0.82;
        transition: height 0.3s ease;

        &--empty {
            height: 3px !important;
            opacity: 0.12;
            background: rgba(15, 43, 76, 0.25);
        }
    }

    &__labels {
        display: grid;
        grid-template-columns: repeat(30, minmax(0, 1fr));
        gap: 5px;
        padding: 0 0.15rem;
    }

    &__label {
        font-family: var(--font-body);
        font-size: 0.58rem;
        color: var(--color-text);
        opacity: 0.4;
        text-align: center;
        min-height: 0.85rem;
        white-space: nowrap;
    }

    @media (max-width: 600px) {
        &__grid,
        &__labels {
            gap: 3px;
        }

        &__bar {
            max-width: 8px;
        }

        &__label {
            font-size: 0.5rem;
        }
    }
}

.bar-list {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 0.55rem;

    &__item {
        display: grid;
        grid-template-columns: 120px 1fr 32px;
        align-items: center;
        gap: 0.5rem;
    }

    &__label {
        font-family: var(--font-body);
        font-size: 0.72rem;
        color: var(--color-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    &__track {
        height: 8px;
        background: rgba(15, 43, 76, 0.06);
        border-radius: 4px;
        overflow: hidden;
    }

    &__fill {
        height: 100%;
        background: var(--color-main);
        border-radius: 4px;

        &--secondary {
            background: var(--color-secondary);
        }
    }

    &__count {
        font-family: var(--font-display);
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--color-main);
        text-align: right;
    }
}

.data-table {
    width: 100%;
    border-collapse: collapse;

    th {
        font-family: var(--font-body);
        font-size: 0.68rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--color-main);
        text-align: left;
        padding: 0.5rem 0.75rem;
        border-bottom: 1.5px solid rgba(15, 43, 76, 0.08);
    }

    td {
        font-family: var(--font-body);
        font-size: 0.82rem;
        color: var(--color-text);
        padding: 0.65rem 0.75rem;
        border-bottom: 1px solid rgba(15, 43, 76, 0.05);
    }

    tbody tr:hover {
        background: rgba(15, 43, 76, 0.02);
    }

    &__action {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--color-secondary);
    }
}
</style>
