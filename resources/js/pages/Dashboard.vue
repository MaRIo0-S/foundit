<script setup>
import { computed, ref, onMounted, onBeforeUnmount } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import LandingPageLayout from "../layouts/LandingPageLayout.vue";
import MainLayout from "../layouts/MainLayout.vue";

const props = defineProps({
    items: Object,
    categories: Object,
    locations: Object,
    userClaims: { type: Array, default: () => [] },
});

defineOptions({
    layout: (h, page) => {
        return page.props.auth
            ? h(MainLayout, null, () => page)
            : h(LandingPageLayout, null, () => page);
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isAuthenticated = computed(() => !!user.value);
const isAdmin = computed(() => page.props.auth?.is_admin ?? false);

const showLoginAlert = ref(false);
const showClaimForm = ref(false);
const selectedItem = ref(null);
const claimFormRef = ref(null);
const loginAlertRef = ref(null);
const ignoreNextClick = ref(false);

const claimForm = useForm({ claim_notes: "", contact_phone: "" });

const search = ref(
    new URLSearchParams(window.location.search).get("search") ?? "",
);
const sortDate = ref(
    new URLSearchParams(window.location.search).get("sort_date") ?? "",
);
const statusFilter = ref(
    new URLSearchParams(window.location.search).get("status") ?? "",
);
const staleFilter = ref(
    new URLSearchParams(window.location.search).get("stale") === "1",
);

let searchTimeout = null;

const statusOptions = [
    { value: "", label: "Tous les statuts", icon: "filter_list" },
    { value: "available", label: "Disponible", icon: "check_circle" },
    { value: "claimed", label: "Réclamé", icon: "hand_gesture" },
    { value: "returned", label: "Restitué", icon: "task_alt" },
];

function applyFilters(page = 1) {
    const params = {};
    if (search.value) params.search = search.value;
    if (sortDate.value) params.sort_date = sortDate.value;
    if (statusFilter.value) params.status = statusFilter.value;
    if (staleFilter.value) params.stale = 1;
    if (page > 1) params.page = page;
    router.get(route("dashboard"), params, {
        preserveScroll: true,
        preserveState: true,
    });
}

function onSearchInput() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => applyFilters(1), 400);
}

function toggleSortDate() {
    if (sortDate.value === "") sortDate.value = "asc";
    else if (sortDate.value === "asc") sortDate.value = "desc";
    else sortDate.value = "";
    applyFilters(1);
}

function setStatus(value) {
    statusFilter.value = value;
    applyFilters(1);
}

function goToPage(p) {
    applyFilters(p);
}

const currentPage = computed(() => props.items?.current_page ?? 1);
const lastPage = computed(() => props.items?.last_page ?? 1);

const visiblePages = computed(() => {
    const c = currentPage.value;
    const l = lastPage.value;
    const pages = [];
    for (let i = c - 2; i <= c + 2; i++) {
        if (i >= 1 && i <= l) pages.push(i);
    }
    return pages;
});

function claimItem(item) {
    selectedItem.value = item;
    ignoreNextClick.value = true;
    if (!isAuthenticated.value) {
        showLoginAlert.value = true;
        showClaimForm.value = false;
    } else {
        showLoginAlert.value = false;
        showClaimForm.value = true;
    }
    setTimeout(() => {
        ignoreNextClick.value = false;
    }, 0);
}

function closeAll() {
    showLoginAlert.value = false;
    showClaimForm.value = false;
    claimForm.reset();
}

function submitClaim() {
    claimForm.post(route("claims.store", { item: selectedItem.value.id }), {
        onSuccess: () => closeAll(),
    });
}

function handleClickOutside(event) {
    if (ignoreNextClick.value) return;
    const insideClaim = claimFormRef.value?.contains(event.target);
    const insideLogin = loginAlertRef.value?.contains(event.target);
    if (!insideClaim && !insideLogin) closeAll();
}

onMounted(() => document.addEventListener("click", handleClickOutside));
onBeforeUnmount(() =>
    document.removeEventListener("click", handleClickOutside),
);

const statusMap = {
    available: { label: "Disponible", color: "status--available" },
    claimed: { label: "Réclamé", color: "status--claimed" },
    returned: { label: "Restitué", color: "status--returned" },
};

function getStatus(status) {
    return statusMap[status] ?? { label: status, color: "" };
}

function getUserClaim(itemId) {
    return props.userClaims.find((c) => c.item_id === itemId);
}

function canClaimItem(item) {
    if (item.status !== "available") return false;
    if (user.value && item.user_id === user.value.id) return false;
    const claim = getUserClaim(item.id);
    if (!claim) return true;
    return claim.status === "rejected";
}

function claimButtonLabel(item) {
    const claim = getUserClaim(item.id);
    if (user.value && item.user_id === user.value.id) {
        return "Votre déclaration";
    }
    if (item.status === "returned") return "Restitué";
    if (claim?.status === "pending") return "En attente";
    if (claim?.status === "approved") return "Approuvée";
    if (claim?.status === "rejected") return "Réclamer à nouveau";
    if (item.status === "claimed") return "Réclamé";
    return "Réclamer";
}
</script>

<template>
    <div class="dashboard">
        <div class="dashboard__inner container">
            <aside class="dashboard__sidebar">
                <div class="sidebar-section">
                    <span class="sidebar-section__title">
                        <i class="material-symbols-rounded">category</i>
                        Catégories
                    </span>
                    <ul class="sidebar-list">
                        <li
                            class="sidebar-list__item"
                            v-for="category in categories"
                            :key="category.id"
                        >
                            <a
                                :href="route('categories.show', category.id)"
                                class="sidebar-list__link"
                            >
                                <i class="material-symbols-rounded">label</i>
                                {{ category.name }}
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="sidebar-section">
                    <span class="sidebar-section__title">
                        <i class="material-symbols-rounded">location_on</i>
                        Lieux
                    </span>
                    <ul class="sidebar-list">
                        <li
                            class="sidebar-list__item"
                            v-for="location in locations"
                            :key="location.id"
                        >
                            <a
                                :href="route('locations.show', location.id)"
                                class="sidebar-list__link"
                            >
                                <i class="material-symbols-rounded">place</i>
                                {{ location.name }}
                            </a>
                        </li>
                    </ul>
                </div>
            </aside>

            <main class="dashboard__main">
                <div class="dashboard__header">
                    <div class="dashboard__header-left">
                        <h2 class="dashboard__title">Objets trouvés</h2>
                        <span class="dashboard__count">
                            {{ items.total ?? 0 }} objet{{
                                (items.total ?? 0) !== 1 ? "s" : ""
                            }}
                        </span>
                    </div>
                    <button
                        v-if="isAuthenticated && !isAdmin"
                        class="dashboard__declare-btn"
                        type="button"
                        @click="router.visit(route('item.declare'))"
                    >
                        <i class="material-symbols-rounded">add_circle</i>
                        Déclarer un objet
                    </button>
                </div>

                <div v-if="staleFilter" class="dashboard__stale-banner">
                    <i class="material-symbols-rounded">warning</i>
                    Affichage des objets disponibles non restitués depuis plus
                    de 30 jours.
                </div>

                <div class="filters">
                    <div class="filters__search-wrap">
                        <i class="material-symbols-rounded filters__search-icon"
                            >search</i
                        >
                        <input
                            class="filters__search"
                            type="text"
                            v-model="search"
                            @input="onSearchInput"
                            placeholder="Rechercher par nom ou description..."
                        />
                        <button
                            v-if="search"
                            class="filters__search-clear"
                            type="button"
                            @click="
                                search = '';
                                applyFilters(1);
                            "
                        >
                            <i class="material-symbols-rounded">close</i>
                        </button>
                    </div>

                    <div class="filters__row">
                        <div class="filters__status">
                            <button
                                v-for="opt in statusOptions"
                                :key="opt.value"
                                class="filters__status-btn"
                                :class="{
                                    'is-active': statusFilter === opt.value,
                                }"
                                type="button"
                                @click="setStatus(opt.value)"
                            >
                                <i class="material-symbols-rounded">{{
                                    opt.icon
                                }}</i>
                                {{ opt.label }}
                            </button>
                        </div>

                        <button
                            class="filters__sort-btn"
                            :class="{ 'is-active': sortDate !== '' }"
                            type="button"
                            @click="toggleSortDate"
                        >
                            <i class="material-symbols-rounded">
                                {{
                                    sortDate === "asc"
                                        ? "arrow_upward"
                                        : sortDate === "desc"
                                          ? "arrow_downward"
                                          : "swap_vert"
                                }}
                            </i>
                            Date
                            <span
                                v-if="sortDate === 'asc'"
                                class="filters__sort-label"
                                >anciens</span
                            >
                            <span
                                v-else-if="sortDate === 'desc'"
                                class="filters__sort-label"
                                >récents</span
                            >
                        </button>
                    </div>
                </div>

                <div v-if="items.data?.length > 0" class="items-grid">
                    <div
                        class="item-card"
                        v-for="item in items.data"
                        :key="item.id"
                    >
                        <div class="item-card__image-wrap">
                            <img
                                :src="`/storage/items/${item.image_path}`"
                                :alt="item.name"
                                class="item-card__image"
                            />
                            <span
                                class="item-card__status"
                                :class="getStatus(item.status).color"
                            >
                                {{ getStatus(item.status).label }}
                            </span>
                        </div>

                        <div class="item-card__body">
                            <h3 class="item-card__name">{{ item.name }}</h3>
                            <p class="item-card__description">
                                {{ item.description }}
                            </p>

                            <div class="item-card__meta">
                                <span class="item-card__meta-item">
                                    <i class="material-symbols-rounded"
                                        >label</i
                                    >
                                    {{ item.category.name }}
                                </span>
                                <span class="item-card__meta-item">
                                    <i class="material-symbols-rounded"
                                        >place</i
                                    >
                                    {{ item.location.name }}
                                </span>
                                <span class="item-card__meta-item">
                                    <i class="material-symbols-rounded"
                                        >calendar_today</i
                                    >
                                    {{ item.found_date }}
                                </span>
                            </div>
                        </div>

                        <div class="item-card__footer" v-if="!isAdmin">
                            <button
                                class="item-card__claim-btn"
                                :class="{
                                    'item-card__claim-btn--disabled':
                                        !canClaimItem(item),
                                }"
                                :disabled="!canClaimItem(item)"
                                @click="claimItem(item)"
                            >
                                <i class="material-symbols-rounded"
                                    >hand_gesture</i
                                >
                                {{ claimButtonLabel(item) }}
                            </button>
                        </div>
                    </div>
                </div>

                <div v-else class="empty-state">
                    <span class="empty-state__icon">
                        <i class="material-symbols-rounded">search_off</i>
                    </span>
                    <p class="empty-state__text">
                        Aucun objet ne correspond à votre recherche.
                    </p>
                </div>

                <div v-if="lastPage > 1" class="pagination">
                    <button
                        class="pagination__btn pagination__btn--edge"
                        :disabled="currentPage === 1"
                        @click="goToPage(1)"
                        title="Première page"
                    >
                        <i class="material-symbols-rounded">first_page</i>
                    </button>

                    <button
                        class="pagination__btn pagination__btn--edge"
                        :disabled="currentPage === 1"
                        @click="goToPage(currentPage - 1)"
                        title="Page précédente"
                    >
                        <i class="material-symbols-rounded">chevron_left</i>
                    </button>

                    <div class="pagination__pages">
                        <button
                            v-for="p in visiblePages"
                            :key="p"
                            class="pagination__btn pagination__btn--page"
                            :class="{ 'is-active': p === currentPage }"
                            @click="goToPage(p)"
                        >
                            {{ p }}
                        </button>
                    </div>

                    <button
                        class="pagination__btn pagination__btn--edge"
                        :disabled="currentPage === lastPage"
                        @click="goToPage(currentPage + 1)"
                        title="Page suivante"
                    >
                        <i class="material-symbols-rounded">chevron_right</i>
                    </button>

                    <button
                        class="pagination__btn pagination__btn--edge"
                        :disabled="currentPage === lastPage"
                        @click="goToPage(lastPage)"
                        title="Dernière page"
                    >
                        <i class="material-symbols-rounded">last_page</i>
                    </button>

                    <span class="pagination__info">
                        Page {{ currentPage }} sur {{ lastPage }}
                    </span>
                </div>
            </main>
        </div>

        <Teleport to="body">
            <div
                v-if="showClaimForm || showLoginAlert"
                class="overlay"
                @click="closeAll"
            ></div>

            <div
                v-if="showClaimForm"
                ref="claimFormRef"
                class="modal"
                @click.stop
            >
                <div class="modal__header">
                    <span class="modal__icon">
                        <i class="material-symbols-rounded">hand_gesture</i>
                    </span>
                    <div>
                        <h3 class="modal__title">Réclamation</h3>
                        <p class="modal__subtitle">{{ selectedItem?.name }}</p>
                    </div>
                    <button class="modal__close" @click="closeAll">
                        <i class="material-symbols-rounded">close</i>
                    </button>
                </div>

                <div class="modal__body">
                    <div class="form-group">
                        <label class="form-group__label">
                            Décrivez votre réclamation
                        </label>
                        <p class="form-notice">
                            <i class="material-symbols-rounded">gpp_maybe</i>
                            <span
                                ><strong>Description obligatoire et
                                détaillée.</strong> Mentionnez les éléments
                                distinctifs, la marque, les gravures et les
                                circonstances. Une description vague entraîne un
                                rejet.</span
                            >
                        </p>
                        <textarea
                            class="form-group__textarea"
                            v-model="claimForm.claim_notes"
                            placeholder="Expliquez pourquoi cet objet vous appartient..."
                            rows="4"
                        ></textarea>
                        <p
                            v-if="claimForm.errors.claim_notes"
                            class="form-group__error"
                        >
                            <i class="material-symbols-rounded">error</i>
                            {{ claimForm.errors.claim_notes }}
                        </p>
                    </div>
                    <div class="form-group">
                        <label class="form-group__label">
                            Numéro de téléphone
                        </label>
                        <input
                            class="form-group__input"
                            type="tel"
                            v-model="claimForm.contact_phone"
                            placeholder="06 12 34 56 78"
                            autocomplete="tel"
                        />
                        <p class="form-notice">
                            <i class="material-symbols-rounded">lock</i>
                            <span
                                ><strong>Numéro confidentiel.</strong> Communiqué
                                au déclarant uniquement après approbation par
                                l'administration.</span
                            >
                        </p>
                        <p
                            v-if="claimForm.errors.contact_phone"
                            class="form-group__error"
                        >
                            <i class="material-symbols-rounded">error</i>
                            {{ claimForm.errors.contact_phone }}
                        </p>
                    </div>
                </div>

                <div class="modal__footer">
                    <button
                        class="modal__btn modal__btn--secondary"
                        @click="closeAll"
                    >
                        Annuler
                    </button>
                    <button
                        class="modal__btn modal__btn--primary"
                        @click="submitClaim"
                        :disabled="claimForm.processing"
                    >
                        <span v-if="!claimForm.processing">Soumettre</span>
                        <span v-else class="btn-loading">
                            <i class="material-symbols-rounded">autorenew</i>
                            Envoi...
                        </span>
                    </button>
                </div>
            </div>

            <div
                v-if="showLoginAlert"
                ref="loginAlertRef"
                class="modal"
                @click.stop
            >
                <div class="modal__header">
                    <span class="modal__icon modal__icon--warn">
                        <i class="material-symbols-rounded">lock</i>
                    </span>
                    <div>
                        <h3 class="modal__title">Connexion requise</h3>
                        <p class="modal__subtitle">
                            Vous devez être connecté pour réclamer un objet.
                        </p>
                    </div>
                    <button class="modal__close" @click="closeAll">
                        <i class="material-symbols-rounded">close</i>
                    </button>
                </div>

                <div class="modal__footer">
                    <button
                        class="modal__btn modal__btn--secondary"
                        @click="closeAll"
                    >
                        Annuler
                    </button>
                    <button
                        class="modal__btn modal__btn--primary"
                        @click="router.visit(route('auth.login'))"
                    >
                        <i class="material-symbols-rounded">login</i>
                        Se connecter
                    </button>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<style lang="scss" scoped>
