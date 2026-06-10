<script setup>
import { router, usePage, useForm } from "@inertiajs/vue3";
import { computed, ref, onMounted, onBeforeUnmount } from "vue";
import { route } from "ziggy-js";

const page = usePage();
const form = useForm({});
const deleteForm = useForm({});

const user = computed(() => page.props.auth?.user);
const isAuthenticated = computed(() => !!user.value);
const mobileMenuOpen = ref(false);
const profileDropdownOpen = ref(false);
const showDeleteModal = ref(false);
const profileDropdownRef = ref(null);
const profileTriggerRef = ref(null);

function logout() {
    form.post("/auth/logout", {
        preserveScroll: true,
        onSuccess: () => {
            mobileMenuOpen.value = false;
            profileDropdownOpen.value = false;
        },
        onError: () => console.log("error"),
    });
}

function deleteAccount() {
    deleteForm.delete(route("profile.destroy"), {
        onSuccess: () => {
            showDeleteModal.value = false;
            mobileMenuOpen.value = false;
            profileDropdownOpen.value = false;
        },
        onError: () => console.log("error deleting account"),
    });
}

function openDeleteConfirmation() {
    showDeleteModal.value = true;
    profileDropdownOpen.value = false;
}

function navigate(routeName) {
    mobileMenuOpen.value = false;
    profileDropdownOpen.value = false;
    router.visit(route(routeName));
}

function toggleProfileDropdown() {
    profileDropdownOpen.value = !profileDropdownOpen.value;
}

function handleClickOutside(event) {
    const insideDropdown = profileDropdownRef.value?.contains(event.target);
    const insideTrigger = profileTriggerRef.value?.contains(event.target);
    if (!insideDropdown && !insideTrigger) {
        profileDropdownOpen.value = false;
    }
}

