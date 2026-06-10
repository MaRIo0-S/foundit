import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { createPinia } from "pinia";
import piniaPluginPersistedstate from "pinia-plugin-persistedstate";
import { Toaster } from "vue-sonner";
import MainLayout from "./layouts/MainLayout.vue";
import "../css/app.scss";

import { ZiggyVue } from "../../vendor/tightenco/ziggy";
const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);

const pages = import.meta.glob("./pages/**/*.vue", { eager: false });

createInertiaApp({
    resolve: async (name) => {
        const page = await pages[`./pages/${name}.vue`]();
        page.default.layout = page.default.layout || MainLayout;
        return page;
    },

    setup({ el, App, props, plugin }) {
        createApp({
            render: () => [
                h(App, props),
                h(Toaster, {
                    position: "top-right",
                    duration: 3500,
                    expand: true,
                    visibleToasts: 5,
                }),
            ],
        })
            .use(plugin)
            .use(pinia)
            .use(ZiggyVue)

            .mount(el);
    },

    progress: {
        color: "#e8410a",
    },

    defaults: {
        visitOptions: () => ({ viewTransition: true }),
    },
});