.dashboard {
    flex: 1;
    padding: 2rem 1.5rem;

    &__inner {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        gap: 2rem;

        @media (max-width: 900px) {
            flex-direction: column;
        }
    }

    &__header {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        gap: 1rem;
        flex-wrap: wrap;
    }

    &__header-left {
        display: flex;
        flex-direction: row;
        align-items: baseline;
        gap: 0.75rem;
    }

    &__title {
        font-family: var(--font-display);
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--color-main);
    }

    &__count {
        font-family: var(--font-body);
        font-size: 0.8rem;
        color: var(--color-text);
        opacity: 0.45;
    }

    &__stale-banner {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 0.9rem;
        margin-bottom: 1rem;
        border-radius: var(--radius-sm);
        background: rgba(232, 65, 10, 0.08);
        border: 1px solid rgba(232, 65, 10, 0.2);
        font-family: var(--font-body);
        font-size: 0.82rem;
        color: var(--color-main);

        i {
            font-size: 1.1rem;
            color: var(--color-secondary);
        }
    }

    &__declare-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        height: 36px;
        padding: 0 1rem;
        font-family: var(--font-display);
        font-size: 0.82rem;
        font-weight: 700;
        color: #fff;
        background-color: var(--color-secondary);
        border: 1.5px solid var(--color-secondary);
        border-radius: var(--radius-sm);
        cursor: pointer;
        white-space: nowrap;
        transition:
            background-color var(--transition),
            border-color var(--transition);

        i {
            font-size: 1.05rem;
        }

        &:hover {
            background-color: var(--color-main);
            border-color: var(--color-main);
        }

        &:active {
            transform: scale(0.98);
        }
    }

    &__sidebar {
        width: 220px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;

        @media (max-width: 900px) {
            width: 100%;
            flex-direction: row;
            flex-wrap: wrap;
        }
    }

    &__main {
        flex: 1;
        min-width: 0;
    }
}

