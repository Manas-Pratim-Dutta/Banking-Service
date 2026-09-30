import api from "../../utils/http/axios";

export async function fetchPartyCategory(
    partyTypeId: string
) {

    const response = await api.post(
        "/get-party-category",
        {
            party_type_id: partyTypeId
        }
    );

    return response.data;
}

export async function addParty(
    payload: any
) {

    const response =
        await api.post(
            "/add-party",
            payload
        );

    return response.data;

}

export async function setExemptionUnder88(
    value: string
) {

    const response = await api.post(
        "/set-exemption-under-88",
        {
            exemption_under_88: value
        }
    );

    return response.data;

}