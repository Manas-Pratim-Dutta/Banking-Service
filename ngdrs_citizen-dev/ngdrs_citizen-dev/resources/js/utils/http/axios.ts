import axios from "axios";

const appBasePath = document.querySelector<HTMLMetaElement>('meta[name="app-base-path"]')?.content ?? "";

const api = axios.create({
  baseURL: `${appBasePath}/api`,
  headers: {
    "X-Requested-With": "XMLHttpRequest",
    "Content-Type": "application/json",
  },
  timeout: 20000,
});

export default api;