onMounted(() => document.addEventListener("click", handleClickOutside));
onBeforeUnmount(() =>
    document.removeEventListener("click", handleClickOutside),
);

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
    <nav class="navbar">
        <div class="navbar__inner">
            <div class="navbar__left">
                <button
                    class="navbar__logo"
                    type="button"
                    @click="navigate('home')"
                >
                    <i class="material-symbols-rounded">find_in_page</i>
                    FoundIt
                </button>
            </div>

            <div class="navbar__actions" v-if="isAuthenticated">
                <button
                    ref="profileTriggerRef"
                    class="navbar__profile-trigger"
                    type="button"
                    @click="toggleProfileDropdown"
                    :class="{ 'is-active': profileDropdownOpen }"
                >
                    <span class="navbar__avatar">{{
                        getInitials(user.name)
                    }}</span>
                    <span class="navbar__profile-name">{{ user.name }}</span>
                    <i class="material-symbols-rounded navbar__profile-chevron">
                        {{
                            profileDropdownOpen ? "expand_less" : "expand_more"
                        }}
                    </i>
                </button>
            </div>

            <div class="navbar__actions" v-else>
                <button
                    class="navbar__btn navbar__btn--ghost"
                    type="button"
                    @click="navigate('auth.login')"
                >
                    Connexion
                </button>
            </div>

            <button
                class="navbar__burger"
                type="button"
                @click="mobileMenuOpen = !mobileMenuOpen"
                :aria-expanded="mobileMenuOpen"
                aria-label="Menu"
            >
                <i class="material-symbols-rounded">{{
                    mobileMenuOpen ? "close" : "menu"
                }}</i>
            </button>
        </div>

        <div
            class="navbar__mobile"
            :class="{ 'navbar__mobile--open': mobileMenuOpen }"
        >
            <div class="navbar__mobile-inner" v-if="isAuthenticated">
                <span class="navbar__mobile-user">
                    <i class="material-symbols-rounded">account_circle</i>
                    {{ user.name }}
                </span>
                <button
                    class="navbar__mobile-link"
                    type="button"
                    @click="navigate('profile.edit')"
                >
                    <i class="material-symbols-rounded">manage_accounts</i>
                    Modifier le profil
                </button>
                <button
                    class="navbar__mobile-link"
                    type="button"
                    @click="navigate('profile.reclamations')"
                >
                    <i class="material-symbols-rounded">hand_gesture</i>
                    Mes réclamations
                </button>
                <button
                    class="navbar__mobile-link"
                    type="button"
                    @click="navigate('profile.declarations')"
                >
                    <i class="material-symbols-rounded">inventory_2</i>
                    Mes déclarations
                </button>
                <button
                    class="navbar__mobile-logout"
                    @click="logout"
                    :disabled="form.processing"
                >
                    <i class="material-symbols-rounded">logout</i>
                    Déconnexion
                </button>
                <button
                    class="navbar__mobile-delete"
                    @click="openDeleteConfirmation"
                    :disabled="deleteForm.processing"
                >
                    <i class="material-symbols-rounded">delete_forever</i>
                    Supprimer le compte
                </button>
            </div>
            <div class="navbar__mobile-inner" v-else>
                <button
                    class="navbar__mobile-link"
                    type="button"
                    @click="navigate('auth.login')"
                >
                    <i class="material-symbols-rounded">login</i>
                    Connexion
                </button>
                <button
                    class="navbar__mobile-link navbar__mobile-link--primary"
                    type="button"
                    @click="navigate('auth.register')"
                >
                    <i class="material-symbols-rounded">person_add</i>
                    Commencer
                </button>
            </div>
        </div>

        <Teleport to="body">
            <div
                v-if="profileDropdownOpen"
                ref="profileDropdownRef"
                class="profile-dropdown"
            >
                <div class="profile-dropdown__header">
                    <div class="profile-dropdown__avatar-lg">
                        {{ getInitials(user.name) }}
                    </div>
                    <div class="profile-dropdown__identity">
                        <span class="profile-dropdown__name">{{
                            user.name
                        }}</span>
                        <span class="profile-dropdown__email">{{
                            user.email
                        }}</span>
                    </div>
                    <button
                        class="profile-dropdown__close"
                        type="button"
                        @click="profileDropdownOpen = false"
                        aria-label="Fermer"
                    >
                        <i class="material-symbols-rounded">close</i>
                    </button>
                </div>

                <div class="profile-dropdown__meta">
                    <div
                        class="profile-dropdown__meta-item"
                        v-if="user.created_at"
                    >
                        <i class="material-symbols-rounded">calendar_today</i>
                        <span
                            >Membre depuis
                            {{
                                new Date(user.created_at).toLocaleDateString(
                                    "fr-FR",
                                    { month: "long", year: "numeric" },
                                )
                            }}</span
                        >
                    </div>
                    <div class="profile-dropdown__meta-item" v-if="user.role">
                        <i class="material-symbols-rounded">badge</i>
                        <span>{{ user.role }}</span>
                    </div>
                </div>

                <div class="profile-dropdown__divider"></div>

                <nav class="profile-dropdown__nav">
                    <button
                        class="profile-dropdown__nav-item"
                        type="button"
                        @click="navigate('profile.edit')"
                    >
                        <span class="profile-dropdown__nav-icon">
                            <i class="material-symbols-rounded"
                                >manage_accounts</i
                            >
                        </span>
                        <div class="profile-dropdown__nav-text">
                            <span class="profile-dropdown__nav-label"
                                >Modifier le profil</span
                            >
                            <span class="profile-dropdown__nav-hint"
                                >Nom, e-mail, mot de passe</span
                            >
                        </div>
                        <i
                            class="material-symbols-rounded profile-dropdown__nav-arrow"
                            >chevron_right</i
                        >
                    </button>

                    <button
                        class="profile-dropdown__nav-item"
                        type="button"
                        @click="navigate('profile.reclamations')"
                    >
                        <span class="profile-dropdown__nav-icon">
                            <i class="material-symbols-rounded">hand_gesture</i>
                        </span>
                        <div class="profile-dropdown__nav-text">
                            <span class="profile-dropdown__nav-label"
                                >Mes réclamations</span
                            >
                            <span class="profile-dropdown__nav-hint"
                                >Objets que vous avez réclamés</span
                            >
                        </div>
                        <i
                            class="material-symbols-rounded profile-dropdown__nav-arrow"
                            >chevron_right</i
                        >
                    </button>

                    <button
                        class="profile-dropdown__nav-item"
                        type="button"
                        @click="navigate('profile.declarations')"
                    >
                        <span class="profile-dropdown__nav-icon">
                            <i class="material-symbols-rounded">inventory_2</i>
                        </span>
                        <div class="profile-dropdown__nav-text">
                            <span class="profile-dropdown__nav-label"
                                >Mes déclarations</span
                            >
                            <span class="profile-dropdown__nav-hint"
                                >Objets que vous avez déclarés</span
                            >
                        </div>
                        <i
                            class="material-symbols-rounded profile-dropdown__nav-arrow"
                            >chevron_right</i
                        >
                    </button>
                </nav>

                <div class="profile-dropdown__divider"></div>

                <div class="profile-dropdown__footer">
                    <button
                        class="profile-dropdown__logout"
                        type="button"
                        @click="logout"
                        :disabled="form.processing"
                    >
                        <i class="material-symbols-rounded">logout</i>
                        <span v-if="!form.processing">Se déconnecter</span>
                        <span v-else>Déconnexion...</span>
                    </button>
                    <button
                        class="profile-dropdown__delete-trigger"
                        type="button"
                        @click="openDeleteConfirmation"
                        :disabled="deleteForm.processing"
                    >
                        <i class="material-symbols-rounded">delete_forever</i>
                        <span>Supprimer le compte</span>
                    </button>
                </div>
            </div>
        </Teleport>

        <Teleport to="body">
            <div
                class="modal-overlay"
                v-if="showDeleteModal"
                @click.self="showDeleteModal = false"
            >
                <div class="confirm-card animate-scale-in">
                    <div class="confirm-card__header">
                        <span class="confirm-card__icon">
                            <i class="material-symbols-rounded">warning</i>
                        </span>
                        <div>
                            <h2 class="confirm-card__title">
                                Supprimer le compte
                            </h2>
                            <p class="confirm-card__subtitle">
                                Cette action est définitive et irréversible.
                            </p>
                        </div>
                    </div>

                    <div class="confirm-card__body">
                        <p>
                            Êtes-vous sûr de vouloir supprimer votre compte ?
                            Toutes vos données, déclarations et réclamations en
                            cours seront effacées définitivement de nos
                            serveurs.
                        </p>
                    </div>

                    <div class="confirm-card__actions">
                        <button
                            type="button"
                            class="confirm-card__btn confirm-card__btn--secondary"
                            @click="showDeleteModal = false"
                            :disabled="deleteForm.processing"
                        >
                            Annuler
                        </button>
                        <button
                            type="button"
                            class="confirm-card__btn confirm-card__btn--danger"
                            @click="deleteAccount"
                            :disabled="deleteForm.processing"
                        >
                            <span v-if="!deleteForm.processing"
                                >Supprimer définitivement</span
                            >
                            <span v-else class="modal-loading-spinner">
                                <i class="material-symbols-rounded"
                                    >autorenew</i
                                >
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </nav>
</template>

