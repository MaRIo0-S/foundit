<script setup>
import { useForm, router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import MainLayout from "../../layouts/MainLayout.vue";

defineOptions({
    layout: MainLayout,
});

const props = defineProps({
    reclamation: Object,
});

const form = useForm({
    claim_notes: props.reclamation.claim_notes ?? "",
    contact_phone: props.reclamation.contact_phone ?? "",
});

function submitEdit() {
    form.patch(route("claims.update", { claim: props.reclamation.id }), {
        preserveScroll: true,
    });
}
</script>

<template>
    <div class="modify-page">
        <div class="modify-page__inner container">
            <div class="modify-page__topbar">
                <button
                    class="back-btn"
                    type="button"
                    @click="router.visit(route('profile.reclamations'))"
                >
                    <i class="material-symbols-rounded">arrow_back</i>
                    Retour
                </button>
            </div>

            <div class="modify-page__header">
                <span class="modify-page__icon">
                    <i class="material-symbols-rounded">edit</i>
                </span>
                <div>
                    <h1 class="modify-page__title">Modifier la réclamation</h1>
                    <p class="modify-page__subtitle" v-if="reclamation.item">
                        {{ reclamation.item.name }}
                    </p>
                </div>
            </div>

            <div class="modify-card">
                <div class="modify-card__item-preview" v-if="reclamation.item">
                    <div
                        class="item-preview__image-wrap"
                        v-if="reclamation.item.image_path"
                    >
                        <img
                            :src="reclamation.item.image_path"
                            :alt="reclamation.item.name"
                            class="item-preview__image"
                        />
                    </div>
                    <div class="item-preview__info">
                        <span class="item-preview__name">{{
                            reclamation.item.name
                        }}</span>
                        <div class="item-preview__meta">
                            <span class="item-preview__meta-item">
                                <i class="material-symbols-rounded">label</i>
                                {{ reclamation.item.category?.name }}
                            </span>
                            <span class="item-preview__meta-item">
                                <i class="material-symbols-rounded">place</i>
                                {{ reclamation.item.location?.name }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="modify-card__divider"></div>

                <div
                    v-if="reclamation.status !== 'pending'"
                    class="modify-card__readonly"
                >
                    <i class="material-symbols-rounded">info</i>
                    <p>
                        Cette réclamation a déjà été traitée et ne peut plus
                        être modifiée.
                    </p>
                    <div
                        v-if="
                            reclamation.status === 'rejected' &&
                            reclamation.rejection_reason
                        "
                        class="modify-card__rejection"
                    >
                        <strong>Motif du refus :</strong>
                        {{ reclamation.rejection_reason }}
                    </div>
                </div>

                <form
                    v-else
                    class="modify-form"
                    @submit.prevent="submitEdit"
                    novalidate
                >
                    <div class="form-section">
                        <span class="form-section__label">
                            <i class="material-symbols-rounded">description</i>
                            Votre réclamation
                        </span>

                        <div class="form-group">
                            <label class="form-group__label" for="claim_notes">
                                Description de la réclamation
                            </label>
                            <p class="form-notice">
                                <i class="material-symbols-rounded">gpp_maybe</i>
                                <span
                                    ><strong>Description obligatoire et
                                    détaillée.</strong> Éléments distinctifs,
                                    marque, gravures, circonstances — sous peine
                                    de rejet.</span
                                >
                            </p>
                            <div
                                class="form-group__textarea-wrap"
                                :class="{ 'is-error': form.errors.claim_notes }"
                            >
                                <textarea
                                    id="claim_notes"
                                    class="form-group__textarea"
                                    v-model="form.claim_notes"
                                    placeholder="Expliquez pourquoi cet objet vous appartient..."
                                    rows="6"
                                ></textarea>
                            </div>
                            <p
                                v-if="form.errors.claim_notes"
                                class="form-group__error"
                            >
                                <i class="material-symbols-rounded">error</i>
                                {{ form.errors.claim_notes }}
                            </p>
                        </div>

                        <div class="form-group">
                            <label class="form-group__label" for="contact_phone">
                                Numéro de téléphone
                            </label>
                            <div
                                class="form-group__input-wrap"
                                :class="{ 'is-error': form.errors.contact_phone }"
                            >
                                <i class="material-symbols-rounded">call</i>
                                <input
                                    id="contact_phone"
                                    class="form-group__input"
                                    type="tel"
                                    v-model="form.contact_phone"
                                    placeholder="06 12 34 56 78"
                                    autocomplete="tel"
                                />
                            </div>
                            <p
                                v-if="form.errors.contact_phone"
                                class="form-group__error"
                            >
                                <i class="material-symbols-rounded">error</i>
                                {{ form.errors.contact_phone }}
                            </p>
                        </div>
                    </div>

                    <div class="modify-form__actions">
                        <button
                            type="button"
                            class="modify-form__btn modify-form__btn--secondary"
                            @click="router.visit(route('profile.reclamations'))"
                        >
                            Annuler
                        </button>
                        <button
                            type="submit"
                            class="modify-form__btn modify-form__btn--primary"
                            :disabled="form.processing"
                        >
                            <span v-if="!form.processing">
                                <i class="material-symbols-rounded">save</i>
                                Enregistrer les modifications
                            </span>
                            <span v-else class="btn-loading">
                                <i class="material-symbols-rounded"
                                    >autorenew</i
                                >
                                Enregistrement...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.modify-page {
    flex: 1;
    padding: 2rem 1.5rem;

    &__inner {
        width: 100%;
        max-width: 680px;
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

.modify-card {
    background-color: #fff;
    border-radius: var(--radius-lg);
    border: 1.5px solid rgba(15, 43, 76, 0.08);
    padding: 2rem 1.75rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;

    @media (max-width: 480px) {
        padding: 1.25rem 1rem;
    }

    &__item-preview {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 1rem;
    }

    &__divider {
        height: 1.5px;
        background-color: rgba(15, 43, 76, 0.06);
    }

    &__readonly {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        padding: 1rem 1.1rem;
        border-radius: var(--radius-sm);
        background: rgba(15, 43, 76, 0.03);
        border: 1px solid rgba(15, 43, 76, 0.08);

        > i {
            font-size: 1.2rem;
            color: var(--color-main);
            opacity: 0.5;
        }

        p {
            font-family: var(--font-body);
            font-size: 0.82rem;
            color: var(--color-text);
            opacity: 0.65;
            margin: 0;
            line-height: 1.5;
        }
    }

    &__rejection {
        font-family: var(--font-body);
        font-size: 0.8rem;
        color: #c0392b;
        line-height: 1.5;
        padding: 0.65rem 0.75rem;
        background: rgba(192, 57, 43, 0.06);
        border-radius: var(--radius-sm);

        strong {
            display: block;
            margin-bottom: 0.2rem;
        }
    }
}

.item-preview {
    &__image-wrap {
        width: 64px;
        height: 48px;
        flex-shrink: 0;
        border-radius: var(--radius-sm);
        overflow: hidden;
        background-color: rgba(15, 43, 76, 0.04);
    }

    &__image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    &__info {
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
        min-width: 0;
    }

    &__name {
        font-family: var(--font-display);
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--color-main);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    &__meta {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        gap: 0.4rem 0.85rem;
    }

    &__meta-item {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-family: var(--font-body);
        font-size: 0.72rem;
        color: var(--color-text);
        opacity: 0.45;

        i {
            font-size: 0.8rem;
        }
    }
}

.modify-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;

    &__actions {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: flex-end;
        gap: 0.75rem;
        padding-top: 0.5rem;
        border-top: 1.5px solid rgba(15, 43, 76, 0.06);

        @media (max-width: 480px) {
            flex-direction: column-reverse;
        }
    }

    &__btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        height: 40px;
        padding: 0 1.25rem;
        font-family: var(--font-display);
        font-size: 0.84rem;
        font-weight: 700;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition:
            background-color var(--transition),
            color var(--transition),
            border-color var(--transition),
            opacity var(--transition);

        i {
            font-size: 1rem;
        }

        @media (max-width: 480px) {
            width: 100%;
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

            &:active:not(:disabled) {
                transform: scale(0.98);
            }

            &:disabled {
                opacity: 0.55;
                cursor: not-allowed;
            }
        }
    }
}

.form-section {
    display: flex;
    flex-direction: column;
    gap: 0.85rem;

    &__label {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-family: var(--font-display);
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--color-main);
        letter-spacing: 0.05em;
        text-transform: uppercase;
        opacity: 0.5;

        i {
            font-size: 0.95rem;
        }
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

    &__textarea-wrap {
        border: 1.5px solid rgba(15, 43, 76, 0.15);
        border-radius: var(--radius-sm);
        background-color: var(--color-third);
        transition:
            border-color var(--transition),
            box-shadow var(--transition);

        &:focus-within {
            border-color: var(--color-main);
            box-shadow: 0 0 0 3px rgba(15, 43, 76, 0.07);
        }

        &.is-error {
            border-color: #c0392b;

            &:focus-within {
                box-shadow: 0 0 0 3px rgba(192, 57, 43, 0.08);
            }
        }
    }

    &__textarea {
        width: 100%;
        border: none;
        outline: none;
        background: transparent;
        padding: 0.6rem 0.75rem;
        font-family: var(--font-body);
        font-size: 0.84rem;
        color: var(--color-text);
        resize: vertical;
        min-height: 130px;

        &::placeholder {
            color: var(--color-text);
            opacity: 0.28;
        }
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
            flex-shrink: 0;
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

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>