.filters {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-bottom: 1.5rem;

    &__search-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    &__search-icon {
        position: absolute;
        left: 0.75rem;
        font-size: 1.05rem;
        color: var(--color-main);
        opacity: 0.35;
        pointer-events: none;
    }

    &__search {
        width: 100%;
        height: 40px;
        padding: 0 2.5rem 0 2.4rem;
        font-family: var(--font-body);
        font-size: 0.84rem;
        color: var(--color-text);
        background-color: #fff;
        border: 1.5px solid rgba(15, 43, 76, 0.12);
        border-radius: var(--radius-sm);
        outline: none;
        transition:
            border-color var(--transition),
            box-shadow var(--transition);

        &::placeholder {
            color: var(--color-text);
            opacity: 0.28;
        }

        &:focus {
            border-color: var(--color-main);
            box-shadow: 0 0 0 3px rgba(15, 43, 76, 0.07);
        }
    }

    &__search-clear {
        position: absolute;
        right: 0.6rem;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        background: rgba(15, 43, 76, 0.07);
        border: none;
        border-radius: 50%;
        cursor: pointer;
        color: var(--color-main);
        opacity: 0.5;
        transition: opacity var(--transition);

        i {
            font-size: 0.85rem;
        }

        &:hover {
            opacity: 1;
        }
    }

    &__row {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 0.6rem;
        flex-wrap: wrap;
    }

    &__status {
        display: flex;
        flex-direction: row;
        gap: 0.35rem;
        flex-wrap: wrap;
        flex: 1;
    }

    &__status-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        height: 32px;
        padding: 0 0.75rem;
        font-family: var(--font-body);
        font-size: 0.76rem;
        font-weight: 500;
        color: var(--color-text);
        background-color: #fff;
        border: 1.5px solid rgba(15, 43, 76, 0.12);
        border-radius: var(--radius-lg);
        cursor: pointer;
        white-space: nowrap;
        transition:
            background-color var(--transition),
            border-color var(--transition),
            color var(--transition);

        i {
            font-size: 0.85rem;
            opacity: 0.5;
        }

        &:hover:not(.is-active) {
            border-color: rgba(15, 43, 76, 0.25);
            background-color: rgba(15, 43, 76, 0.03);
        }

        &.is-active {
            background-color: var(--color-main);
            border-color: var(--color-main);
            color: #fff;

            i {
                opacity: 1;
            }
        }
    }

    &__sort-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        height: 32px;
        padding: 0 0.85rem;
        font-family: var(--font-body);
        font-size: 0.76rem;
        font-weight: 500;
        color: var(--color-text);
        background-color: #fff;
        border: 1.5px solid rgba(15, 43, 76, 0.12);
        border-radius: var(--radius-lg);
        cursor: pointer;
        white-space: nowrap;
        flex-shrink: 0;
        transition:
            background-color var(--transition),
            border-color var(--transition),
            color var(--transition);

        i {
            font-size: 0.9rem;
            opacity: 0.5;
        }

        &:hover:not(.is-active) {
            border-color: rgba(15, 43, 76, 0.25);
            background-color: rgba(15, 43, 76, 0.03);
        }

        &.is-active {
            background-color: var(--color-main);
            border-color: var(--color-main);
            color: #fff;

            i {
                opacity: 1;
            }
        }
    }

    &__sort-label {
        font-size: 0.7rem;
        opacity: 0.75;
    }
}

