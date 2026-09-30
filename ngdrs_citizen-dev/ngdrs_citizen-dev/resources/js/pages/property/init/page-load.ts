import {
    setPropertyClassification,
    setFetchNoc,
} from "../../../services/property/action";
import { getPageData } from "../state/page-data";

export function initNocWorkflow() {
    console.log("initNocWorkflow called.");
    const pageData = getPageData();
    if (pageData.nocFlag) {
        if (pageData.nocNo != null) {
            setPropertyClassification();
            setTimeout(() => {
                setFetchNoc(pageData.nocNo);
            }, 2000);
        }
    }
}
