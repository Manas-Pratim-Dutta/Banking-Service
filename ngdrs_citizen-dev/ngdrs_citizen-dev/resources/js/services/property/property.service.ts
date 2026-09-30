import axios from "axios";
import api from "../../utils/http/axios";

// export async function fetchPropertyFields(
//     areaTypeId: string,
//     propTypeId: string,
// ): Promise<PropertyFieldsResult> {
//     try {
//         const { data } = await api.get<PropertyDynamicFieldsResponse>(
//             "/property-type-change",
//             {
//                 params: {
//                     area_type_id: areaTypeId,
//                     property_type_id: propTypeId,
//                 },
//             },
//         );
//         // console.log('result::x:', data);
//         if (!data?.success) {
//             return {
//                 success: false,
//                 message: data.message,
//                 sections: null,
//                 showLocationCard: null,
//                 showDagPattaContainer: null,
//                 config: null
//             };
//         }

//         return {
//             success: true,
//             message: data.message,
//             sections: data.sections ?? null,
//             showLocationCard: data.showLocationCard,
//             showDagPattaContainer: data.showDagPattaContainer,
//         };
//     } catch (error: any) {
//         const message =
//             error?.response?.data?.message ||
//             error?.message ||
//             "Something went wrong";

//         return {
//             success: false,
//             message: message,
//             sections: null,
//             showLocationCard: null,
//             showDagPattaContainer: null,
//         };
//     }
// }

export async function fetchPropertyFields(
    areaTypeId: string,
    propTypeId: string,
): Promise<PropertyFieldsResult> {
    const { data } = await api.get<PropertyFieldsResult>(
        "/property-type-change",
        {
            params: {
                area_type_id: areaTypeId,
                property_type_id: propTypeId,
            },
        },
    );

    return data;
}

export async function fetchSubdivisions(
    districtId: string,
): Promise<SubdivisionResult> {
    console.log(districtId);
    try {
        const { data } = await api.get<SubdivisionResult>(
            "/fetch-subdivisions",
            {
                params: {
                    district_id: districtId,
                },
            },
        );

        if (!data?.success) {
            return {
                success: false,
                message: data.message,
                subdivisions: null,
            };
        }
        // console.log(data);return;

        return {
            success: true,
            message: data.message,
            subdivisions: data.subdivisions,
        };
    } catch (error: any) {
        const message =
            error?.response?.data?.message ||
            error?.message ||
            "Something went wrong";
        return {
            success: false,
            message: message,
            subdivisions: null,
        };
    }
}

export async function getNocDetails(
    nocno: string,
    propertyId: string,
): Promise<any> {
    try {
        // console.log('reached');return;
        const { data } = await api.get("/fetch-noc-details", {
            params: {
                noc_no: nocno,
                property_type_id: propertyId,
            },
        });
        return data;
    } catch (error: any) {
        const message =
            error?.response?.data?.message ||
            error?.message ||
            "Something went wrong";
        return { success: false, message: message };
    }
}

export async function fetchCircle(
    districtId: string,
    subDivId: string,
): Promise<CircleResult> {
    console.log(subDivId);
    try {
        const { data } = await api.get<CircleResult>("/fetch-circle", {
            params: {
                district_id: districtId,
                subdiv_id: subDivId,
            },
        });

        if (!data?.success) {
            return {
                success: false,
                message: data.message,
                circle: null,
            };
        }
        // console.log(data);return;

        return {
            success: true,
            message: data.message,
            circle: data.circle,
        };
    } catch (error: any) {
        const message =
            error?.response?.data?.message ||
            error?.message ||
            "Something went wrong";
        return {
            success: false,
            message: message,
            circle: null,
        };
    }
}

export async function fetchMouja(
    districtId: string,
    subDivId: string,
    circleId: string,
): Promise<MoujaResult> {
    console.log(subDivId);
    try {
        const { data } = await api.get<MoujaResult>("/fetch-mouja", {
            params: {
                district_id: districtId,
                subdiv_id: subDivId,
                circle_id: circleId,
            },
        });

        if (!data?.success) {
            return {
                success: false,
                message: data.message,
                mouja: null,
            };
        }
        // console.log(data);return;

        return {
            success: true,
            message: data.message,
            mouja: data.mouja,
        };
    } catch (error: any) {
        const message =
            error?.response?.data?.message ||
            error?.message ||
            "Something went wrong";
        return {
            success: false,
            message: message,
            mouja: null,
        };
    }
}

