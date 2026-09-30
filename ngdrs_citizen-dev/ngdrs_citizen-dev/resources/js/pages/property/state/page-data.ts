export function getPageData(): PropertyPageData {
    const element = document.getElementById("property-page-data");

    return element
        ? JSON.parse(element.textContent || "{}")
        : {
              nocFlag: false,
              nocNo: "",
          };
}

export let propFetchedFromNoc = false;

export function setPropFetchedFromNoc(value: boolean): void {
    propFetchedFromNoc = value;
}

export function getPropFetchedFromNoc(): boolean {
    return propFetchedFromNoc;
}