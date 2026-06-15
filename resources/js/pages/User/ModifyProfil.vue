<script setup>
import { useForm, usePage, router } from "@inertiajs/vue3";
import { computed } from "vue";
import MainLayout from "../../layouts/MainLayout.vue";
import PasswordInput from "../../components/ui/PasswordInput.vue";
import { route } from "ziggy-js";

defineOptions({
    layout: MainLayout,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const form = useForm({
    name: user.value.name,
    email: user.value.email,
    current_password: "",
    new_password: "",
    new_password_confirmation: "",
});

const modifyUser = () => {
    form.patch(route("profile.edit.post"), {
        preserveScroll: true,
        onSuccess: () =>
            form.reset(
                "current_password",
                "new_password",
                "new_password_confirmation",
            ),
    });
};

function getInitials(name) {
    if (!name) return "?";
    return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase()
        .slice(0, 2);
}
</script>

<template>
    <div class="profile-page">
        <div class="profile-page__inner container">
            <div class="profile-page__topbar">
                <button
                    class="back-btn"
                    type="button"
                    @click="router.visit(route('dashboard'))"
                >
                    <i class="material-symbols-rounded">arrow_back</i>
                    Retour
                </button>
            </div>

            <div class="profile-page__header">
                <div class="profile-page__header-left">
                    <div class="profile-page__avatar">
                        {{ getInitials(user.name) }}
                    </div>
                    <div>
                        <h1 class="profile-page__title">{{ user.name }}</h1>
                        <p class="profile-page__subtitle">{{ user.email }}</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="modifyUser" class="profile-card" novalidate>
                <div class="form-section">
                    <span class="form-section__label">
                        <i class="material-symbols-rounded">person</i>
                        Informations générales
                    </span>

                    <div class="form-group">
                        <label class="form-group__label" for="name"
                            >Nom complet</label
                        >
                        <div
                            class="form-group__input-wrap"
                            :class="{ 'is-error': form.errors.name }"
                        >
                            <i class="material-symbols-rounded">badge</i>
                            <input
                                id="name"
                                type="text"
                                class="form-group__input"
                                v-model="form.name"
                                :placeholder="user.name"
                                autocomplete="name"
                            />
                        </div>
                        <p v-if="form.errors.name" class="form-group__error">
                            <i class="material-symbols-rounded">error</i>
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="form-group__label" for="email"
                            >Adresse e-mail</label
                        >
                        <div
                            class="form-group__input-wrap"
                            :class="{ 'is-error': form.errors.email }"
                        >
                            <i class="material-symbols-rounded">mail</i>
                            <input
                                id="email"
                                type="email"
                                class="form-group__input"
                                v-model="form.email"
                                autocomplete="email"
                            />
                        </div>
                        <p v-if="form.errors.email" class="form-group__error">
                            <i class="material-symbols-rounded">error</i>
                            {{ form.errors.email }}
                        </p>
                    </div>
                </div>

                <div class="form-section__divider"></div>

                <div class="form-section">
                    <span class="form-section__label">
                        <i class="material-symbols-rounded">lock</i>
                        Sécurité et mot de passe
                    </span>

                    <p class="form-section__hint">
                        Laissez les champs vides si vous ne souhaitez pas
                        modifier votre mot de passe.
                    </p>

                    <div class="form-group">
                        <label class="form-group__label" for="current_password">
                            Mot de passe actuel
                        </label>
                        <PasswordInput
                            id="current_password"
                            v-model="form.current_password"
                            :has-error="!!form.errors.current_password"
                            placeholder="Requis pour changer de mot de passe"
                            autocomplete="current-password"
                        />
                        <p
                            v-if="form.errors.current_password"
                            class="form-group__error"
                        >
                            <i class="material-symbols-rounded">error</i>
                            {{ form.errors.current_password }}
                        </p>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-group__label" for="new_password">
                                Nouveau mot de passe
                            </label>
                            <PasswordInput
                                id="new_password"
                                v-model="form.new_password"
                                :has-error="!!form.errors.new_password"
                                icon="lock_reset"
                                autocomplete="new-password"
                            />
                            <p
                                v-if="form.errors.new_password"
                                class="form-group__error"
                            >
                                <i class="material-symbols-rounded">error</i>
                                {{ form.errors.new_password }}
                            </p>
                        </div>

                        <div class="form-group">
                            <label
                                class="form-group__label"
                                for="new_password_confirmation"
                            >
                                Confirmation
                            </label>
                            <PasswordInput
                                id="new_password_confirmation"
                                v-model="form.new_password_confirmation"
                                icon="lock_reset"
                                autocomplete="new-password"
                            />
                        </div>
                    </div>
                </div>

                <div class="profile-card__footer">
                    <button
                        type="button"
                        class="profile-card__btn profile-card__btn--secondary"
                        @click="router.visit(route('dashboard'))"
                    >
                        Annuler
                    </button>
                    <button
                        type="submit"
                        class="profile-card__btn profile-card__btn--primary"
                        :disabled="form.processing"
                    >
                        <span v-if="!form.processing">
                            <i class="material-symbols-rounded">save</i>
                            Enregistrer les modifications
                        </span>
                        <span v-else class="btn-loading">
                            <i class="material-symbols-rounded">autorenew</i>
                            Enregistrement...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.profile-page {
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

    &__header-left {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 1rem;
    }

    &__avatar {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background-color: var(--color-main);
        color: #fff;
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    &__title {
        font-family: var(--font-display);
        font-size: 1.4rem;
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

.profile-card {
    background-color: #fff;
    border-radius: var(--radius-lg);
    border: 1.5px solid rgba(15, 43, 76, 0.08);
    padding: 2rem 1.75rem;
    display: flex;
    flex-direction: column;
    gap: 1.75rem;

    @media (max-width: 480px) {
        padding: 1.25rem 1rem;
    }

    &__footer {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: flex-end;
        gap: 0.75rem;
        padding-top: 0.25rem;
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
        height: 38px;
        padding: 0 1.2rem;
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

    &__hint {
        font-family: var(--font-body);
        font-size: 0.76rem;
        color: var(--color-text);
        opacity: 0.4;
        line-height: 1.5;
        margin-top: -0.25rem;
    }

    &__divider {
        height: 1.5px;
        background-color: rgba(15, 43, 76, 0.06);
    }
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;

    @media (max-width: 480px) {
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
