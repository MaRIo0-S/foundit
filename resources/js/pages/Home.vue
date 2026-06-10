<script setup>
import { usePage } from "@inertiajs/vue3";
import MainLink from "../components/ui/links/MainLink.vue";
import { computed, ref } from "vue";
import LandingPageLayout from "../layouts/LandingPageLayout.vue";

const page = usePage();
const user = computed(() => page.props.auth.user);
const isAuthenticated = computed(() => !!user.value);

const itemsCount = computed(() => page.props.items);
const claimsCount = computed(() => page.props.claims);
const employeesCount = computed(() => page.props.users);

defineOptions({ layout: LandingPageLayout });
</script>

<template>
    <div class="home">
        <div class="home__inner container">
            <div class="home__info">
                <span class="home__badge">
                    <i class="material-symbols-rounded">find_in_page</i>
                    Gestion Interne Centralisée
                </span>
                <h1 class="home__title">FoundIt</h1>
                <h3 class="home__subtitle">
                    Une plateforme intuitive pour répertorier, déclarer et
                    restituer les objets égarés au sein de votre établissement.
                </h3>
                <div class="home__cta-group" v-if="!isAuthenticated">
                    <MainLink :lien="route('auth.login')" content="Commencer" />
                    <span class="home__or">ou</span>
                    <MainLink
                        :lien="route('dashboard')"
                        content="Voir les objets"
                    />
                </div>
                <div class="home__cta-group" v-else>
                    <MainLink
                        :lien="route('dashboard')"
                        content="Voir les objets"
                    />
                    <span class="home__or">ou</span>
                    <MainLink
                        :lien="route('item.declare')"
                        content="Déclarer un objet"
                    />
                </div>
                <div class="home__stats">
                    <div class="home__stat">
                        <span class="home__stat-number">{{ itemsCount }}</span>
                        <span class="home__stat-label">Objets Trouvés</span>
                    </div>
                    <div class="home__stats-divider"></div>
                    <div class="home__stat">
                        <span class="home__stat-number">{{ claimsCount }}</span>
                        <span class="home__stat-label">Réclamations</span>
                    </div>
                    <div class="home__stats-divider"></div>
                    <div class="home__stat">
                        <span class="home__stat-number">{{
                            employeesCount
                        }}</span>
                        <span class="home__stat-label">Collaborateurs</span>
                    </div>
                </div>
            </div>
            <div class="home__picture" aria-label="Hero illustration"></div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.home {
    flex: 1;
    display: flex;
    align-items: center;
    padding: 3rem 1.5rem;
    background-color: var(--color-third);
    &__inner {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        gap: 3rem;

        @media (max-width: 768px) {
            flex-direction: column;
            text-align: center;
        }
    }

    &__info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 1.25rem;
        flex: 1;

        @media (max-width: 768px) {
            align-items: center;
        }
    }

    &__badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background-color: var(--color-main);
        color: var(--color-third);
        font-family: var(--font-body);
        font-size: 0.8rem;
        font-weight: 500;
        letter-spacing: 0.04em;
        padding: 0.35rem 0.85rem;
        border-radius: var(--radius-lg);

        i {
            font-size: 1rem;
        }
    }

    &__title {
        font-family: var(--font-display);
        font-size: clamp(2.8rem, 6vw, 5rem);
        font-weight: 800;
        color: var(--color-main);
        line-height: 1.05;
    }

    &__subtitle {
        font-family: var(--font-body);
        font-size: clamp(1rem, 2vw, 1.2rem);
        font-weight: 300;
        color: var(--color-text);
        max-width: 480px;
        line-height: 1.7;
    }

    &__cta-group {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;

        @media (max-width: 768px) {
            justify-content: center;
        }
    }

    &__or {
        font-family: var(--font-body);
        font-size: 0.9rem;
        color: var(--color-text);
        opacity: 0.5;
    }

    &__stats {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 1.25rem;
        margin-top: 0.25rem;
    }

    &__stat {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.1rem;

        @media (max-width: 768px) {
            align-items: center;
        }
    }

    &__stat-number {
        font-family: var(--font-display);
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--color-main);
        line-height: 1;
    }

    &__stat-label {
        font-family: var(--font-body);
        font-size: 0.75rem;
        font-weight: 400;
        color: var(--color-text);
        opacity: 0.5;
        letter-spacing: 0.02em;
    }

    &__stats-divider {
        width: 1px;
        height: 32px;
        background-color: var(--color-main);
        opacity: 0.15;
        flex-shrink: 0;
    }

    &__picture {
        flex: 1;
        min-height: 420px;
        max-width: 540px;
        border-radius: var(--radius-lg);
        background-color: var(--color-main);
        opacity: 0.08;

        @media (max-width: 768px) {
            width: 100%;
            min-height: 220px;
            max-width: 100%;
        }
    }
}

.home__cta-group {
    :deep(.main-link:last-of-type) {
        background-color: transparent;
        color: var(--color-main);
        border-color: var(--color-main);

        &:hover {
            background-color: var(--color-main);
            color: #fff;
        }
    }
}
</style>
