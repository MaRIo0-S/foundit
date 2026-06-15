<script setup>
import { ref } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import StatusBadge from "../../../components/ui/StatusBadge.vue";

const props = defineProps({
    claim: Object,
});

const showRejectModal = ref(false);
const showApproveModal = ref(false);

const rejectForm = useForm({ rejection_reason: "" });
const approveForm = useForm({});

const rejectionPresets = [
    "Détails insuffisants pour identifier l'objet.",
    "Les éléments distinctifs ne correspondent pas.",
    "Cet objet a déjà été restitué à un autre réclamant.",
    "Informations incohérentes avec la déclaration.",
];

function submitApprove() {
    approveForm.post(route("admin.claims.approve", props.claim.id), {
        onSuccess: () => {
            showApproveModal.value = false;
        },
    });
}

function submitReject() {
    rejectForm.post(route("admin.claims.reject", props.claim.id), {
        onSuccess: () => {
            showRejectModal.value = false;
        },
    });
}

function formatDate(dateStr) {
    if (!dateStr) return "—";
    return new Date(dateStr).toLocaleString("fr-FR");
}
</script>

<template>
    <div class="admin-page">
        <header class="admin-page__header">
            <Link :href="route('admin.claims.index')" class="review__back">
                <i class="material-symbols-rounded">arrow_back</i>
                Retour aux réclamations
            </Link>
            <div class="review__title-row">
                <h1 class="admin-page__title">
                    Réclamation #{{ claim.id }}
                </h1>
                <StatusBadge :status="claim.status" />
            </div>
        </header>

        <div class="review__grid">
            <section class="admin-panel">
                <h2 class="admin-panel__title">
                    <i class="material-symbols-rounded">inventory_2</i>
                    Objet concerné
                </h2>
                <div class="object-preview">
                    <img
                        v-if="claim.item?.image_path"
                        :src="`/storage/items/${claim.item.image_path}`"
                        :alt="claim.item?.name"
                        class="object-preview__img"
                    />
                    <div v-else class="object-preview__placeholder">
                        <i class="material-symbols-rounded">image</i>
                    </div>
                    <div class="object-preview__info">
                        <h3>{{ claim.item?.name }}</h3>
                        <p>{{ claim.item?.description }}</p>
                        <div
                            v-if="claim.item?.admin_details"
                            class="message-box message-box--admin"
                        >
                            <span class="message-box__label">
                                <i class="material-symbols-rounded">shield</i>
                                Détails réservés à l'administration
                            </span>
                            <p>{{ claim.item.admin_details }}</p>
                        </div>
                        <ul class="meta-list">
                            <li>
                                <i class="material-symbols-rounded">label</i>
                                {{ claim.item?.category?.name }}
                            </li>
                            <li>
                                <i class="material-symbols-rounded">place</i>
                                {{ claim.item?.location?.name }}
                            </li>
                            <li>
                                <i class="material-symbols-rounded">calendar_today</i>
                                Trouvé le {{ claim.item?.found_date }}
                            </li>
                            <li>
                                <i class="material-symbols-rounded">info</i>
                                Statut objet :
                                <StatusBadge :status="claim.item?.status" />
                            </li>
                        </ul>
                        <p v-if="claim.item?.user" class="declarant">
                            Déclaré par <strong>{{ claim.item.user.name }}</strong>
                            ({{ claim.item.user.email }})
                        </p>
                        <p v-if="claim.item?.contact_phone" class="contact-line">
                            <i class="material-symbols-rounded">call</i>
                            Tél. déclarant (admin) :
                            <strong>{{ claim.item.contact_phone }}</strong>
                        </p>
                    </div>
                </div>
            </section>

            <section class="admin-panel">
                <h2 class="admin-panel__title">
                    <i class="material-symbols-rounded">person</i>
                    Réclamation
                </h2>
                <div class="claimant">
                    <div class="claimant__header">
                        <span class="claimant__avatar">
                            {{ claim.user?.name?.charAt(0) }}
                        </span>
                        <div>
                            <strong>{{ claim.user?.name }}</strong>
                            <span>{{ claim.user?.email }}</span>
                        </div>
                    </div>
                    <dl class="detail-list">
                        <dt>Soumise le</dt>
                        <dd>{{ formatDate(claim.created_at) }}</dd>
                        <dt v-if="claim.contact_phone">Téléphone réclamant</dt>
                        <dd v-if="claim.contact_phone">
                            <strong>{{ claim.contact_phone }}</strong>
                        </dd>
                        <template v-if="claim.reviewed_at">
                            <dt>Traitée le</dt>
                            <dd>{{ formatDate(claim.reviewed_at) }}</dd>
                            <dt>Par</dt>
                            <dd>{{ claim.reviewer?.name ?? "—" }}</dd>
                        </template>
                    </dl>
                    <div class="message-box">
                        <span class="message-box__label">Message du réclamant</span>
                        <p>{{ claim.claim_notes || "—" }}</p>
                    </div>
                    <div v-if="claim.rejection_reason" class="message-box message-box--error">
                        <span class="message-box__label">Motif du refus</span>
                        <p>{{ claim.rejection_reason }}</p>
                    </div>
                </div>
            </section>
        </div>

        <section
            v-if="claim.item?.claims?.length"
            class="admin-panel review-section"
        >
            <h2 class="admin-panel__title">
                <i class="material-symbols-rounded">group</i>
                Autres réclamations sur cet objet
            </h2>
            <table class="mini-table">
                <thead>
                    <tr>
                        <th>Réclamant</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="other in claim.item.claims" :key="other.id">
                        <td>{{ other.user?.name }}</td>
                        <td><StatusBadge :status="other.status" /></td>
                        <td>{{ formatDate(other.created_at) }}</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section v-if="claim.status === 'pending'" class="admin-panel review-section">
            <h2 class="admin-panel__title">
                <i class="material-symbols-rounded">fact_check</i>
                Vérification administrateur
            </h2>
            <ul class="checklist">
                <li>Description cohérente avec l'objet déclaré</li>
                <li>Éléments distinctifs correspondants</li>
                <li>Pas de réclamation concurrente déjà approuvée</li>
                <li>Réclamant identifié (compte actif)</li>
            </ul>
            <div class="action-buttons">
                <button
                    type="button"
                    class="btn btn--approve"
                    @click="showApproveModal = true"
                >
                    <i class="material-symbols-rounded">check_circle</i>
                    Approuver
                </button>
                <button
                    type="button"
                    class="btn btn--reject"
                    @click="showRejectModal = true"
                >
                    <i class="material-symbols-rounded">cancel</i>
                    Rejeter
                </button>
            </div>
        </section>

        <!-- Approve modal -->
        <Teleport to="body">
            <div v-if="showApproveModal" class="modal-overlay" @click.self="showApproveModal = false">
                <div class="modal">
                    <h3 class="modal__title">Approuver la réclamation</h3>
                    <p class="modal__text">
                        L'objet « {{ claim.item?.name }} » sera marqué comme
                        <strong>restitué</strong> et retiré du catalogue. Les
                        numéros de téléphone seront échangés entre le déclarant
                        et le réclamant. Les autres réclamations en attente
                        seront automatiquement rejetées.
                    </p>
                    <div class="modal__footer">
                        <button type="button" class="btn btn--ghost" @click="showApproveModal = false">
                            Annuler
                        </button>
                        <button
                            type="button"
                            class="btn btn--approve"
                            :disabled="approveForm.processing"
                            @click="submitApprove"
                        >
                            Confirmer l'approbation
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="showRejectModal" class="modal-overlay" @click.self="showRejectModal = false">
                <div class="modal modal--wide">
                    <h3 class="modal__title">Rejeter la réclamation</h3>
                    <label class="form-label">Motif du refus (obligatoire)</label>
                    <textarea
                        v-model="rejectForm.rejection_reason"
                        class="form-textarea"
                        rows="4"
                        placeholder="Expliquez pourquoi la réclamation est refusée..."
                    ></textarea>
                    <p v-if="rejectForm.errors.rejection_reason" class="form-error">
                        {{ rejectForm.errors.rejection_reason }}
                    </p>
                    <div class="presets">
                        <button
                            v-for="preset in rejectionPresets"
                            :key="preset"
                            type="button"
                            class="preset-btn"
                            @click="rejectForm.rejection_reason = preset"
                        >
                            {{ preset }}
                        </button>
                    </div>
                    <div class="modal__footer">
                        <button type="button" class="btn btn--ghost" @click="showRejectModal = false">
                            Annuler
                        </button>
                        <button
                            type="button"
                            class="btn btn--reject"
                            :disabled="rejectForm.processing"
                            @click="submitReject"
                        >
                            Confirmer le refus
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<style lang="scss" scoped>
.review__back {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-family: var(--font-body);
    font-size: 0.82rem;
    color: var(--color-main);
    opacity: 0.6;
    margin-bottom: 1rem;

    i {
        font-size: 1rem;
    }

    &:hover {
        opacity: 1;
    }
}

