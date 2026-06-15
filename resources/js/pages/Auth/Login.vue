<script setup>
import { Link, useForm, router } from "@inertiajs/vue3";
import MainLink from "../../components/ui/links/MainLink.vue";
import PasswordInput from "../../components/ui/PasswordInput.vue";
import RegisterLayout from "../../layouts/RegisterLayout.vue";
import { route } from "ziggy-js";
const form = useForm({
    email: "",
    password: "",
    remember: false,
});

function loginUser() {
    form.post(route("auth.login.post"), {
        onSuccess: () => {
            form.reset();
        },
        onError: () => {},
    });
}
defineOptions({ layout: RegisterLayout });
</script>

<template>
    <div class="login-page">
        <div class="login-panel">
            <div class="login-card">
                <div class="login-card__header">
                    <span class="login-card__logo">
                        <i class="material-symbols-rounded">find_in_page</i>
                    </span>
                    <h2 class="login-card__title">Bon retour</h2>
                    <p class="login-card__subtitle">
                        Connectez-vous à votre espace FoundIt.
                    </p>
                </div>

                <form class="login-form" @submit.prevent="loginUser" novalidate>
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
                                class="form-group__input"
                                type="email"
                                v-model="form.email"
                                placeholder="exemple@email.com"
                                autocomplete="email"
                            />
                        </div>
                        <p v-if="form.errors.email" class="form-group__error">
                            <i class="material-symbols-rounded">error</i
                            >{{ form.errors.email }}
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="form-group__label" for="password"
                            >Mot de passe</label
                        >
                        <PasswordInput
                            id="password"
                            v-model="form.password"
                            :has-error="!!form.errors.password"
                            autocomplete="current-password"
                        />
                        <p
                            v-if="form.errors.password"
                            class="form-group__error"
                        >
                            <i class="material-symbols-rounded">error</i
                            >{{ form.errors.password }}
                        </p>
                    </div>

                    <div class="form-check">
                        <label class="form-check__label">
                            <input
                                class="form-check__input"
                                type="checkbox"
                                v-model="form.remember"
                                id="remember"
                            />
                            <span class="form-check__box">
                                <i class="material-symbols-rounded">check</i>
                            </span>
                            Se souvenir de moi
                        </label>
                    </div>

                    <button
                        class="login-form__submit"
                        type="submit"
                        :disabled="form.processing"
                    >
                        <span v-if="!form.processing">Se connecter</span>
                        <span v-else class="login-form__loading">
                            <i class="material-symbols-rounded">autorenew</i>
                            Connexion...
                        </span>
                    </button>
                </form>

                <div class="login-footer">
                    <span>Pas encore de compte ?</span>
                    <MainLink
                        :lien="route('auth.register')"
                        content="S'inscrire"
                    />
                </div>
            </div>
        </div>

        <div class="login-brand">
            <div class="login-brand__inner">
                <button
                    class="brand-back"
                    type="button"
                    @click="() => router.visit(route('home'))"
                >
                    <i class="material-symbols-rounded">arrow_back</i>
                    Accueil
                </button>

                <span class="login-brand__icon">
                    <i class="material-symbols-rounded">find_in_page</i>
                </span>
                <h1 class="login-brand__name">FoundIt</h1>
                <p class="login-brand__tagline">
                    Une plateforme intuitive pour répertorier, déclarer et
                    restituer les objets égarés.
                </p>
                <ul class="login-brand__features">
                    <li>
                        <i class="material-symbols-rounded">check_circle</i>
                        Déclarez les objets trouvés
                    </li>
                    <li>
                        <i class="material-symbols-rounded">check_circle</i>
                        Gérez les réclamations
                    </li>
                    <li>
                        <i class="material-symbols-rounded">check_circle</i>
                        Suivez les restitutions en direct
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.login-page {
    min-height: 100vh;
    display: flex;
    flex-direction: row;

    @media (max-width: 768px) {
        flex-direction: column-reverse;
    }
}