.sidebar-section {
    background-color: #fff;
    border-radius: var(--radius-md);
    padding: 1rem;
    border: 1.5px solid rgba(15, 43, 76, 0.08);

    @media (max-width: 900px) {
        flex: 1;
        min-width: 180px;
    }

    &__title {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-family: var(--font-display);
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--color-main);
        letter-spacing: 0.04em;
        text-transform: uppercase;
        margin-bottom: 0.75rem;

        i {
            font-size: 1rem;
            color: var(--color-secondary);
        }
    }
}

.sidebar-list {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 0.1rem;

    &__item {
        border-radius: var(--radius-sm);
        overflow: hidden;
    }

    &__link {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.42rem 0.6rem;
        font-family: var(--font-body);
        font-size: 0.82rem;
        color: var(--color-text);
        opacity: 0.7;
        border-radius: var(--radius-sm);
        transition:
            background-color var(--transition),
            opacity var(--transition);

        i {
            font-size: 0.9rem;
            opacity: 0.5;
        }

        &:hover {
            background-color: rgba(15, 43, 76, 0.05);
            opacity: 1;
        }
    }
}

.items-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 1.25rem;
}

.item-card {
    background-color: #fff;
    border-radius: var(--radius-md);
    border: 1.5px solid rgba(15, 43, 76, 0.08);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition:
        box-shadow var(--transition),
        border-color var(--transition);

    &:hover {
        border-color: rgba(15, 43, 76, 0.18);
        box-shadow: 0 4px 18px rgba(15, 43, 76, 0.07);
    }

    &__image-wrap {
        position: relative;
        width: 100%;
        aspect-ratio: 4 / 3;
        background-color: rgba(15, 43, 76, 0.04);
        overflow: hidden;
    }

    &__image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    &__status {
        position: absolute;
        top: 0.6rem;
        right: 0.6rem;
        font-family: var(--font-body);
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.2rem 0.6rem;
        border-radius: var(--radius-lg);
        letter-spacing: 0.02em;
    }

    &__body {
        padding: 1rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    &__name {
        font-family: var(--font-display);
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--color-main);
        line-height: 1.3;
    }

    &__description {
        font-family: var(--font-body);
        font-size: 0.8rem;
        color: var(--color-text);
        opacity: 0.55;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    &__meta {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        margin-top: auto;
        padding-top: 0.5rem;
    }

    &__meta-item {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-family: var(--font-body);
        font-size: 0.75rem;
        color: var(--color-text);
        opacity: 0.5;

        i {
            font-size: 0.85rem;
        }
    }

    &__footer {
        padding: 0.75rem 1rem;
        border-top: 1.5px solid rgba(15, 43, 76, 0.06);
    }

    &__claim-btn {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        padding: 0.58rem 1rem;
        font-family: var(--font-display);
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--color-secondary);
        background-color: transparent;
        border: 1.5px solid var(--color-secondary);
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition:
            background-color var(--transition),
            color var(--transition),
            opacity var(--transition),
            border-color var(--transition);

        i {
            font-size: 1rem;
        }

        &:hover:not(:disabled) {
            background-color: var(--color-secondary);
            color: #fff;
        }

        &:active:not(:disabled) {
            transform: scale(0.98);
        }

        &--disabled,
        &:disabled {
            color: var(--color-main);
            border-color: rgba(15, 43, 76, 0.2);
            opacity: 0.4;
            cursor: not-allowed;
        }
    }
}