.review__title-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-top: 0.25rem;
}

.review__grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;

    @media (max-width: 900px) {
        grid-template-columns: 1fr;
    }
}

.review-section {
    margin-bottom: 1.5rem;
}

.object-preview {
    display: flex;
    gap: 1rem;
    flex-direction: column;

    @media (min-width: 600px) {
        flex-direction: row;
    }

    &__img {
        width: 100%;
        max-width: 220px;
        aspect-ratio: 4/3;
        object-fit: cover;
        border-radius: var(--radius-sm);
    }

    &__placeholder {
        width: 220px;
        aspect-ratio: 4/3;
        background: rgba(15, 43, 76, 0.05);
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;

        i {
            font-size: 2rem;
            opacity: 0.2;
        }
    }

    &__info h3 {
        font-family: var(--font-display);
        font-size: 1.1rem;
        margin-bottom: 0.35rem;
    }

    &__info p {
        font-size: 0.84rem;
        opacity: 0.65;
        line-height: 1.5;
        margin-bottom: 0.75rem;
    }
}

.meta-list {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    margin-bottom: 0.75rem;

    li {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.8rem;
        opacity: 0.7;

        i {
            font-size: 0.95rem;
        }
    }
}

.declarant {
    font-size: 0.78rem;
    opacity: 0.6;
}

