<script setup>
import { router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { ref, onMounted, onBeforeUnmount } from "vue";
import MainLayout from "../../layouts/MainLayout.vue";

defineOptions({
    layout: MainLayout,
});

defineProps({
    declarations: Object,
});

const statusMap = {
    available: { label: "Disponible", color: "status--available" },
    claimed: { label: "Réclamé", color: "status--claimed" },
    returned: { label: "Restitué", color: "status--returned" },
};

function getStatus(status) {
    return statusMap[status] ?? { label: status, color: "" };
}

function formatDate(dateStr) {
    if (!dateStr) return "—";
    return new Date(dateStr).toLocaleDateString("fr-FR", {
        day: "numeric",
        month: "long",
        year: "numeric",
    });
}

const confirmDeleteId = ref(null);
const ignoreNextClick = ref(false);
const confirmCardRef = ref(null);

function askDelete(id) {
    confirmDeleteId.value = id;
    ignoreNextClick.value = true;
    setTimeout(() => {
        ignoreNextClick.value = false;
    }, 0);
}

function cancelDelete() {
    confirmDeleteId.value = null;
}

function confirmDelete() {
    router.delete(route("items.destroy", { item: confirmDeleteId.value }), {
        preserveScroll: true,
        onSuccess: () => {
            confirmDeleteId.value = null;
        },
    });
}

function isLocked(declaration) {
    return (
        declaration.status === "returned" ||
        declaration.status === "claimed" ||
        !!declaration.claimant_contact_phone
    );
}

function handleClickOutside(event) {
    if (ignoreNextClick.value) return;
    if (confirmCardRef.value && !confirmCardRef.value.contains(event.target)) {
        confirmDeleteId.value = null;
    }
}

onMounted(() => document.addEventListener("click", handleClickOutside));
onBeforeUnmount(() =>
    document.removeEventListener("click", handleClickOutside),
);
</script>

<template>
    <div class="declarations-page">
        <div class="declarations-page__inner container">
            <div class="declarations-page__topbar">
                <button
                    class="back-btn"
                    type="button"
                    @click="router.visit(route('dashboard'))"
                >
                    <i class="material-symbols-rounded">arrow_back</i>
                    Retour
                </button>
            </div>

            <div class="declarations-page__header">
                <span class="declarations-page__icon">
                    <i class="material-symbols-rounded">inventory_2</i>
                </span>
                <div>
                    <h1 class="declarations-page__title">Mes déclarations</h1>
                    <p class="declarations-page__subtitle">
                        {{ declarations?.length ?? 0 }} déclaration{{
                            (declarations?.length ?? 0) !== 1 ? "s" : ""
                        }}
                    </p>
                </div>
            </div>

            <div v-if="declarations?.length > 0" class="declarations-list">
                <div
                    class="declaration-card"
                    v-for="(declaration, idx) in declarations"
                    :key="declaration.id"
                >
                    <div class="declaration-card__header">
                        <div class="declaration-card__header-left">
                            <span class="declaration-card__index"
                                >#{{ idx + 1 }}</span
                            >
                            <span class="declaration-card__date">
                                <i class="material-symbols-rounded">schedule</i>
                                Déclaré le
                                {{ formatDate(declaration.created_at) }}
                            </span>
                        </div>
                        <span
                            class="declaration-card__status"
                            :class="getStatus(declaration.status).color"
                        >
                            {{ getStatus(declaration.status).label }}
                        </span>
                    </div>

                    <div
                        v-if="declaration.claimant_contact_phone"
                        class="resolved-banner"
                    >
                        <span class="resolved-banner__icon">
                            <i class="material-symbols-rounded">verified</i>
                        </span>
                        <div class="resolved-banner__content">
                            <span class="resolved-banner__title"
                                >Objet restitué — processus terminé</span
                            >
                            <p class="resolved-banner__text">
                                Une réclamation a été approuvée par
                                l'administration. Contactez le réclamant pour
                                finaliser la remise si ce n'est pas déjà fait.
                            </p>
                            <div class="resolved-banner__contact">
                                <i class="material-symbols-rounded">call</i>
                                <span
                                    >{{ declaration.claimant_name ?? "Réclamant" }} :
                                    <a
                                        :href="`tel:${declaration.claimant_contact_phone}`"
                                        >{{ declaration.claimant_contact_phone }}</a
                                    ></span
                                >
                            </div>
                            <span class="resolved-banner__sealed">
                                <i class="material-symbols-rounded">lock</i>
                                Déclaration verrouillée — modification
                                impossible
                            </span>
                        </div>
                    </div>

                    <div v-if="declaration" class="declaration-card__body">
                        <div
                            class="declaration-card__image-wrap"
                            v-if="declaration.image_path"
                        >
                            <img
                                :alt="declaration.name"
                                :src="`/storage/items/${declaration.image_path}`"
                                class="declaration-card__image"
                            />
                        </div>

                        <div class="declaration-card__info">
                            <h3 class="declaration-card__name">
                                {{ declaration.name }}
                            </h3>
                            <p class="declaration-card__description">
                                {{ declaration.description }}
                            </p>

                            <div class="declaration-card__meta">
                                <span class="declaration-card__meta-item">
                                    <i class="material-symbols-rounded"
                                        >label</i
                                    >
                                    {{ declaration.category?.name }}
                                </span>
                                <span class="declaration-card__meta-item">
                                    <i class="material-symbols-rounded"
                                        >place</i
                                    >
                                    {{ declaration.location?.name }}
                                </span>
                                <span class="declaration-card__meta-item">
                                    <i class="material-symbols-rounded"
                                        >calendar_today</i
                                    >
                                    Trouvé le
                                    {{ formatDate(declaration.found_date) }}
                                </span>
                                <span class="declaration-card__meta-item">
                                    <i class="material-symbols-rounded"
                                        >add_circle</i
                                    >
                                    Déclaré le
                                    {{ formatDate(declaration.created_at) }}
                                </span>
                            </div>
                        </div>

                        <div
                            v-if="!isLocked(declaration)"
                            class="declaration-card__actions"
                        >
                            <button
                                class="declaration-card__action-btn declaration-card__action-btn--edit"
                                type="button"
                                @click="
                                    router.visit(
                                        route('declaration.modify', {
                                            item: declaration.id,
                                        }),
                                    )
                                "
                            >
                                <i class="material-symbols-rounded">edit</i>
                                Modifier
                            </button>
                            <button
                                class="declaration-card__action-btn declaration-card__action-btn--delete"
                                type="button"
                                @click="askDelete(declaration.id)"
                            >
                                <i class="material-symbols-rounded">delete</i>
                                Supprimer
                            </button>
                        </div>
                    </div>

                    <div v-else class="declaration-card__missing">
                        <i class="material-symbols-rounded">error_outline</i>
                        Objet introuvable ou supprimé.
                    </div>
                </div>
            </div>

            <div v-else class="empty-state">
                <span class="empty-state__icon">
                    <i class="material-symbols-rounded">inbox</i>
                </span>
                <p class="empty-state__text">
                    Vous n'avez effectué aucune déclaration pour le moment.
                </p>
                <button
                    class="empty-state__btn"
                    type="button"
                    @click="router.visit(route('item.declare'))"
                >
                    Déclarer un objet trouvé
                </button>
            </div>
        </div>

        <Teleport to="body">
            <div
                v-if="confirmDeleteId !== null"
                class="overlay"
                @click="cancelDelete"
            ></div>

            <div
                v-if="confirmDeleteId !== null"
                ref="confirmCardRef"
                class="modal"
                @click.stop
            >
                <div class="modal__header">
                    <span class="modal__icon modal__icon--danger">
                        <i class="material-symbols-rounded">delete</i>
                    </span>
                    <div>
                        <h3 class="modal__title">Supprimer la déclaration</h3>
                        <p class="modal__subtitle">
                            Cette action est irréversible.
                        </p>
                    </div>
                    <button
                        class="modal__close"
                        type="button"
                        @click="cancelDelete"
                    >
                        <i class="material-symbols-rounded">close</i>
                    </button>
                </div>

                <div class="modal__body">
                    <p class="modal__text">
                        Êtes-vous sûr de vouloir supprimer cet objet déclaré ?
                        Il sera définitivement retiré de la plateforme.
                    </p>
                </div>

                <div class="modal__footer">
                    <button
                        class="modal__btn modal__btn--secondary"
                        type="button"
                        @click="cancelDelete"
                    >
                        Annuler
                    </button>
                    <button
                        class="modal__btn modal__btn--danger"
                        type="button"
                        @click="confirmDelete"
                    >
                        <i class="material-symbols-rounded">delete</i>
                        Supprimer
                    </button>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<style lang="scss" scoped>
.declaration-card .resolved-banner {
    margin: 0 0 0.85rem;
    border-bottom: 1.5px solid rgba(15, 43, 76, 0.06);
}

.declarations-page {
    flex: 1;
    padding: 2rem 1.5rem;

    &__inner {
        width: 100%;
        max-width: 860px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 1.75rem;
    }

    &__topbar {
        display: flex;
        align-items: center;
    }

    &__header {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 1rem;
    }

    &__icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        flex-shrink: 0;
        background-color: rgba(232, 65, 10, 0.1);
        border-radius: var(--radius-md);

        i {
            font-size: 1.4rem;
            color: var(--color-secondary);
        }
    }

    &__title {
        font-family: var(--font-display);
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--color-main);
        line-height: 1.1;
    }

    &__subtitle {
        font-family: var(--font-body);
        font-size: 0.8rem;
        color: var(--color-text);
        opacity: 0.45;
        margin-top: 0.15rem;
    }
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.4rem 0.85rem 0.4rem 0.55rem;
    background-color: transparent;
    border: 1.5px solid rgba(15, 43, 76, 0.15);
    border-radius: var(--radius-lg);
    font-family: var(--font-body);
    font-size: 0.78rem;
    font-weight: 500;
    color: var(--color-main);
    opacity: 0.6;
    cursor: pointer;
    transition:
        opacity var(--transition),
        border-color var(--transition),
        background-color var(--transition);

    i {
        font-size: 0.95rem;
    }

    &:hover {
        opacity: 1;
        border-color: var(--color-main);
        background-color: rgba(15, 43, 76, 0.04);
    }

    &:active {
        transform: scale(0.97);
    }
}