export async function fetchVillage(
    districtId: string,
    subDivId: string,
    circleId: string,
    moujaId: string,
): Promise<VillageResult> {
    console.log(subDivId);
    try {
        const { data } = await api.get<VillageResult>("/fetch-village", {
            params: {
                district_id: districtId,
                subdiv_id: subDivId,
                circle_id: circleId,
                mouja_id: moujaId,
            },
        });

        if (!data?.success) {
            return {
                success: false,
                message: data.message,
                village: null,
            };
        }
        // console.log(data);return;

        return {
            success: true,
            message: data.message,
            village: data.village,
        };
    } catch (error: any) {
        const message =
            error?.response?.data?.message ||
            error?.message ||
            "Something went wrong";
        return {
            success: false,
            message: message,
            village: null,
        };
    }
}

export async function fetchAreaType(
    districtId: string,
    subDivId: string,
    circleId: string,
    moujaId: string,
    villageId: string,
): Promise<AreaTypeResult> {
    try {
        const { data } = await api.get<AreaTypeResult>("/fetch-area-type", {
            params: {
                district_id: districtId,
                subdiv_id: subDivId,
                circle_id: circleId,
                mouja_id: moujaId,
                village_id: villageId,
            },
        });

        if (!data?.success) {
            return {
                success: false,
                message: data.message,
                areaType: null,
            };
        }
        // console.log(data);return;

        return {
            success: true,
            message: data.message,
            areaType: data.areaType,
        };
    } catch (error: any) {
        const message =
            error?.response?.data?.message ||
            error?.message ||
            "Something went wrong";
        return {
            success: false,
            message: message,
            areaType: null,
        };
    }
}

export async function verifyDagApi(payload: {
    propertyId: string;
    areaTypeId: string;
    districtId: string;
    subDivId: string;
    circleId: string;
    moujaId: string;
    villageId: string;
    dagNo: string;
}): Promise<any> {
    try {
        const { data } = await api.get<any>("/verify-dag", { params: payload });

        if (!data?.success) {
            return {
                success: false,
                message: data.message,
                pattaNo: null,
            };
        }

        return {
            success: true,
            message: data.message,
            pattaNo: data.patta,
        };
    } catch (error: any) {
        const message =
            error?.response?.data?.message ||
            error?.message ||
            "Something went wrong";
        return {
            success: false,
            message: message,
            pattaNo: null,
        };
    }
}

export async function saveProperty(payload) {
    try {
        const { data } = await api.post<any>("/save-property", {
            params: payload,
        });
        return data;
    } catch (error: any) {
        const message =
            error?.response?.data?.message ||
            error?.message ||
            "Something went wrong";
        return {
            success: false,
            message: message,
        };
    }
}

export async function postManualProperty(payload) {
    try {
        const { data } = await api.post<any>("/save-manual-property", {
            params: payload,
        });
        return data;
    } catch (error: any) {
        const message =
            error?.response?.data?.message ||
            error?.message ||
            "Something went wrong";
        return {
            success: false,
            message: message,
        };
    }
}

export async function getSavedProperty(): Promise<SavedPropertyResponse> {
    try {
        const { data } = await api.get<SavedPropertyResponse>(
            "/get-saved-property",
            {},
        );
        return data;
    } catch (error: any) {
        const message =
            error?.response?.data?.message ||
            error?.message ||
            "Something went wrong";
        return { success: false, message: message };
    }
}

export async function fetchDagDetails(propertyData) {
    try {
        const { data } = await api.get("/get-dag-details", {
            params: propertyData,
        });

        return data;
    } catch (error: any) {
        const message =
            error?.response?.data?.message ||
            error?.message ||
            "Something went wrong";
        return { success: false, message: message };
    }
}
