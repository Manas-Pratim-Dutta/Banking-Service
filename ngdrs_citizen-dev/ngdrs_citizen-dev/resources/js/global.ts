import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";

// export function initTomSelect() {
//     document.querySelectorAll("select.tom-select").forEach((el) => {
//         const select = el as HTMLSelectElement & { tomselect?: TomSelect };

//         if (!select.tomselect) {
//             new TomSelect(select, {
//                 create: false,
//                 maxOptions: 1000,
//             });
//         }
//     });
// }
export function initTomSelect(container?: HTMLElement) {
    const scope = container ?? document;

    scope.querySelectorAll("select.tom-select").forEach((el) => {
        const select = el as HTMLSelectElement & { tomselect?: TomSelect };

        if (!select.tomselect) {
            new TomSelect(select, {
                create: false,
                maxOptions: 1000,
                dropdownParent: "body"
            });
        }
    });
}