<style lang="scss" scoped>
.navbar {
    width: 100%;
    background-color: #fff;
    border-bottom: 1.5px solid rgba(15, 43, 76, 0.08);
    position: sticky;
    top: 0;
    z-index: 50;

    &__inner {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1.5rem;
        height: 58px;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    &__left {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 0.5rem;
        flex-shrink: 0;
    }

    &__home {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: var(--radius-sm);
        border: 1.5px solid rgba(15, 43, 76, 0.15);
        background: transparent;
        cursor: pointer;
        color: var(--color-main);
        opacity: 0.5;
        flex-shrink: 0;
        transition:
            opacity var(--transition),
            border-color var(--transition),
            background-color var(--transition);

        i {
            font-size: 1.05rem;
        }

        &:hover {
            opacity: 1;
            border-color: var(--color-main);
            background-color: rgba(15, 43, 76, 0.04);
        }

        &:active {
            transform: scale(0.96);
        }
    }

    &__logo {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-family: var(--font-display);
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--color-main);
        background: transparent;
        border: none;
        cursor: pointer;
        flex-shrink: 0;
        padding: 0;
        transition: opacity var(--transition);

        i {
            font-size: 1.3rem;
            color: var(--color-secondary);
        }

        &:hover {
            opacity: 0.8;
        }
    }

    &__actions {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 0.6rem;

        @media (max-width: 600px) {
            display: none;
        }
    }

    &__profile-trigger {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        height: 36px;
        padding: 0 0.65rem 0 0.45rem;
        background: transparent;
        border: 1.5px solid rgba(15, 43, 76, 0.15);
        border-radius: var(--radius-lg);
        cursor: pointer;
        transition:
            background-color var(--transition),
            border-color var(--transition);

        &:hover,
        &.is-active {
            background-color: rgba(15, 43, 76, 0.04);
            border-color: rgba(15, 43, 76, 0.3);
        }
    }

    &__avatar {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background-color: var(--color-main);
        color: #fff;
        font-family: var(--font-display);
        font-size: 0.65rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    &__profile-name {
        font-family: var(--font-body);
        font-size: 0.82rem;
        font-weight: 500;
        color: var(--color-main);
        max-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    &__profile-chevron {
        font-size: 1rem;
        color: var(--color-main);
        opacity: 0.45;
    }

    &__btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 34px;
        padding: 0 1rem;
        font-family: var(--font-display);
        font-size: 0.82rem;
        font-weight: 700;
        border-radius: var(--radius-sm);
        cursor: pointer;
        white-space: nowrap;
        transition:
            background-color var(--transition),
            color var(--transition),
            border-color var(--transition);

        &--ghost {
            background: transparent;
            border: 1.5px solid rgba(15, 43, 76, 0.18);
            color: var(--color-main);

            &:hover {
                background-color: rgba(15, 43, 76, 0.05);
                border-color: var(--color-main);
            }
        }

        &--primary {
            background-color: var(--color-secondary);
            border: 1.5px solid var(--color-secondary);
            color: #fff;

            &:hover {
                background-color: var(--color-main);
                border-color: var(--color-main);
            }

            &:active {
                transform: scale(0.98);
            }
        }
    }

    &__burger {
        display: none;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        background: transparent;
        border: 1.5px solid rgba(15, 43, 76, 0.15);
        border-radius: var(--radius-sm);
        cursor: pointer;
        color: var(--color-main);
        flex-shrink: 0;
        transition:
            background-color var(--transition),
            border-color var(--transition);

        i {
            font-size: 1.15rem;
        }

        &:hover {
            background-color: rgba(15, 43, 76, 0.05);
            border-color: var(--color-main);
        }

        @media (max-width: 600px) {
            display: flex;
        }
    }

    &__mobile {
        max-height: 0;
        overflow: hidden;
        border-top: 0 solid rgba(15, 43, 76, 0.08);
        transition:
            max-height 0.28s cubic-bezier(0.4, 0, 0.2, 1),
            border-top-width var(--transition);

        &--open {
            max-height: 380px;
            border-top-width: 1.5px;
        }
    }

    &__mobile-inner {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        padding: 0.75rem 1.5rem 1rem;
    }

    &__mobile-user {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-family: var(--font-body);
        font-size: 0.82rem;
        font-weight: 500;
        color: var(--color-text);
        opacity: 0.55;
        padding: 0.3rem 0;

        i {
            font-size: 1.1rem;
        }
    }

    &__mobile-logout {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.55rem 0.85rem;
        font-family: var(--font-body);
        font-size: 0.84rem;
        font-weight: 500;
        color: #c0392b;
        background: rgba(192, 57, 43, 0.06);
        border: 1.5px solid rgba(192, 57, 43, 0.2);
        border-radius: var(--radius-sm);
        cursor: pointer;
        width: 100%;
        transition: background-color var(--transition);

        i {
            font-size: 1rem;
        }

        &:hover:not(:disabled) {
            background-color: rgba(192, 57, 43, 0.1);
        }

        &:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    }

    &__mobile-delete {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.55rem 0.85rem;
        font-family: var(--font-body);
        font-size: 0.84rem;
        font-weight: 500;
        color: rgba(15, 43, 76, 0.4);
        background: transparent;
        border: 1.5px dashed rgba(15, 43, 76, 0.15);
        border-radius: var(--radius-sm);
        cursor: pointer;
        width: 100%;
        margin-top: 0.2rem;
        transition:
            color var(--transition),
            border-color var(--transition),
            background-color var(--transition);

        i {
            font-size: 1rem;
        }

        &:hover:not(:disabled) {
            color: #c0392b;
            border-color: rgba(192, 57, 43, 0.3);
            background-color: rgba(192, 57, 43, 0.02);
        }

        &:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    }

    &__mobile-link {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.55rem 0.85rem;
        font-family: var(--font-display);
        font-size: 0.84rem;
        font-weight: 700;
        color: var(--color-main);
        background: transparent;
        border: 1.5px solid rgba(15, 43, 76, 0.18);
        border-radius: var(--radius-sm);
        cursor: pointer;
        width: 100%;
        transition:
            background-color var(--transition),
            border-color var(--transition);

        i {
            font-size: 1rem;
        }

        &:hover {
            background-color: rgba(15, 43, 76, 0.05);
            border-color: var(--color-main);
        }

        &--primary {
            background-color: var(--color-secondary);
            border-color: var(--color-secondary);
            color: #fff;

            &:hover {
                background-color: var(--color-main);
                border-color: var(--color-main);
            }
        }
    }
}

.profile-dropdown {
    position: fixed;
    top: 66px;
    right: max(1.5rem, calc((100vw - 1200px) / 2 + 1.5rem));
    z-index: 200;
    width: 300px;
    background-color: #fff;
    border-radius: var(--radius-lg);
    border: 1.5px solid rgba(15, 43, 76, 0.1);
    box-shadow: 0 8px 40px rgba(15, 43, 76, 0.13);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    animation: dropdown-in 0.18s cubic-bezier(0.4, 0, 0.2, 1);

    @media (max-width: 600px) {
        right: 1rem;
        left: 1rem;
        width: auto;
    }

    &__header {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1rem 0.85rem;
    }

    &__avatar-lg {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: var(--color-main);
        color: #fff;
        font-family: var(--font-display);
        font-size: 0.85rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    &__identity {
        display: flex;
        flex-direction: column;
        gap: 0.1rem;
        flex: 1;
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

    &__email {
        font-family: var(--font-body);
        font-size: 0.72rem;
        color: var(--color-text);
        opacity: 0.45;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    &__close {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
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
            font-size: 0.95rem;
        }

        &:hover {
            opacity: 1;
            border-color: rgba(15, 43, 76, 0.3);
        }
    }

    &__meta {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        padding: 0 1rem 0.85rem;
    }

    &__meta-item {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-family: var(--font-body);
        font-size: 0.72rem;
        color: var(--color-text);
        opacity: 0.45;

        i {
            font-size: 0.85rem;
        }
    }

    &__divider {
        height: 1.5px;
        background-color: rgba(15, 43, 76, 0.06);
        margin: 0;
    }

    &__nav {
        display: flex;
        flex-direction: column;
        padding: 0.5rem 0;
    }

    &__nav-item {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 0.75rem;
        padding: 0.65rem 1rem;
        background: transparent;
        border: none;
        cursor: pointer;
        text-align: left;
        transition: background-color var(--transition);

        &:hover {
            background-color: rgba(15, 43, 76, 0.04);

            .profile-dropdown__nav-arrow {
                opacity: 0.6;
                transform: translateX(2px);
            }
        }
    }

    &__nav-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        flex-shrink: 0;
        background-color: rgba(15, 43, 76, 0.06);
        border-radius: var(--radius-sm);

        i {
            font-size: 1rem;
            color: var(--color-main);
            opacity: 0.7;
        }
    }

    &__nav-text {
        display: flex;
        flex-direction: column;
        gap: 0.05rem;
        flex: 1;
        min-width: 0;
    }

    &__nav-label {
        font-family: var(--font-body);
        font-size: 0.82rem;
        font-weight: 500;
        color: var(--color-main);
    }

    &__nav-hint {
        font-family: var(--font-body);
        font-size: 0.7rem;
        color: var(--color-text);
        opacity: 0.4;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    &__nav-arrow {
        font-size: 1rem;
        color: var(--color-main);
        opacity: 0.25;
        flex-shrink: 0;
        transition:
            opacity var(--transition),
            transform var(--transition);
    }

    &__footer {
        padding: 0.65rem 1rem 0.85rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    &__logout {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        width: 100%;
        height: 36px;
        padding: 0 0.85rem;
        font-family: var(--font-body);
        font-size: 0.82rem;
        font-weight: 500;
        color: #c0392b;
        background: rgba(192, 57, 43, 0.06);
        border: 1.5px solid rgba(192, 57, 43, 0.15);
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition:
            background-color var(--transition),
            border-color var(--transition);

        i {
            font-size: 1rem;
        }

        &:hover:not(:disabled) {
            background-color: rgba(192, 57, 43, 0.1);
            border-color: rgba(192, 57, 43, 0.3);
        }

        &:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    }

    &__delete-trigger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        width: 100%;
        height: 34px;
        padding: 0 0.85rem;
        font-family: var(--font-body);
        font-size: 0.76rem;
        font-weight: 500;
        color: rgba(15, 43, 76, 0.4);
        background: transparent;
        border: none;
        cursor: pointer;
        transition: color var(--transition);

        i {
            font-size: 0.95rem;
        }

        &:hover:not(:disabled) {
            color: #c0392b;
        }

        &:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    }
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(15, 43, 76, 0.4);
    backdrop-filter: blur(4px);
    z-index: 300;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.confirm-card {
    background-color: #fff;
    border-radius: var(--radius-lg);
    border: 1.5px solid rgba(15, 43, 76, 0.08);
    box-shadow: 0 12px 50px rgba(15, 43, 76, 0.16);
    width: 100%;
    max-width: 480px;
    padding: 1.75rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;

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
        width: 42px;
        height: 42px;
        flex-shrink: 0;
        background-color: rgba(192, 57, 43, 0.08);
        border-radius: var(--radius-md);

        i {
            font-size: 1.25rem;
            color: #c0392b;
        }
    }

    &__title {
        font-family: var(--font-display);
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--color-main);
        line-height: 1.1;
    }

    &__subtitle {
        font-family: var(--font-body);
        font-size: 0.74rem;
        color: #c0392b;
        font-weight: 500;
        margin-top: 0.15rem;
    }

    &__body {
        font-family: var(--font-body);
        font-size: 0.84rem;
        color: var(--color-text);
        line-height: 1.5;
        opacity: 0.75;
    }

    &__actions {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: flex-end;
        gap: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1.5px solid rgba(15, 43, 76, 0.06);

        @media (max-width: 400px) {
            flex-direction: column-reverse;

            button {
                width: 100%;
            }
        }
    }

    &__btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        height: 38px;
        padding: 0 1.25rem;
        font-family: var(--font-display);
        font-size: 0.82rem;
        font-weight: 700;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition:
            background-color var(--transition),
            color var(--transition),
            border-color var(--transition);

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

            &:hover:not(:disabled) {
                background-color: #a0281c;
                border-color: #a0281c;
            }

            &:disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }
        }
    }
}

.modal-loading-spinner {
    display: inline-flex;
    align-items: center;

    i {
        font-size: 1.1rem;
        animation: modal-spin 0.9s linear infinite;
    }
}

@keyframes modal-spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

@keyframes dropdown-in {
    from {
        opacity: 0;
        transform: translateY(-6px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-scale-in {
    animation: scale-in 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes scale-in {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>
