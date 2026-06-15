<script setup>
import { router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { ref } from "vue";
import MainLayout from "../../layouts/MainLayout.vue";

defineOptions({
    layout: MainLayout,
});

defineProps({
    reclamations: Object,
});

const statusMap = {
    pending: { label: "En attente", color: "claim-status--pending" },
    approved: { label: "Approuvée", color: "claim-status--approved" },
    rejected: { label: "Rejetée", color: "claim-status--rejected" },
};

const itemStatusMap = {
    available: { label: "Disponible", color: "status--available" },
    claimed: { label: "Réclamé", color: "status--claimed" },
    returned: { label: "Restitué", color: "status--returned" },
};

function getClaimStatus(status) {
    return statusMap[status] ?? { label: status, color: "" };
}

function getItemStatus(status) {
    return itemStatusMap[status] ?? { label: status, color: "" };
}

function isPending(reclamation) {
    return reclamation.status === "pending";
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
    router.delete(route("claims.destroy", { claim: confirmDeleteId.value }), {
        preserveScroll: true,
        onSuccess: () => {
            confirmDeleteId.value = null;
        },
    });
}

function handleClickOutside(event) {
    if (ignoreNextClick.value) return;
    if (confirmCardRef.value && !confirmCardRef.value.contains(event.target)) {
        confirmDeleteId.value = null;
    }
}

import { onMounted, onBeforeUnmount } from "vue";
onMounted(() => document.addEventListener("click", handleClickOutside));
onBeforeUnmount(() =>
    document.removeEventListener("click", handleClickOutside),
);
</script>

<template>
    <div class="claims-page">
        <div class="claims-page__inner container">
            <div class="claims-page__topbar">
                <button
                    class="back-btn"
                    type="button"
                    @click="router.visit(route('dashboard'))"
                >
                    <i class="material-symbols-rounded">arrow_back</i>
                    Retour
                </button>
            </div>

            <div class="claims-page__header">
                <span class="claims-page__icon">
                    <i class="material-symbols-rounded">hand_gesture</i>
                </span>
                <div>
                    <h1 class="claims-page__title">Mes réclamations</h1>
                    <p class="claims-page__subtitle">
                        {{ reclamations?.length ?? 0 }} réclamation{{
                            (reclamations?.length ?? 0) !== 1 ? "s" : ""
                        }}
                    </p>
                </div>
            </div>

            <div v-if="reclamations?.length > 0" class="claims-list">
                <div
                    class="claim-card"
                    v-for="(reclamation, idx) in reclamations"
                    :key="reclamation.id"
                >
                    <div class="claim-card__header">
                        <div class="claim-card__header-left">
                            <span class="claim-card__index"
                                >#{{ idx + 1 }}</span
                            >
                            <span class="claim-card__date">
                                <i class="material-symbols-rounded">schedule</i>
                                Réclamé le
                                {{ formatDate(reclamation.created_at) }}
                            </span>
                        </div>
                        <span
                            class="claim-card__status"
                            :class="getClaimStatus(reclamation.status).color"
                        >
                            {{ getClaimStatus(reclamation.status).label }}
                        </span>
                    </div>

                    <div
                        v-if="reclamation.status === 'approved'"
                        class="resolved-banner claim-card__resolved"
                    >
                        <span class="resolved-banner__icon">
                            <i class="material-symbols-rounded">verified</i>
                        </span>
                        <div class="resolved-banner__content">
                            <span class="resolved-banner__title"
                                >Réclamation approuvée — objet restitué</span
                            >
                            <p class="resolved-banner__text">
                                Votre demande a été validée. Contactez le
                                déclarant pour organiser la récupération de
                                l'objet.
                            </p>
                            <div
                                v-if="reclamation.declarant_contact_phone"
                                class="resolved-banner__contact"
                            >
                                <i class="material-symbols-rounded">call</i>
                                <span
                                    >{{
                                        reclamation.declarant_name ?? "Déclarant"
                                    }}
                                    :
                                    <a
                                        :href="`tel:${reclamation.declarant_contact_phone}`"
                                        >{{
                                            reclamation.declarant_contact_phone
                                        }}</a
                                    ></span
                                >
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="reclamation.status === 'rejected' && reclamation.rejection_reason"
                        class="claim-card__feedback claim-card__feedback--error"
                    >
                        <i class="material-symbols-rounded">cancel</i>
                        <div>
                            <strong>Réclamation refusée</strong>
                            <p>{{ reclamation.rejection_reason }}</p>
                        </div>
                    </div>

                    <div
                        v-if="reclamation.status === 'pending'"
                        class="claim-card__feedback claim-card__feedback--pending"
                    >
                        <i class="material-symbols-rounded">hourglass_top</i>
                        <div>
                            <strong>En cours de traitement</strong>
                            <p class="form-notice form-notice--inline">
                                <i class="material-symbols-rounded">gpp_maybe</i>
                                <span
                                    >Votre description doit être
                                    <strong>précise et détaillée</strong>
                                    (éléments distinctifs, marque, gravures,
                                    circonstances). Une description insuffisante
                                    entraîne un rejet.</span
                                >
                            </p>
                        </div>
                    </div>

                    <div
                        v-if="reclamation.claim_notes"
                        class="claim-card__notes"
                    >
                        <i class="material-symbols-rounded">notes</i>
                        <span>{{ reclamation.claim_notes }}</span>
                    </div>

                    <div v-if="reclamation.item" class="claim-card__body">
                        <div
                            class="claim-card__image-wrap"
                            v-if="reclamation.item.image_path"
                        >
                            <img
                                :src="`/storage/items/${reclamation.item.image_path}`"
                                :alt="reclamation.item.name"
                                class="claim-card__image"
                            />
                        </div>

                        <div class="claim-card__info">
                            <h3 class="claim-card__name">
                                {{ reclamation.item.name }}
                            </h3>
                            <p class="claim-card__description">
                                {{ reclamation.item.description }}
                            </p>

                            <div class="claim-card__meta">
                                <span
                                    v-if="reclamation.item"
                                    class="claim-card__meta-item"
                                >
                                    <i class="material-symbols-rounded"
                                        >inventory_2</i
                                    >
                                    Objet :
                                    {{
                                        getItemStatus(reclamation.item.status)
                                            .label
                                    }}
                                </span>
                                <span class="claim-card__meta-item">
                                    <i class="material-symbols-rounded"
                                        >label</i
                                    >
                                    {{ reclamation.item.category?.name }}
                                </span>
                                <span class="claim-card__meta-item">
                                    <i class="material-symbols-rounded"
                                        >place</i
                                    >
                                    {{ reclamation.item.location?.name }}
                                </span>
                                <span class="claim-card__meta-item">
                                    <i class="material-symbols-rounded"
                                        >calendar_today</i
                                    >
                                    Trouvé le
                                    {{
                                        formatDate(reclamation.item.found_date)
                                    }}
                                </span>
                                <span class="claim-card__meta-item">
                                    <i class="material-symbols-rounded"
                                        >add_circle</i
                                    >
                                    Déclaré le
                                    {{
                                        formatDate(reclamation.item.created_at)
                                    }}
                                </span>
                            </div>
                        </div>

                        <div class="claim-card__actions" v-if="isPending(reclamation)">
                            <button
                                class="claim-card__action-btn claim-card__action-btn--edit"
                                type="button"
                                @click="
                                    router.visit(
                                        route('claim.modify', {
                                            claim: reclamation.id,
                                        }),
                                    )
                                "
                            >
                                <i class="material-symbols-rounded">edit</i>
                                Modifier
                            </button>
                            <button
                                class="claim-card__action-btn claim-card__action-btn--delete"
                                type="button"
                                @click="askDelete(reclamation.id)"
                            >
                                <i class="material-symbols-rounded">delete</i>
                                Supprimer
                            </button>
                        </div>
                    </div>

                    <div v-else class="claim-card__missing">
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
                    Vous n'avez effectué aucune réclamation pour le moment.
                </p>
                <button
                    class="empty-state__btn"
                    type="button"
                    @click="router.visit(route('dashboard'))"
                >
                    Parcourir les objets trouvés
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
                        <h3 class="modal__title">Supprimer la réclamation</h3>
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
                        Êtes-vous sûr de vouloir supprimer cette réclamation ?
                        Elle sera définitivement retirée.
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
.claims-page {
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

.claims-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.claim-card {
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

    &__notes {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        padding: 0.7rem 1.1rem;
        background-color: rgba(232, 65, 10, 0.03);
        border-bottom: 1.5px solid rgba(15, 43, 76, 0.05);

        i {
            font-size: 0.95rem;
            color: var(--color-secondary);
            opacity: 0.7;
            flex-shrink: 0;
            margin-top: 0.05rem;
        }

        span {
            font-family: var(--font-body);
            font-size: 0.8rem;
            color: var(--color-text);
            opacity: 0.65;
            line-height: 1.55;
            font-style: italic;
        }
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

.claim-status--pending {
    background-color: rgba(232, 65, 10, 0.12);
    color: #a02d07;
}

.claim-status--approved {
    background-color: rgba(39, 174, 96, 0.12);
    color: #1a6b3c;
}

.claim-status--rejected {
    background-color: rgba(192, 57, 43, 0.12);
    color: #c0392b;
}

.claim-card__feedback {
    display: flex;
    gap: 0.65rem;
    padding: 0.85rem 1.1rem;
    border-bottom: 1.5px solid rgba(15, 43, 76, 0.05);

    i {
        font-size: 1.15rem;
        flex-shrink: 0;
        margin-top: 0.1rem;
    }

    strong {
        display: block;
        font-family: var(--font-display);
        font-size: 0.78rem;
        font-weight: 700;
        margin-bottom: 0.2rem;
    }

    p {
        font-family: var(--font-body);
        font-size: 0.78rem;
        line-height: 1.5;
        opacity: 0.75;
        margin: 0;
    }

    &--success {
        background: rgba(39, 174, 96, 0.06);
        color: #1a6b3c;

        i {
            color: #27ae60;
        }
    }

    &--error {
        background: rgba(192, 57, 43, 0.06);
        color: #c0392b;

        i {
            color: #c0392b;
        }
    }

    &--pending {
        background: rgba(232, 65, 10, 0.05);
        color: #a02d07;

        i {
            color: var(--color-secondary);
        }
    }
}

.claim-card__resolved {
    margin: 0 1.1rem;
    border-bottom: 1.5px solid rgba(15, 43, 76, 0.05);
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