.status--available {
    background-color: rgba(39, 174, 96, 0.12);
    color: #1a6b3c;
}

.status--claimed {
    background-color: rgba(232, 65, 10, 0.1);
    color: #a02d07;
}

.status--returned {
    background-color: rgba(15, 43, 76, 0.08);
    color: var(--color-main);
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 4rem 2rem;
    background-color: #fff;
    border-radius: var(--radius-md);
    border: 1.5px solid rgba(15, 43, 76, 0.08);

    &__icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 52px;
        background-color: rgba(15, 43, 76, 0.05);
        border-radius: var(--radius-md);

        i {
            font-size: 1.5rem;
            color: var(--color-main);
            opacity: 0.3;
        }
    }

    &__text {
        font-family: var(--font-body);
        font-size: 0.88rem;
        color: var(--color-text);
        opacity: 0.45;
    }
}

.pagination {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    margin-top: 2rem;
    flex-wrap: wrap;

    &__pages {
        display: flex;
        flex-direction: row;
        gap: 0.35rem;
    }

    &__btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-family: var(--font-display);
        font-size: 0.82rem;
        font-weight: 700;
        background-color: #fff;
        border: 1.5px solid rgba(15, 43, 76, 0.12);
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition:
            background-color var(--transition),
            border-color var(--transition),
            color var(--transition);

        &--edge {
            width: 34px;
            height: 34px;
            color: var(--color-main);
            opacity: 0.5;

            i {
                font-size: 1.1rem;
            }

            &:hover:not(:disabled) {
                opacity: 1;
                border-color: rgba(15, 43, 76, 0.3);
                background-color: rgba(15, 43, 76, 0.04);
            }

            &:disabled {
                opacity: 0.2;
                cursor: not-allowed;
            }
        }

        &--page {
            min-width: 34px;
            height: 34px;
            padding: 0 0.5rem;
            color: var(--color-main);

            &:hover:not(.is-active) {
                border-color: rgba(15, 43, 76, 0.25);
                background-color: rgba(15, 43, 76, 0.04);
            }

            &.is-active {
                background-color: var(--color-main);
                border-color: var(--color-main);
                color: #fff;
                cursor: default;
            }
        }
    }

    &__info {
        font-family: var(--font-body);
        font-size: 0.75rem;
        color: var(--color-text);
        opacity: 0.4;
        margin-left: 0.5rem;
    }
}