.declarations-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.declaration-card {
    background-color: #fff;
    border-radius: var(--radius-md);
    border: 1.5px solid rgba(15, 43, 76, 0.08);
    overflow: hidden;
    transition:
        box-shadow var(--transition),
        border-color var(--transition);

    &:hover {
        border-color: rgba(15, 43, 76, 0.15);
        box-shadow: 0 4px 18px rgba(15, 43, 76, 0.06);
    }

    &__header {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        padding: 0.85rem 1.1rem;
        border-bottom: 1.5px solid rgba(15, 43, 76, 0.06);
        background-color: rgba(15, 43, 76, 0.02);
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    &__header-left {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 0.75rem;
    }

    &__index {
        font-family: var(--font-display);
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--color-main);
        opacity: 0.35;
        letter-spacing: 0.04em;
    }

    &__date {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-family: var(--font-body);
        font-size: 0.76rem;
        color: var(--color-text);
        opacity: 0.5;

        i {
            font-size: 0.85rem;
        }
    }

    &__status {
        font-family: var(--font-body);
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.2rem 0.65rem;
        border-radius: var(--radius-lg);
        letter-spacing: 0.02em;
        flex-shrink: 0;
    }

    &__body {
        display: flex;
        flex-direction: row;
        gap: 1.25rem;
        padding: 1.1rem;

        @media (max-width: 560px) {
            flex-direction: column;
        }
    }

    &__image-wrap {
        width: 140px;
        height: 105px;
        flex-shrink: 0;
        border-radius: var(--radius-sm);
        overflow: hidden;
        background-color: rgba(15, 43, 76, 0.04);

        @media (max-width: 560px) {
            width: 100%;
            height: 180px;
        }
    }

    &__image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    &__info {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    &__name {
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 700;
        color: var(--color-main);
        line-height: 1.2;
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
        flex-direction: row;
        flex-wrap: wrap;
        gap: 0.5rem 1rem;
        margin-top: auto;
        padding-top: 0.35rem;
    }

    &__meta-item {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-family: var(--font-body);
        font-size: 0.74rem;
        color: var(--color-text);
        opacity: 0.5;

        i {
            font-size: 0.82rem;
        }
    }

    &__actions {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        flex-shrink: 0;
        justify-content: flex-start;

        @media (max-width: 560px) {
            flex-direction: row;
        }
    }

    &__action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        height: 32px;
        padding: 0 0.85rem;
        font-family: var(--font-body);
        font-size: 0.76rem;
        font-weight: 500;
        border-radius: var(--radius-sm);
        cursor: pointer;
        white-space: nowrap;
        transition:
            background-color var(--transition),
            border-color var(--transition),
            color var(--transition);

        i {
            font-size: 0.9rem;
        }

        &--edit {
            background-color: transparent;
            border: 1.5px solid rgba(15, 43, 76, 0.18);
            color: var(--color-main);

            &:hover {
                background-color: rgba(15, 43, 76, 0.05);
                border-color: var(--color-main);
            }
        }

        &--delete {
            background-color: transparent;
            border: 1.5px solid rgba(192, 57, 43, 0.2);
            color: #c0392b;

            &:hover {
                background-color: rgba(192, 57, 43, 0.06);
                border-color: #c0392b;
            }
        }
    }

    &__missing {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 1rem 1.1rem;
        font-family: var(--font-body);
        font-size: 0.8rem;
        color: var(--color-text);
        opacity: 0.4;

        i {
            font-size: 1rem;
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
        text-align: center;
    }

    &__btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        height: 36px;
        padding: 0 1.1rem;
        margin-top: 0.25rem;
        font-family: var(--font-display);
        font-size: 0.82rem;
        font-weight: 700;
        color: #fff;
        background-color: var(--color-secondary);
        border: 1.5px solid var(--color-secondary);
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition:
            background-color var(--transition),
            border-color var(--transition);

        &:hover {
            background-color: var(--color-main);
            border-color: var(--color-main);
        }
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
    max-width: 420px;
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
        border-radius: var(--radius-sm);

        i {
            font-size: 1.2rem;
        }

        &--danger {
            background-color: rgba(192, 57, 43, 0.1);

            i {
                color: #c0392b;
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

    &__text {
        font-family: var(--font-body);
        font-size: 0.84rem;
        color: var(--color-text);
        opacity: 0.65;
        line-height: 1.6;
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
            border-color var(--transition);

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

        &--danger {
            background-color: #c0392b;
            border: 1.5px solid #c0392b;
            color: #fff;

            &:hover {
                background-color: #a93226;
                border-color: #a93226;
            }

            &:active {
                transform: scale(0.98);
            }
        }
    }
}
</style>
