<script setup>
import { router, usePage } from "@inertiajs/vue3";
import MainLink from "../components/ui/links/MainLink.vue";
import { computed } from "vue";
import { route } from "ziggy-js";
import LandingPageLayout from "../layouts/LandingPageLayout.vue";

const page = usePage();
const user = computed(() => page.props.auth.user);
const isAuthenticated = computed(() => !!user.value);

const itemsCount = computed(() => page.props.items);
const claimsCount = computed(() => page.props.claims);
const employeesCount = computed(() => page.props.users);

const heroLink = computed(() =>
    isAuthenticated.value ? route("dashboard") : route("auth.login"),
);

const floatingItems = [
    { icon: "key", label: "Clés", x: "12%", y: "18%", delay: "0s" },
    { icon: "smartphone", label: "Téléphone", x: "72%", y: "12%", delay: "0.4s" },
    { icon: "backpack", label: "Sac", x: "8%", y: "62%", delay: "0.8s" },
    { icon: "watch", label: "Montre", x: "78%", y: "58%", delay: "1.2s" },
];

function enterSite() {
    router.visit(heroLink.value);
}

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

            <button
                class="home-hero"
                type="button"
                @click="enterSite"
                :aria-label="
                    isAuthenticated
                        ? 'Accéder au catalogue'
                        : 'Se connecter pour commencer'
                "
            >
                <div class="home-hero__glow"></div>
                <div class="home-hero__grid"></div>

                <div
                    v-for="item in floatingItems"
                    :key="item.label"
                    class="home-hero__float"
                    :style="{
                        left: item.x,
                        top: item.y,
                        animationDelay: item.delay,
                    }"
                >
                    <i class="material-symbols-rounded">{{ item.icon }}</i>
                    <span>{{ item.label }}</span>
                </div>

                <div class="home-hero__center">
                    <div class="home-hero__icon-ring">
                        <i class="material-symbols-rounded">find_in_page</i>
                    </div>
                    <p class="home-hero__tagline">Perdu ? Retrouvé.</p>
                    <p class="home-hero__sub">
                        Des objets attendent leur propriétaire dans votre
                        établissement.
                    </p>
                    <span class="home-hero__cta">
                        {{ isAuthenticated ? "Explorer le catalogue" : "Entrer sur FoundIt" }}
                        <i class="material-symbols-rounded">arrow_forward</i>
                    </span>
                </div>

                <div class="home-hero__chip home-hero__chip--left">
                    <i class="material-symbols-rounded">inventory_2</i>
                    <span>{{ itemsCount }} objets référencés</span>
                </div>
                <div class="home-hero__chip home-hero__chip--right">
                    <i class="material-symbols-rounded">verified</i>
                    <span>Restitution sécurisée</span>
                </div>
            </button>
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
}

.home-hero {
    position: relative;
    flex: 1;
    width: 100%;
    max-width: 520px;
    min-height: 440px;
    padding: 0;
    border: none;
    border-radius: var(--radius-lg);
    overflow: hidden;
    cursor: pointer;
    background: linear-gradient(
        145deg,
        #0f2b4c 0%,
        #1a3d66 45%,
        #0f2b4c 100%
    );
    box-shadow:
        0 20px 60px rgba(15, 43, 76, 0.25),
        0 0 0 1px rgba(255, 255, 255, 0.06) inset;
    transition:
        transform 0.35s cubic-bezier(0.4, 0, 0.2, 1),
        box-shadow 0.35s ease;

    @media (max-width: 768px) {
        max-width: 100%;
        min-height: 360px;
    }

    &:hover {
        transform: translateY(-4px) scale(1.01);
        box-shadow:
            0 28px 70px rgba(15, 43, 76, 0.32),
            0 0 0 1px rgba(255, 255, 255, 0.1) inset;

        .home-hero__cta i {
            transform: translateX(4px);
        }

        .home-hero__icon-ring {
            transform: scale(1.05);
            box-shadow: 0 0 40px rgba(232, 65, 10, 0.45);
        }
    }

    &__glow {
        position: absolute;
        width: 280px;
        height: 280px;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        border-radius: 50%;
        background: radial-gradient(
            circle,
            rgba(232, 65, 10, 0.35) 0%,
            transparent 70%
        );
        pointer-events: none;
    }

    &__grid {
        position: absolute;
        inset: 0;
        opacity: 0.08;
        background-image:
            linear-gradient(rgba(255, 255, 255, 0.5) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.5) 1px, transparent 1px);
        background-size: 32px 32px;
        pointer-events: none;
    }

    &__float {
        position: absolute;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.25rem;
        padding: 0.55rem 0.65rem;
        border-radius: var(--radius-md);
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(6px);
        animation: float-y 4s ease-in-out infinite;

        i {
            font-size: 1.35rem;
            color: #fff;
        }

        span {
            font-family: var(--font-body);
            font-size: 0.58rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.75);
            letter-spacing: 0.02em;
        }
    }

    &__center {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        height: 100%;
        padding: 2rem 1.5rem;
        gap: 0.65rem;
    }

    &__icon-ring {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 88px;
        height: 88px;
        border-radius: 50%;
        background: linear-gradient(
            135deg,
            var(--color-secondary) 0%,
            #ff6b35 100%
        );
        box-shadow: 0 8px 32px rgba(232, 65, 10, 0.4);
        margin-bottom: 0.5rem;
        transition:
            transform 0.35s ease,
            box-shadow 0.35s ease;

        i {
            font-size: 2.6rem;
            color: #fff;
        }
    }

    &__tagline {
        font-family: var(--font-display);
        font-size: clamp(1.4rem, 3vw, 1.85rem);
        font-weight: 800;
        color: #fff;
        line-height: 1.15;
        margin: 0;
    }

    &__sub {
        font-family: var(--font-body);
        font-size: 0.82rem;
        color: rgba(255, 255, 255, 0.65);
        line-height: 1.5;
        max-width: 260px;
        margin: 0;
    }

    &__cta {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        margin-top: 0.75rem;
        padding: 0.55rem 1.1rem;
        border-radius: var(--radius-lg);
        background: rgba(255, 255, 255, 0.12);
        border: 1.5px solid rgba(255, 255, 255, 0.25);
        font-family: var(--font-display);
        font-size: 0.82rem;
        font-weight: 700;
        color: #fff;
        transition: background 0.25s ease;

        i {
            font-size: 1rem;
            transition: transform 0.25s ease;
        }
    }

    &:hover &__cta {
        background: rgba(255, 255, 255, 0.18);
    }

    &__chip {
        position: absolute;
        z-index: 3;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.7rem;
        border-radius: var(--radius-lg);
        background: #fff;
        font-family: var(--font-body);
        font-size: 0.68rem;
        font-weight: 600;
        color: var(--color-main);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);

        i {
            font-size: 0.95rem;
            color: var(--color-secondary);
        }

        &--left {
            bottom: 1.25rem;
            left: 1.25rem;
        }

        &--right {
            top: 1.25rem;
            right: 1.25rem;
        }
    }
}

@keyframes float-y {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-8px);
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