.overlay {
    position: fixed;
    inset: 0;
    background-color: rgba(15, 43, 76, 0.35);
    z-index: 100;
    backdrop-filter: blur(2px);
}

.modal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 101;
    width: 100%;
    max-width: 440px;
    background-color: #fff;
    border-radius: var(--radius-lg);
    box-shadow: 0 8px 40px rgba(15, 43, 76, 0.16);
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;

    @media (max-width: 480px) {
        max-width: calc(100vw - 2rem);
        padding: 1.25rem;
    }

    &__header {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        gap: 0.85rem;
    }

    &__icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        flex-shrink: 0;
        background-color: rgba(232, 65, 10, 0.1);
        border-radius: var(--radius-sm);

        i {
            font-size: 1.2rem;
            color: var(--color-secondary);
        }

        &--warn {
            background-color: rgba(15, 43, 76, 0.07);

            i {
                color: var(--color-main);
            }
        }
    }

    &__title {
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 800;
        color: var(--color-main);
        line-height: 1.2;
    }

    &__subtitle {
        font-family: var(--font-body);
        font-size: 0.78rem;
        color: var(--color-text);
        opacity: 0.5;
        margin-top: 0.1rem;
    }

    &__close {
        margin-left: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        flex-shrink: 0;
        background: transparent;
        border: 1.5px solid rgba(15, 43, 76, 0.12);
        border-radius: var(--radius-sm);
        cursor: pointer;
        color: var(--color-main);
        opacity: 0.4;
        transition:
            opacity var(--transition),
            border-color var(--transition);

        i {
            font-size: 1rem;
        }

        &:hover {
            opacity: 1;
            border-color: rgba(15, 43, 76, 0.3);
        }
    }

    &__body {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    &__footer {
        display: flex;
        flex-direction: row;
        gap: 0.75rem;
        justify-content: flex-end;
    }

    &__btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        padding: 0.6rem 1.2rem;
        font-family: var(--font-display);
        font-size: 0.84rem;
        font-weight: 700;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition:
            background-color var(--transition),
            color var(--transition),
            opacity var(--transition);

        i {
            font-size: 1rem;
        }

        &--secondary {
            background-color: transparent;
            border: 1.5px solid rgba(15, 43, 76, 0.18);
            color: var(--color-text);

            &:hover {
                background-color: rgba(15, 43, 76, 0.05);
            }
        }

        &--primary {
            background-color: var(--color-secondary);
            border: 1.5px solid var(--color-secondary);
            color: #fff;

            &:hover:not(:disabled) {
                background-color: var(--color-main);
                border-color: var(--color-main);
            }

            &:disabled {
                opacity: 0.55;
                cursor: not-allowed;
            }
        }
    }
}