.login-brand {
    flex: 1;
    background-color: var(--color-main);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem 2.5rem;

    @media (max-width: 768px) {
        padding: 2rem 1.5rem;
        flex: none;
    }

    &__inner {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 1.25rem;
        max-width: 360px;
    }

    &__icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 52px;
        background-color: var(--color-secondary);
        border-radius: var(--radius-md);

        i {
            font-size: 1.6rem;
            color: #fff;
        }
    }

    &__name {
        font-family: var(--font-display);
        font-size: 2.8rem;
        font-weight: 800;
        color: #fff;
        line-height: 1;
    }

    &__tagline {
        font-family: var(--font-body);
        font-size: 0.95rem;
        color: rgba(255, 255, 255, 0.65);
        line-height: 1.6;
        max-width: 280px;
    }

    &__features {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        margin-top: 0.5rem;

        li {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            font-family: var(--font-body);
            font-size: 0.88rem;
            color: rgba(255, 255, 255, 0.8);

            i {
                font-size: 1.05rem;
                color: var(--color-secondary);
                flex-shrink: 0;
            }
        }
    }
}

.login-panel {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--color-third);
    padding: 2.5rem 1.5rem;

    @media (max-width: 768px) {
        padding: 2rem 1rem;
    }
}

.login-card {
    width: 100%;
    max-width: 420px;
    background-color: #fff;
    border-radius: var(--radius-lg);
    padding: 2rem 1.75rem;
    box-shadow: 0 4px 24px rgba(15, 43, 76, 0.08);
    display: flex;
    flex-direction: column;
    gap: 1.25rem;

    @media (max-width: 480px) {
        padding: 1.5rem 1.1rem;
    }

    &__header {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    &__logo {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background-color: var(--color-main);
        border-radius: var(--radius-sm);
        color: var(--color-third);
        margin-bottom: 0.3rem;
        flex-shrink: 0;

        i {
            font-size: 1.3rem;
        }
    }

    &__title {
        font-family: var(--font-display);
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--color-main);
    }

    &__subtitle {
        font-family: var(--font-body);
        font-size: 0.8rem;
        color: var(--color-text);
        opacity: 0.5;
    }
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;

    &__submit {
        margin-top: 0.25rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        width: 100%;
        padding: 0.72rem 1.25rem;
        font-family: var(--font-display);
        font-size: 0.88rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        color: #fff;
        background-color: var(--color-secondary);
        border: none;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition:
            background-color var(--transition),
            transform var(--transition),
            opacity var(--transition);

        &:hover:not(:disabled) {
            background-color: var(--color-main);
        }

        &:active:not(:disabled) {
            transform: scale(0.98);
        }

        &:disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }
    }

    &__loading {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;

        i {
            font-size: 1rem;
            animation: spin 0.9s linear infinite;
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

.form-check {
    display: flex;
    flex-direction: row;
    align-items: center;

    &__label {
        display: inline-flex;
        flex-direction: row;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        font-family: var(--font-body);
        font-size: 0.8rem;
        color: var(--color-text);
        opacity: 0.7;
        user-select: none;
    }

    &__input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }

    &__box {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        border: 1.5px solid rgba(15, 43, 76, 0.25);
        border-radius: 4px;
        background-color: var(--color-third);
        display: flex;
        align-items: center;
        justify-content: center;
        transition:
            background-color var(--transition),
            border-color var(--transition);

        i {
            font-size: 0.75rem;
            color: #fff;
            opacity: 0;
            transition: opacity var(--transition);
        }
    }

    &__input:checked + &__box {
        background-color: var(--color-main);
        border-color: var(--color-main);

        i {
            opacity: 1;
        }
    }
}

.login-footer {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    flex-wrap: wrap;
    padding-top: 0.1rem;

    span {
        font-family: var(--font-body);
        font-size: 0.8rem;
        color: var(--color-text);
        opacity: 0.52;
    }

    :deep(.main-link) {
        background: none;
        border: none;
        padding: 0;
        font-family: var(--font-body);
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--color-secondary);
        text-decoration: underline;
        text-underline-offset: 2px;
        transition: color var(--transition);

        i {
            display: none;
        }

        &:hover {
            color: var(--color-main);
            background: none;
        }

        &:active {
            transform: none;
        }
    }
}
.brand-back {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.4rem 0.85rem 0.4rem 0.55rem;
    background-color: rgba(255, 255, 255, 0.1);
    border: 1.5px solid rgba(255, 255, 255, 0.18);
    border-radius: var(--radius-lg);
    font-family: var(--font-body);
    font-size: 0.78rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.85);
    cursor: pointer;
    transition:
        background-color var(--transition),
        color var(--transition),
        border-color var(--transition);
    align-self: flex-start;

    i {
        font-size: 0.95rem;
    }

    &:hover {
        background-color: rgba(255, 255, 255, 0.18);
        border-color: rgba(255, 255, 255, 0.35);
        color: #fff;
    }

    &:active {
        transform: scale(0.97);
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
