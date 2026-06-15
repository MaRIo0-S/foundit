<script setup>
defineProps({
    status: { type: String, required: true },
    label: { type: String, default: null },
});

const statusMap = {
    available: { label: "Disponible", class: "status--available" },
    claimed: { label: "Réclamé", class: "status--claimed" },
    returned: { label: "Restitué", class: "status--returned" },
    pending: { label: "En attente", class: "status--pending" },
    approved: { label: "Approuvée", class: "status--approved" },
    rejected: { label: "Rejetée", class: "status--rejected" },
};

function resolve(status) {
    return statusMap[status] ?? { label: status, class: "" };
}
</script>

<template>
    <span class="status-badge" :class="resolve(status).class">
        {{ label ?? resolve(status).label }}
    </span>
</template>

<style lang="scss" scoped>
.status-badge {
    display: inline-flex;
    align-items: center;
    font-family: var(--font-body);
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.2rem 0.6rem;
    border-radius: var(--radius-lg);
    letter-spacing: 0.02em;
    white-space: nowrap;
}

.status--available,
.status--approved {
    background-color: rgba(39, 174, 96, 0.12);
    color: #1a6b3c;
}

.status--claimed,
.status--pending {
    background-color: rgba(234, 179, 8, 0.15);
    color: #854d0e;
}

.status--returned {
    background-color: rgba(15, 43, 76, 0.08);
    color: var(--color-main);
}

.status--rejected {
    background-color: rgba(220, 38, 38, 0.1);
    color: #991b1b;
}
</style>