.contact-line {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    margin-top: 0.5rem;
    font-size: 0.78rem;
    color: var(--color-main);

    i {
        font-size: 1rem;
        color: var(--color-secondary);
    }
}

.claimant__header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.claimant__avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--color-main);
    color: #fff;
    font-family: var(--font-display);
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}

.claimant__header div {
    display: flex;
    flex-direction: column;

    strong {
        font-size: 0.95rem;
        color: var(--color-main);
    }

    span {
        font-size: 0.78rem;
        opacity: 0.5;
    }
}

.detail-list {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 0.25rem 1rem;
    margin-bottom: 1rem;
    font-size: 0.82rem;

    dt {
        opacity: 0.45;
    }

    dd {
        font-weight: 500;
    }
}

.message-box {
    background: var(--color-third);
    border-radius: var(--radius-sm);
    padding: 0.85rem;

    &--error {
        background: rgba(220, 38, 38, 0.06);
        margin-top: 0.75rem;
    }

    &--admin {
        margin-top: 0.75rem;
        background: rgba(15, 43, 76, 0.04);
        border: 1px solid rgba(15, 43, 76, 0.1);

        .message-box__label {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
    }

    &__label {
        display: block;
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--color-main);
        margin-bottom: 0.35rem;
    }

    p {
        font-size: 0.84rem;
        line-height: 1.55;
    }
}

.mini-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.82rem;

    th {
        text-align: left;
        padding: 0.5rem;
        font-size: 0.68rem;
        text-transform: uppercase;
        opacity: 0.5;
        border-bottom: 1px solid rgba(15, 43, 76, 0.08);
    }

    td {
        padding: 0.6rem 0.5rem;
        border-bottom: 1px solid rgba(15, 43, 76, 0.05);
    }
}

.checklist {
    list-style: none;
    margin-bottom: 1.25rem;

    li {
        padding: 0.4rem 0 0.4rem 1.5rem;
        position: relative;
        font-size: 0.84rem;
        opacity: 0.75;

        &::before {
            content: "○";
            position: absolute;
            left: 0;
            color: var(--color-secondary);
        }
    }
}

.action-buttons {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.return-text {
    font-size: 0.88rem;
    opacity: 0.7;
    margin-bottom: 1rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    height: 40px;
    padding: 0 1.25rem;
    font-family: var(--font-display);
    font-size: 0.84rem;
    font-weight: 700;
    border-radius: var(--radius-sm);
    border: none;
    cursor: pointer;
    transition: opacity var(--transition);

    &:disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }

    &--approve {
        background: #16a34a;
        color: #fff;
    }

    &--reject {
        background: transparent;
        border: 1.5px solid #dc2626;
        color: #dc2626;
    }

    &--ghost {
        background: transparent;
        border: 1.5px solid rgba(15, 43, 76, 0.18);
        color: var(--color-text);
    }
}

.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 43, 76, 0.35);
    backdrop-filter: blur(2px);
    z-index: 300;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.modal {
    background: #fff;
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    max-width: 440px;
    width: 100%;
    box-shadow: 0 8px 40px rgba(15, 43, 76, 0.16);

    &--wide {
        max-width: 520px;
    }

    &__title {
        font-family: var(--font-display);
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--color-main);
        margin-bottom: 0.75rem;
    }

    &__text {
        font-size: 0.88rem;
        line-height: 1.55;
        opacity: 0.75;
        margin-bottom: 1.25rem;
    }

    &__footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.6rem;
        margin-top: 1rem;
    }
}

.form-label {
    display: block;
    font-size: 0.74rem;
    font-weight: 500;
    color: var(--color-main);
    margin-bottom: 0.35rem;
}

.form-textarea {
    width: 100%;
    border: 1.5px solid rgba(15, 43, 76, 0.15);
    border-radius: var(--radius-sm);
    padding: 0.65rem 0.75rem;
    font-family: var(--font-body);
    font-size: 0.84rem;
    resize: vertical;
    outline: none;

    &:focus {
        border-color: var(--color-main);
    }
}

.form-error {
    font-size: 0.72rem;
    color: #c0392b;
    margin-top: 0.35rem;
}

.presets {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    margin-top: 0.75rem;
}

.preset-btn {
    font-size: 0.72rem;
    padding: 0.35rem 0.65rem;
    border: 1px solid rgba(15, 43, 76, 0.15);
    border-radius: var(--radius-lg);
    background: #fff;
    cursor: pointer;
    opacity: 0.8;

    &:hover {
        border-color: var(--color-main);
        opacity: 1;
    }
}
</style>