.btn-loading {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;

    i {
        font-size: 1rem;
        animation: spin 0.9s linear infinite;
    }
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.22rem;

    &__label {
        font-family: var(--font-body);
        font-size: 0.74rem;
        font-weight: 500;
        color: var(--color-main);
        letter-spacing: 0.01em;
    }

    &__textarea {
        width: 100%;
        border: 1.5px solid rgba(15, 43, 76, 0.15);
        border-radius: var(--radius-sm);
        background-color: var(--color-third);
        padding: 0.6rem 0.75rem;
        font-family: var(--font-body);
        font-size: 0.84rem;
        color: var(--color-text);
        resize: vertical;
        outline: none;
        transition:
            border-color var(--transition),
            box-shadow var(--transition);

        &::placeholder {
            color: var(--color-text);
            opacity: 0.28;
        }

        &:focus {
            border-color: var(--color-main);
            box-shadow: 0 0 0 3px rgba(15, 43, 76, 0.07);
        }
    }

    &__input {
        width: 100%;
        height: 38px;
        border: 1.5px solid rgba(15, 43, 76, 0.15);
        border-radius: var(--radius-sm);
        background-color: var(--color-third);
        padding: 0 0.75rem;
        font-family: var(--font-body);
        font-size: 0.84rem;
        color: var(--color-text);
        outline: none;
    }

    &__hint {
        font-family: var(--font-body);
        font-size: 0.68rem;
        color: var(--color-text);
        opacity: 0.45;
        margin: 0;
        line-height: 1.4;
    }

    &__error {
        display: inline-flex;
        align-items: center;
        gap: 0.22rem;
        font-family: var(--font-body);
        font-size: 0.7rem;
        color: #c0392b;
        font-weight: 500;

        i {
            font-size: 0.82rem;
        }
    }
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>
