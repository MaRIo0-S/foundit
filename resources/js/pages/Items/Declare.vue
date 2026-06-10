<script setup>
import { useForm, router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import MainLayout from "../../layouts/MainLayout.vue";

defineProps({
    categories: Object,
    locations: Object,
});

defineOptions({
    layout: MainLayout,
});

const form = useForm({
    name: "",
    description: "",
    found_date: "",
    image_path: null,
    category_id: "",
    location_id: "",
});

function handleImage(e) {
    form.image_path = e.target.files[0];
}

function storeItem() {
    form.post(route("item.declare.post"));
}
</script>

<template>
    <div class="declare-page">
        <div class="declare-page__inner container">
            <div class="declare-page__topbar">
                <button
                    class="back-btn"
                    type="button"
                    @click="router.visit(route('dashboard'))"
                >
                    <i class="material-symbols-rounded">arrow_back</i>
                    Retour
                </button>
            </div>

            <div class="declare-page__header">
                <span class="declare-page__icon">
                    <i class="material-symbols-rounded">add_circle</i>
                </span>
                <div>
                    <h1 class="declare-page__title">Déclarer un objet</h1>
                    <p class="declare-page__subtitle">
                        Renseignez les informations de l'objet trouvé
                    </p>
                </div>
            </div>

            <div class="declare-card">
                <form
                    class="declare-form"
                    @submit.prevent="storeItem"
                    novalidate
                >
                    <div class="form-section">
                        <span class="form-section__label">
                            <i class="material-symbols-rounded">info</i>
                            Informations générales
                        </span>

                        <div class="form-group">
                            <label class="form-group__label" for="name">
                                Nom de l'objet
                            </label>
                            <div
                                class="form-group__input-wrap"
                                :class="{ 'is-error': form.errors.name }"
                            >
                                <i class="material-symbols-rounded">title</i>
                                <input
                                    id="name"
                                    class="form-group__input"
                                    type="text"
                                    v-model="form.name"
                                    placeholder="Ex: Portefeuille noir, clés, téléphone..."
                                    autocomplete="off"
                                />
                            </div>
                            <p
                                v-if="form.errors.name"
                                class="form-group__error"
                            >
                                <i class="material-symbols-rounded">error</i>
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div class="form-group">
                            <label class="form-group__label" for="description">
                                Description
                            </label>
                            <div
                                class="form-group__textarea-wrap"
                                :class="{ 'is-error': form.errors.description }"
                            >
                                <textarea
                                    id="description"
                                    class="form-group__textarea"
                                    v-model="form.description"
                                    placeholder="Décrivez l'objet, sa couleur, son état, tout détail utile..."
                                    rows="4"
                                ></textarea>
                            </div>
                            <p
                                v-if="form.errors.description"
                                class="form-group__error"
                            >
                                <i class="material-symbols-rounded">error</i>
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <div class="form-group">
                            <label class="form-group__label" for="found_date">
                                Date de découverte
                            </label>
                            <div
                                class="form-group__input-wrap"
                                :class="{ 'is-error': form.errors.found_date }"
                            >
                                <i class="material-symbols-rounded"
                                    >calendar_today</i
                                >
                                <input
                                    id="found_date"
                                    class="form-group__input"
                                    type="date"
                                    v-model="form.found_date"
                                />
                            </div>
                            <p
                                v-if="form.errors.found_date"
                                class="form-group__error"
                            >
                                <i class="material-symbols-rounded">error</i>
                                {{ form.errors.found_date }}
                            </p>
                        </div>
                    </div>

                    <div class="form-section">
                        <span class="form-section__label">
                            <i class="material-symbols-rounded">photo_camera</i>
                            Photo
                        </span>

                        <div class="form-group">
                            <label class="form-group__label" for="image_path">
                                Image de l'objet
                            </label>
                            <label
                                class="file-upload"
                                :class="{
                                    'is-error': form.errors.image_path,
                                    'has-file': form.image_path,
                                }"
                                for="image_path"
                            >
                                <i class="material-symbols-rounded">{{
                                    form.image_path ? "check_circle" : "upload"
                                }}</i>
                                <span class="file-upload__text">
                                    {{
                                        form.image_path
                                            ? form.image_path.name
                                            : "Cliquez pour choisir une image"
                                    }}
                                </span>
                                <span
                                    class="file-upload__hint"
                                    v-if="!form.image_path"
                                >
                                    JPG, PNG — max 5 Mo
                                </span>
                                <input
                                    id="image_path"
                                    type="file"
                                    accept="image/*"
                                    class="file-upload__input"
                                    @change="handleImage"
                                />
                            </label>
                            <p
                                v-if="form.errors.image_path"
                                class="form-group__error"
                            >
                                <i class="material-symbols-rounded">error</i>
                                {{ form.errors.image_path }}
                            </p>
                        </div>
                    </div>

                    <div class="form-section">
                        <span class="form-section__label">
                            <i class="material-symbols-rounded">category</i>
                            Classification
                        </span>

                        <div class="form-row">
                            <div class="form-group">
                                <label
                                    class="form-group__label"
                                    for="category_id"
                                >
                                    Catégorie
                                </label>
                                <div
                                    class="form-group__select-wrap"
                                    :class="{
                                        'is-error': form.errors.category_id,
                                    }"
                                >
                                    <i class="material-symbols-rounded"
                                        >label</i
                                    >
                                    <select
                                        id="category_id"
                                        class="form-group__select"
                                        v-model="form.category_id"
                                    >
                                        <option disabled value="">
                                            Sélectionner...
                                        </option>
                                        <option
                                            v-for="category in categories"
                                            :key="category.id"
                                            :value="category.id"
                                        >
                                            {{ category.name }}
                                        </option>
                                    </select>
                                    <i
                                        class="material-symbols-rounded form-group__chevron"
                                        >expand_more</i
                                    >
                                </div>
                                <p
                                    v-if="form.errors.category_id"
                                    class="form-group__error"
                                >
                                    <i class="material-symbols-rounded"
                                        >error</i
                                    >
                                    {{ form.errors.category_id }}
                                </p>
                            </div>

                            <div class="form-group">
                                <label
                                    class="form-group__label"
                                    for="location_id"
                                >
                                    Lieu de découverte
                                </label>
                                <div
                                    class="form-group__select-wrap"
                                    :class="{
                                        'is-error': form.errors.location_id,
                                    }"
                                >
                                    <i class="material-symbols-rounded"
                                        >place</i
                                    >
                                    <select
                                        id="location_id"
                                        class="form-group__select"
                                        v-model="form.location_id"
                                    >
                                        <option disabled value="">
                                            Sélectionner...
                                        </option>
                                        <option
                                            v-for="location in locations"
                                            :key="location.id"
                                            :value="location.id"
                                        >
                                            {{ location.name }}
                                        </option>
                                    </select>
                                    <i
                                        class="material-symbols-rounded form-group__chevron"
                                        >expand_more</i
                                    >
                                </div>
                                <p
                                    v-if="form.errors.location_id"
                                    class="form-group__error"
                                >
                                    <i class="material-symbols-rounded"
                                        >error</i
                                    >
                                    {{ form.errors.location_id }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="declare-form__actions">
                        <button
                            type="button"
                            class="declare-form__btn declare-form__btn--secondary"
                            @click="router.visit(route('dashboard'))"
                        >
                            Annuler
                        </button>
                        <button
                            type="submit"
                            class="declare-form__btn declare-form__btn--primary"
                            :disabled="form.processing"
                        >
                            <span v-if="!form.processing">
                                <i class="material-symbols-rounded"
                                    >add_circle</i
                                >
                                Déclarer l'objet
                            </span>
                            <span v-else class="btn-loading">
                                <i class="material-symbols-rounded"
                                    >autorenew</i
                                >
                                Envoi...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.declare-page {
    flex: 1;
    padding: 2rem 1.5rem;

    &__inner {
        width: 100%;
        max-width: 760px;
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

.declare-card {
    background-color: #fff;
    border-radius: var(--radius-lg);
    border: 1.5px solid rgba(15, 43, 76, 0.08);
    padding: 2rem 1.75rem;

    @media (max-width: 480px) {
        padding: 1.25rem 1rem;
    }
}

.declare-form {
    display: flex;
    flex-direction: column;
    gap: 2rem;

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

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;

    @media (max-width: 540px) {
        grid-template-columns: 1fr;
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

    &__input-wrap {
        display: flex;
        flex-direction: row;
        align-items: center;
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

        > i {
            padding: 0 0.55rem;
            font-size: 0.95rem;
            color: var(--color-main);
            opacity: 0.38;
            flex-shrink: 0;
        }
    }

    &__input {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        padding: 0.58rem 0.6rem 0.58rem 0;
        font-family: var(--font-body);
        font-size: 0.84rem;
        color: var(--color-text);
        min-width: 0;

        &::placeholder {
            color: var(--color-text);
            opacity: 0.28;
        }

        &[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0.35;
            cursor: pointer;
        }
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
        min-height: 100px;

        &::placeholder {
            color: var(--color-text);
            opacity: 0.28;
        }
    }

    &__select-wrap {
        display: flex;
        flex-direction: row;
        align-items: center;
        border: 1.5px solid rgba(15, 43, 76, 0.15);
        border-radius: var(--radius-sm);
        background-color: var(--color-third);
        position: relative;
        transition:
            border-color var(--transition),
            box-shadow var(--transition);

        &:focus-within {
            border-color: var(--color-main);
            box-shadow: 0 0 0 3px rgba(15, 43, 76, 0.07);
        }

        &.is-error {
            border-color: #c0392b;
        }

        > i:first-child {
            padding: 0 0.55rem;
            font-size: 0.95rem;
            color: var(--color-main);
            opacity: 0.38;
            flex-shrink: 0;
        }
    }

    &__select {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        padding: 0.58rem 2rem 0.58rem 0;
        font-family: var(--font-body);
        font-size: 0.84rem;
        color: var(--color-text);
        appearance: none;
        cursor: pointer;
        min-width: 0;
    }

    &__chevron {
        position: absolute;
        right: 0.55rem;
        font-size: 1rem;
        color: var(--color-main);
        opacity: 0.38;
        pointer-events: none;
        flex-shrink: 0;
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

.file-upload {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    padding: 1.5rem;
    border: 1.5px dashed rgba(15, 43, 76, 0.2);
    border-radius: var(--radius-sm);
    background-color: var(--color-third);
    cursor: pointer;
    transition:
        border-color var(--transition),
        background-color var(--transition);

    &:hover {
        border-color: var(--color-main);
        background-color: rgba(15, 43, 76, 0.03);
    }

    &.is-error {
        border-color: #c0392b;
    }

    &.has-file {
        border-style: solid;
        border-color: rgba(39, 174, 96, 0.4);
        background-color: rgba(39, 174, 96, 0.04);

        i {
            color: #1a6b3c;
        }
    }

    i {
        font-size: 1.5rem;
        color: var(--color-main);
        opacity: 0.35;
    }

    &__text {
        font-family: var(--font-body);
        font-size: 0.82rem;
        font-weight: 500;
        color: var(--color-main);
        opacity: 0.6;
        text-align: center;
        word-break: break-all;
    }

    &__hint {
        font-family: var(--font-body);
        font-size: 0.72rem;
        color: var(--color-text);
        opacity: 0.35;
    }

    &__input {
        display: none;
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
