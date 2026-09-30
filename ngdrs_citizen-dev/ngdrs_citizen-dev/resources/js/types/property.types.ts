interface PropertyDynamicFieldsResponse {
    success: boolean;
    message: string;
    sections: Record<string, string> | null;
    showLocationCard: boolean;
    showDagPattaContainer: boolean;
}

interface PropertyFieldsResult {
    success: boolean;
    message: string;
    sections: Record<string, string> | null;
    config: {
        showLocationCard: boolean;
        showDagPattaContainer: boolean;
        dagPattaInputMode: DagPattaInputMode;
    };
}

interface SubdivisionResult {
    success: boolean;
    message: string;
    subdivisions: Record<string, string> | null;
}

interface CircleResult {
    success: boolean;
    message: string;
    circle: Record<string, string> | null;
}

interface MoujaResult {
    success: boolean;
    message: string;
    mouja: Record<string, string> | null;
}
interface VillageResult {
    success: boolean;
    message: string;
    village: Record<string, string> | null;
}
interface AreaTypeResult {
    success: boolean;
    message: string;
    areaType: Record<string, string> | null;
}

interface LocationData {
    district_id: number;
    district_name: string;

    subdivision_id: number;
    subdivision_name: string;

    circle_id: number;
    circle_name: string;

    moza_id: number;
    mouza_name: string;

    village_id: number;
    village_name: string;

    land_type_id: number;
    land_type: string;

    dag_list: string[];
    patta_no: string;

    noc_date: string;
}

interface PropertyPageData {
    nocFlag: boolean;
    nocNo: string;
}

interface TomSelectElement extends HTMLSelectElement {
    tomselect?: {
        setValue: (value: string, silent?: boolean) => void;
    };
}

interface PropertyConfig {
    showLocationCard: boolean;
    showDagPattaContainer: boolean;
    dagPattaInputMode: DagPattaInputMode;
}

type DagPattaInputMode = "VERIFY" | "MANUAL";

interface SavedProperty {
    dag_no: number;
    patta_no: number;
    area_bigha: string;
    area_kattha: string;
    area_lessa: string;
    patta_type_id: string;
    zonal_rate: string;
    land_valuation: string;
    zone_name: string;
    zone_class: string;
    dharitree_class: string;
}

 interface SavedPropertyResponse {
    success: boolean;
    data?: SavedProperty[];
    message?: string;
}