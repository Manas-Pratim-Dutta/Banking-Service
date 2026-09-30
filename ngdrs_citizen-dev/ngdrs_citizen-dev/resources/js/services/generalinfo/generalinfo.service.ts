import api from "../../utils/http/axios";

export async function fetchDocumentTitle(articleId: string) {
    try {
        const { data } = await api.get(`/document-title/${articleId}`);
        console.log("Document Title",data);
        return { success: true, title: data.title ,titleId: data.title_id};
    } catch (error: any) {
        return {
            success: false,
            message: error?.response?.data?.message || "Failed to load document title"
        };
    }
}

export async function fetchTalukas(districtId: string) {
    try {
        const { data } = await api.get(`/get-talukas/${districtId}`);
        console.log("Talukas",data);
        
        return { success: true, talukas: data };
    } catch (error: any) {
        return {
            success: false,
            message: error?.response?.data?.message || "Failed to load talukas"
        };
    }
}

export async function fetchOffices(talukaId: string) {
    try {
        const { data } = await api.get(`/get-offices/${talukaId}`);
        console.log("Offices",data);
        return { success: true, offices: data };
    } catch (error: any) {
        return {
            success: false,
            message: error?.response?.data?.message || "Failed to load offices"
        };
    }
}
