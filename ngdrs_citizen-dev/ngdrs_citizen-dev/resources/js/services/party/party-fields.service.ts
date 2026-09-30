import axios from "axios";

export async function getPartyFields() {

    const response =
        await axios.post(
            "/api/get-party-fields"
        );

    return response.data;
}