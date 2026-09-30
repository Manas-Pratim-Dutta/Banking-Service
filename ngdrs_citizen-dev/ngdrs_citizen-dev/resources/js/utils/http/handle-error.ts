import { notyf } from "../ui/notyf";

export function handleError(error: any) {
  const message =
    error?.response?.data?.message ||
    error?.message ||
    "Something went wrong";

  notyf.error(message);
}