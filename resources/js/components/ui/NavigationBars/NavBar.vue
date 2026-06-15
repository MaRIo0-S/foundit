<script setup>
import { router, usePage, useForm } from "@inertiajs/vue3";
import { computed, ref, onMounted, onBeforeUnmount } from "vue";
import { route } from "ziggy-js";

const page = usePage();
const form = useForm({});
const deleteForm = useForm({});
const markReadForm = useForm({});

const user = computed(() => page.props.auth?.user);
const isAuthenticated = computed(() => !!user.value);
const isAdmin = computed(() => page.props.auth?.is_admin ?? false);
const roleLabel = computed(() => {
    const role = page.props.auth?.role;
    if (role === "admin") return "Administrateur";
    if (role === "user") return "Utilisateur";
    return role ?? "";
});
const pendingClaims = computed(() => page.props.adminNav?.pendingClaims ?? 0);
const unreadNotifications = computed(
    () => page.props.unreadNotifications ?? 0,
);
const notifications = computed(() => page.props.notifications ?? []);
const currentUrl = computed(() => page.url);
const isObjectsTabActive = computed(
    () =>
        currentUrl.value.startsWith("/dashboard") ||
        currentUrl.value.startsWith("/declare"),
);
const isAdminTabActive = computed(() =>
    currentUrl.value.startsWith("/admin"),
);
const mobileMenuOpen = ref(false);
const profileDropdownOpen = ref(false);
const notificationPanelOpen = ref(false);
const showDeleteModal = ref(false);
const profileDropdownRef = ref(null);
const profileTriggerRef = ref(null);
const notificationPanelRef = ref(null);
const notificationTriggerDesktopRef = ref(null);
const notificationTriggerMobileRef = ref(null);

function logout() {
    form.post("/auth/logout", {
        preserveScroll: true,
        onSuccess: () => {
            mobileMenuOpen.value = false;
            profileDropdownOpen.value = false;
        },
        onError: () => {},
    });
}

function deleteAccount() {
    deleteForm.delete(route("profile.destroy"), {
        onSuccess: () => {
            showDeleteModal.value = false;
            mobileMenuOpen.value = false;
            profileDropdownOpen.value = false;
        },
        onError: () => {},
    });
}

function openDeleteConfirmation() {
    showDeleteModal.value = true;
    profileDropdownOpen.value = false;
}

function navigate(routeName) {
    mobileMenuOpen.value = false;
    profileDropdownOpen.value = false;
    notificationPanelOpen.value = false;
    router.visit(route(routeName));
}

function toggleProfileDropdown() {
    profileDropdownOpen.value = !profileDropdownOpen.value;
    if (profileDropdownOpen.value) {
        notificationPanelOpen.value = false;
    }
}

function toggleNotificationPanel() {
    const willOpen = !notificationPanelOpen.value;
    notificationPanelOpen.value = willOpen;
    profileDropdownOpen.value = false;
    mobileMenuOpen.value = false;

    if (willOpen && unreadNotifications.value > 0) {
        markReadForm.post(route("notifications.mark-read"), {
            preserveScroll: true,
            preserveState: true,
        });
    }
}

