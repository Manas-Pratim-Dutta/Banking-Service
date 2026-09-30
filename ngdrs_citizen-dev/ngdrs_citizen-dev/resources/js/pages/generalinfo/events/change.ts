
import { handleArticleChange, handleCircleChange, handleCourtCaseTypeChange, handleDeedWriterChange, handleDistrictChange, handleExecutionTypeChange, handleHomeVisitChange } from "../handlers/main";

const changeHandlers: Record<string, (el: HTMLElement) => void | Promise<void>> = {
    "article-id": handleArticleChange,
    "district_id": handleDistrictChange,
    "taluka_id": handleCircleChange,
    "document-execution-type": handleExecutionTypeChange,
    "court-case":handleCourtCaseTypeChange,
    "deed-writer-type": handleDeedWriterChange,
    "is_home_visit":handleHomeVisitChange
};

export async function handleChange(event: Event) {
    const target = event.target as HTMLElement;

    if (!target.id) return;

    const handler = changeHandlers[target.id];
    if (handler) {
        await handler(target);
    }
}
