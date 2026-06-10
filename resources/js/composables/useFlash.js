import { usePage } from "@inertiajs/vue3";
import { toast } from "vue-sonner";
import { watch } from "vue";

export function useFlash() {
    const page = usePage();

    watch(
        () => page.props.flash,
        (flash) => {
            if (flash?.success) toast.success(flash.success);
            if (flash?.error) toast.error(flash.error);
        },
        { immediate: true, deep: true },
    );
}
