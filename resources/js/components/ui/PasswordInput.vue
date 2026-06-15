<script setup>
import { ref, computed } from "vue";

const props = defineProps({
    modelValue: { type: String, default: "" },
    id: { type: String, default: null },
    placeholder: { type: String, default: "••••••••" },
    autocomplete: { type: String, default: "current-password" },
    hasError: { type: Boolean, default: false },
    icon: { type: String, default: "lock" },
});

const emit = defineEmits(["update:modelValue"]);

const visible = ref(false);
const inputType = computed(() => (visible.value ? "text" : "password"));

function onInput(event) {
    emit("update:modelValue", event.target.value);
}

function toggleVisibility() {
    visible.value = !visible.value;
}
</script>

<template>
    <div
        class="password-input"
        :class="{ 'password-input--error': hasError }"
    >
        <i class="material-symbols-rounded password-input__icon">{{
            icon
        }}</i>
        <input
            :id="id"
            class="password-input__field"
            :type="inputType"
            :value="modelValue"
            :placeholder="placeholder"
            :autocomplete="autocomplete"
            @input="onInput"
        />
        <button
            type="button"
            class="password-input__toggle"
            :aria-label="
                visible
                    ? 'Masquer le mot de passe'
                    : 'Afficher le mot de passe'
            "
            @click="toggleVisibility"
        >
            <i class="material-symbols-rounded">{{
                visible ? "visibility_off" : "visibility"
            }}</i>
        </button>
    </div>
</template>

<style lang="scss" scoped>
.password-input {
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

    &--error {
        border-color: #c0392b;

        &:focus-within {
            box-shadow: 0 0 0 3px rgba(192, 57, 43, 0.08);
        }
    }

    &__icon {
        padding: 0 0.55rem;
        font-size: 0.95rem;
        color: var(--color-main);
        opacity: 0.38;
        flex-shrink: 0;
    }

    &__field {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        padding: 0.58rem 0.35rem 0.58rem 0;
        font-family: var(--font-body);
        font-size: 0.84rem;
        color: var(--color-text);
        min-width: 0;

        &::placeholder {
            color: var(--color-text);
            opacity: 0.28;
        }
    }

    &__toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        margin-right: 0.15rem;
        padding: 0;
        border: none;
        background: transparent;
        border-radius: var(--radius-sm);
        cursor: pointer;
        color: var(--color-main);
        opacity: 0.45;
        transition:
            opacity var(--transition),
            background-color var(--transition),
            color var(--transition);

        i {
            font-size: 1.05rem;
        }

        &:hover {
            opacity: 0.85;
            background: rgba(15, 43, 76, 0.06);
        }

        &:focus-visible {
            outline: 2px solid var(--color-main);
            outline-offset: 1px;
            opacity: 1;
        }
    }
}
</style>
