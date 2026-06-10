<script setup>
import { Toaster } from "vue-sonner";
</script>

<template>
    <Toaster position="top-right" :duration="3500" expand :visibleToasts="5" />
</template>

<style>
[data-sonner-toaster] {
    position: fixed !important;
    top: 1.25rem !important;
    right: 1.25rem !important;
    left: auto !important;
    bottom: auto !important;
    width: 340px !important;
    z-index: 9999 !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 0.5rem !important;
    pointer-events: none !important;
}

[data-sonner-toast] {
    pointer-events: all !important;
    position: relative !important;
    width: 100% !important;
    display: flex !important;
    flex-direction: row !important;
    align-items: flex-start !important;
    gap: 0.65rem !important;
    padding: 0.85rem 1rem !important;
    border-radius: 8px !important;
    border: none !important;
    box-shadow: 0 4px 20px rgba(15, 43, 76, 0.13) !important;
    font-family: "DM Sans", sans-serif !important;
    font-size: 0.84rem !important;
    line-height: 1.45 !important;
    cursor: default !important;
    transform: none !important;
    transition:
        opacity 0.2s ease,
        transform 0.2s ease !important;
    background-color: #ffffff !important;
    color: #1a1a2e !important;
    box-sizing: border-box !important;
}

[data-sonner-toast][data-type="success"] {
    border-left: 4px solid #27ae60 !important;
    background-color: #ffffff !important;
}

[data-sonner-toast][data-type="error"] {
    border-left: 4px solid #c0392b !important;
    background-color: #ffffff !important;
}

[data-sonner-toast] [data-icon] {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    width: 18px !important;
    height: 18px !important;
    margin-top: 1px !important;
}

[data-sonner-toast][data-type="success"] [data-icon] svg {
    color: #27ae60 !important;
    width: 18px !important;
    height: 18px !important;
}

[data-sonner-toast][data-type="error"] [data-icon] svg {
    color: #c0392b !important;
    width: 18px !important;
    height: 18px !important;
}

[data-sonner-toast] [data-content] {
    display: flex !important;
    flex-direction: column !important;
    gap: 0.15rem !important;
    flex: 1 !important;
    min-width: 0 !important;
}

[data-sonner-toast] [data-title] {
    font-family: "DM Sans", sans-serif !important;
    font-size: 0.84rem !important;
    font-weight: 600 !important;
    color: #1a1a2e !important;
    line-height: 1.4 !important;
    margin: 0 !important;
    padding: 0 !important;
    background: none !important;
}

[data-sonner-toast][data-type="success"] [data-title] {
    color: #1a5c35 !important;
}

[data-sonner-toast][data-type="error"] [data-title] {
    color: #7b1d1d !important;
}

[data-sonner-toast] [data-description] {
    font-family: "DM Sans", sans-serif !important;
    font-size: 0.78rem !important;
    font-weight: 400 !important;
    color: #1a1a2e !important;
    opacity: 0.65 !important;
    margin: 0 !important;
    padding: 0 !important;
}

[data-sonner-toast] [data-close-button] {
    position: absolute !important;
    top: 0.5rem !important;
    right: 0.5rem !important;
    width: 20px !important;
    height: 20px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: transparent !important;
    border: none !important;
    cursor: pointer !important;
    color: #1a1a2e !important;
    opacity: 0.35 !important;
    border-radius: 4px !important;
    padding: 0 !important;
    transition: opacity 0.15s ease !important;
}

[data-sonner-toast] [data-close-button]:hover {
    opacity: 0.7 !important;
    background-color: rgba(15, 43, 76, 0.07) !important;
}

[data-sonner-toast] [data-close-button] svg {
    width: 12px !important;
    height: 12px !important;
}
</style>
