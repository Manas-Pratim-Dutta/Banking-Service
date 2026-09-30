import type * as JQuery from "jquery";
import type { Notyf } from "notyf";

declare global {
    interface Window {
        $: typeof JQuery;
        jQuery: typeof JQuery;

        swalSuccess: (message: string) => void;
        swalError: (message: string) => void;
        swalConfirm: (title: string, text: string) => Promise<any>;
        triggerChatbotError: (error) => void;
        resetChatbot: () => void;
        notyf: Notyf;
        showAlertModal: (id: string, options?: { title?: string; description?: string; confirmText?: string; cancelText?: string }) => Promise<boolean>;
        closeAlertModal: (id: string, result: boolean) => void;
    }
}
declare const marked: any;
export {};
