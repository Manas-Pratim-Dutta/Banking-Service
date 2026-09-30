import "axios";

declare module "axios" {
  export interface AxiosRequestConfig {
    skipLoader?: boolean;
    loadingMessage?: string;
    loadingSubtext?: string;
  }
}