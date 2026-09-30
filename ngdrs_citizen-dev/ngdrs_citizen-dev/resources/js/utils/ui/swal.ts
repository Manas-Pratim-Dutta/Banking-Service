import Swal from "sweetalert2";

export const swalConfirm = (
    title: string,
    text: string,
    confirmButtonText = "Yes",
    cancelButtonText = "Cancel",
) => {
    return Swal.fire({
        title,
        text,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#2563eb",
        cancelButtonColor: "#dc2626",
        confirmButtonText,
        cancelButtonText,
    });
};

export const swalSuccess = (message: string) => {
    return Swal.fire({
        icon: "success",
        title: "Success",
        text: message,
        timer: 2000,
        showConfirmButton: true,
        confirmButtonColor: "#FF803B",
    });
};

export const swalError = (message: string) => {
    return Swal.fire({
        icon: "error",
        title: "Error",
        text: message,
        timer: 2000,
        showConfirmButton: true,
        confirmButtonColor: "#FF803B",
    });
};

(window as any).swalSuccess = swalSuccess;
(window as any).swalError = swalError;
(window as any).swalConfirm = swalConfirm;