function formatNotificationDate(dateStr) {
    if (!dateStr) return "";
    return new Date(dateStr).toLocaleDateString("fr-FR", {
        day: "numeric",
        month: "short",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function handleClickOutside(event) {
    const insideDropdown = profileDropdownRef.value?.contains(event.target);
    const insideTrigger = profileTriggerRef.value?.contains(event.target);
    const insideNotifPanel = notificationPanelRef.value?.contains(event.target);
    const insideNotifTrigger =
        notificationTriggerDesktopRef.value?.contains(event.target) ||
        notificationTriggerMobileRef.value?.contains(event.target);
    if (!insideDropdown && !insideTrigger) {
        profileDropdownOpen.value = false;
    }
    if (!insideNotifPanel && !insideNotifTrigger) {
        notificationPanelOpen.value = false;
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

            <div class="navbar__center" v-if="isAuthenticated">
                <button
                    class="navbar__nav-link"
                    type="button"
                    :class="{ 'is-active': isObjectsTabActive }"
                    @click="navigate('dashboard')"
                >
                    <i class="material-symbols-rounded">inventory_2</i>
                    Objets
                </button>
                <button
                    v-if="isAdmin"
                    class="navbar__nav-link"
                    type="button"
                    :class="{ 'is-active': isAdminTabActive }"
                    @click="navigate('admin.dashboard')"
                >
                    <i class="material-symbols-rounded">admin_panel_settings</i>
                    Administration
                </button>
            </div>

            <div class="navbar__actions" v-if="isAuthenticated">
                <button
                    ref="notificationTriggerDesktopRef"
                    class="navbar__bell"
                    type="button"
                    :class="{ 'is-active': notificationPanelOpen }"
                    @click="toggleNotificationPanel"
                    aria-label="Notifications"
                >
                    <i class="material-symbols-rounded">notifications</i>
                    <span
                        v-if="unreadNotifications > 0"
                        class="navbar__bell-badge"
                        >{{
                            unreadNotifications > 9 ? "9+" : unreadNotifications
                        }}</span
                    >
                </button>
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
                v-if="isAuthenticated"
                ref="notificationTriggerMobileRef"
                class="navbar__bell navbar__bell--mobile"
                type="button"
                :class="{ 'is-active': notificationPanelOpen }"
                @click="toggleNotificationPanel"
                aria-label="Notifications"
            >
                <i class="material-symbols-rounded">notifications</i>
                <span
                    v-if="unreadNotifications > 0"
                    class="navbar__bell-badge"
                    >{{
                        unreadNotifications > 9 ? "9+" : unreadNotifications
                    }}</span
                >
            </button>

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

                <p class="navbar__mobile-section">Navigation</p>
                <button
                    class="navbar__mobile-link"
                    type="button"
                    :class="{ 'is-active': isObjectsTabActive }"
                    @click="navigate('dashboard')"
                >
                    <i class="material-symbols-rounded">inventory_2</i>
                    Objets trouvés
                </button>
                <button
                    v-if="isAdmin"
                    class="navbar__mobile-link"
                    type="button"
                    :class="{ 'is-active': isAdminTabActive }"
                    @click="navigate('admin.dashboard')"
                >
                    <i class="material-symbols-rounded">admin_panel_settings</i>
                    Administration
                </button>

                <template v-if="isAdmin">
                    <p class="navbar__mobile-section">Administration</p>
                    <button
                        class="navbar__mobile-link"
                        type="button"
                        @click="navigate('admin.claims.index')"
                    >
                        <i class="material-symbols-rounded">hand_gesture</i>
                        Réclamations à traiter
                        <span
                            v-if="pendingClaims > 0"
                            class="navbar__mobile-badge"
                            >{{ pendingClaims }}</span
                        >
                    </button>
                </template>

                <p class="navbar__mobile-section">Mon compte</p>
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

                <div class="navbar__mobile-footer">
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
                v-if="notificationPanelOpen"
                ref="notificationPanelRef"
                class="notification-panel"
            >
                <div class="notification-panel__header">
                    <h3 class="notification-panel__title">
                        <i class="material-symbols-rounded">notifications</i>
                        Notifications
                    </h3>
                    <button
                        class="notification-panel__close"
                        type="button"
                        @click="notificationPanelOpen = false"
                        aria-label="Fermer"
                    >
                        <i class="material-symbols-rounded">close</i>
                    </button>
                </div>
                <div v-if="notifications.length" class="notification-panel__list">
                    <div
                        v-for="notif in notifications"
                        :key="notif.id"
                        class="notification-panel__item"
                        :class="{ 'is-unread': !notif.is_read }"
                    >
                        <strong>{{ notif.title }}</strong>
                        <p>{{ notif.message }}</p>
                        <time>{{ formatNotificationDate(notif.created_at) }}</time>
                    </div>
                </div>
                <div v-else class="notification-panel__empty">
                    <i class="material-symbols-rounded">notifications_off</i>
                    <p>Aucune notification pour le moment.</p>
                </div>
                <p class="notification-panel__hint">
                    Les notifications lues sont conservées 7 jours puis
                    supprimées automatiquement.
                </p>
            </div>
        </Teleport>

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
                    <div class="profile-dropdown__meta-item" v-if="roleLabel">
                        <i class="material-symbols-rounded">badge</i>
                        <span>{{ roleLabel }}</span>
                    </div>
                </div>

                <div class="profile-dropdown__divider"></div>

                <nav v-if="isAdmin" class="profile-dropdown__nav">
                    <span class="profile-dropdown__section">Administration</span>
                    <button
                        class="profile-dropdown__nav-item"
                        type="button"
                        @click="navigate('admin.claims.index')"
                    >
                        <span class="profile-dropdown__nav-icon profile-dropdown__nav-icon--admin">
                            <i class="material-symbols-rounded">hand_gesture</i>
                        </span>
                        <div class="profile-dropdown__nav-text">
                            <span class="profile-dropdown__nav-label">
                                Réclamations à traiter
                                <span
                                    v-if="pendingClaims > 0"
                                    class="profile-dropdown__badge"
                                    >{{ pendingClaims }}</span
                                >
                            </span>
                            <span class="profile-dropdown__nav-hint"
                                >Modérer les demandes en attente</span
                            >
                        </div>
                        <i
                            class="material-symbols-rounded profile-dropdown__nav-arrow"
                            >chevron_right</i
                        >
                    </button>
                </nav>

                <div v-if="isAdmin" class="profile-dropdown__divider"></div>

                <nav class="profile-dropdown__nav">
                    <span class="profile-dropdown__section">Mon compte</span>
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

    &__center {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        flex: 1;
        justify-content: center;

        @media (max-width: 768px) {
            display: none;
        }
    }

    &__nav-link {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        height: 34px;
        padding: 0 0.75rem;
        font-family: var(--font-body);
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--color-main);
        background: transparent;
        border: none;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: background-color var(--transition);

        i {
            font-size: 1rem;
            opacity: 0.55;
        }

        &:hover {
            background: rgba(15, 43, 76, 0.05);
        }

        &.is-active {
            background: rgba(232, 65, 10, 0.1);
            color: var(--color-main);
            font-weight: 700;
            box-shadow: inset 0 -2px 0 var(--color-secondary);

            i {
                opacity: 1;
                color: var(--color-secondary);
            }
        }
    }

    &__badge {
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border-radius: 9px;
        background: var(--color-secondary);
        color: #fff;
        font-size: 0.62rem;
        font-weight: 700;
    }

    &__bell {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: transparent;
        border: 1.5px solid rgba(15, 43, 76, 0.15);
        border-radius: var(--radius-sm);
        cursor: pointer;
        color: var(--color-main);
        transition:
            background-color var(--transition),
            border-color var(--transition);

        i {
            font-size: 1.15rem;
        }

        &:hover,
        &.is-active {
            background-color: rgba(15, 43, 76, 0.04);
            border-color: rgba(15, 43, 76, 0.3);
        }

        &--mobile {
            display: none;

            @media (max-width: 600px) {
                display: inline-flex;
            }
        }
    }

    &__bell-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        min-width: 16px;
        height: 16px;
        padding: 0 4px;
        border-radius: 8px;
        background: var(--color-secondary);
        color: #fff;
        font-family: var(--font-body);
        font-size: 0.58rem;
        font-weight: 700;
        line-height: 16px;
        text-align: center;
        border: 2px solid #fff;
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
            max-height: 85vh;
            overflow-y: auto;
            border-top-width: 1.5px;
        }
    }

    &__mobile-section {
        font-family: var(--font-display);
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--color-main);
        opacity: 0.4;
        margin: 0.5rem 0 0.15rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    &__mobile-badge {
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border-radius: 9px;
        background: var(--color-secondary);
        color: #fff;
        font-size: 0.62rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-left: auto;

        &--inline {
            margin-left: 0.35rem;
        }
    }

    &__mobile-footer {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid rgba(15, 43, 76, 0.08);
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

        &.is-active {
            background-color: rgba(232, 65, 10, 0.08);
            border-color: var(--color-secondary);
            color: var(--color-main);
            box-shadow: inset 3px 0 0 var(--color-secondary);
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

.notification-panel {
    position: fixed;
    top: 66px;
    right: max(1.5rem, calc((100vw - 1200px) / 2 + 1.5rem));
    z-index: 210;
    width: 340px;
    max-height: min(70vh, 480px);
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
        align-items: center;
        justify-content: space-between;
        padding: 0.85rem 1rem;
        border-bottom: 1px solid rgba(15, 43, 76, 0.06);
    }

    &__title {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-family: var(--font-display);
        font-size: 0.9rem;
        font-weight: 800;
        color: var(--color-main);
        margin: 0;

        i {
            font-size: 1.1rem;
            color: var(--color-secondary);
        }
    }

    &__close {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        background: transparent;
        border: 1.5px solid rgba(15, 43, 76, 0.12);
        border-radius: var(--radius-sm);
        cursor: pointer;
        color: var(--color-main);
        opacity: 0.5;

        &:hover {
            opacity: 1;
        }
    }

    &__list {
        overflow-y: auto;
        flex: 1;
    }

    &__item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(15, 43, 76, 0.04);

        strong {
            display: block;
            font-family: var(--font-body);
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--color-main);
            margin-bottom: 0.2rem;
        }

        p {
            font-family: var(--font-body);
            font-size: 0.72rem;
            color: var(--color-text);
            opacity: 0.7;
            line-height: 1.45;
            margin: 0 0 0.25rem;
        }

        time {
            font-family: var(--font-body);
            font-size: 0.65rem;
            color: var(--color-text);
            opacity: 0.4;
        }

        &.is-unread {
            background: rgba(232, 65, 10, 0.04);
            border-left: 3px solid var(--color-secondary);
        }
    }

    &__empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 2rem 1rem;
        text-align: center;

        i {
            font-size: 2rem;
            color: var(--color-main);
            opacity: 0.2;
        }

        p {
            font-family: var(--font-body);
            font-size: 0.78rem;
            color: var(--color-text);
            opacity: 0.5;
            margin: 0;
        }
    }

    &__hint {
        padding: 0.55rem 1rem;
        margin: 0;
        font-family: var(--font-body);
        font-size: 0.65rem;
        color: var(--color-text);
        opacity: 0.4;
        border-top: 1px solid rgba(15, 43, 76, 0.06);
        line-height: 1.4;
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

    &__section {
        font-family: var(--font-display);
        font-size: 0.62rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--color-main);
        opacity: 0.38;
        padding: 0.35rem 1rem 0.15rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    &__badge {
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border-radius: 9px;
        background: var(--color-secondary);
        color: #fff;
        font-size: 0.6rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
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

        &--admin {
            background-color: rgba(232, 65, 10, 0.1);

            i {
                color: var(--color-secondary);
                opacity: 1;
            }
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
