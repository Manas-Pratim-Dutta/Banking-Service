import api from "./axios";
import { showLoader, hideLoader } from "../ui/loader";

api.interceptors.request.use(
  (config) => {

    const token = document
      .querySelector('meta[name="csrf-token"]')
      ?.getAttribute("content");

    if (token) {
      config.headers["X-CSRF-TOKEN"] = token;
    }

    if (!config.skipLoader) {
      showLoader(config.loadingMessage, config.loadingSubtext);
    }

    return config;
  },
  (error) => {
    hideLoader();
    return Promise.reject(error);
  }
);

api.interceptors.response.use(
  (response) => {
    hideLoader();
    return response;
  },
  (error) => {
    hideLoader();

    if (error.response?.status === 401) {
      window.location.href = "/login";
    } 

    return Promise.reject(error);
  }
);