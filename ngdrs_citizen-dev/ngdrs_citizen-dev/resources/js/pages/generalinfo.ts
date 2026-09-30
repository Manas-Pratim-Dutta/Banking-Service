// import axios from "axios";
// import { notyf } from "../utils/ui/notyf";

// document.addEventListener("DOMContentLoaded", () => {
//     const articleEl = document.getElementById("article-id") as HTMLSelectElement | null;
//     const docTitleInput = document.getElementById("document-title") as HTMLInputElement | null;
//     const wrapper = document.getElementById("doc-title-wrapper") as HTMLDivElement | null;
//     const district = document.getElementById("district_id") as HTMLSelectElement;
//     const circle = document.getElementById("taluka_id") as HTMLSelectElement;
//     const office = document.getElementById("office_id") as HTMLSelectElement;

//     articleEl?.addEventListener("change", async () => {
//         const articleId = articleEl.value;

//         if (!articleId) {
//             wrapper?.classList.add("hidden");

//             if (docTitleInput) {
//                 docTitleInput.value = "";
//             }

//             return;
//         }

//         try {
//             const response = await axios.get(`/api/document-title/${articleId}`);
//             const result = response.data;

//             if (result.title) {
//                 if (docTitleInput) {
//                     docTitleInput.value = result.title;
//                 }

//                 wrapper?.classList.remove("hidden");
//             } else {
//                 wrapper?.classList.add("hidden");

//                 if (docTitleInput) {
//                     docTitleInput.value = "";
//                 }
//             }
//         } catch (error) {
//             // console.error(error);
//             // console.log(error);
//             // console.log(error.response);
//             // console.log(error.response?.data);
//             // console.log(error.response?.status);
//             notyf.error("Failed to load document title");

//             wrapper?.classList.add("hidden");

//             if (docTitleInput) {
//                 docTitleInput.value = "";
//             }
//         }
//     });

//     //main
//     district?.addEventListener("change", async () => {
//         const districtId = district.value;

//         console.log("District selected:", districtId);

//         if (!circle) return;

//         // circle.innerHTML = `<option>Loading...</option>`;
//         const tomCircle = (circle as any).tomselect;

     
//         tomCircle.clear();
//         tomCircle.clearOptions();
       

//         if (office) {
//             const tomOffice = (office as any).tomselect;
//             tomOffice.clear();
//             tomOffice.clearOptions();
//             // tomOffice.sync();
//         }


//         if (!districtId) {
//             circle.innerHTML = `<option value="">Select Taluka</option>`;
//             return;
//         }

//         try {
//             const response = await axios.get(`/api/get-talukas/${districtId}`);
//             const talukas = response.data;
//             console.log(talukas);

//             talukas.forEach((item: any) => {
//                 tomCircle.addOption({
//                     value: item.taluka_id,
//                     text: item.name
//                 });
//             });

//             tomCircle.refreshOptions(false);
//             // tom.sync();

//         } catch (error: any) {
//             console.log(error?.response);
//             notyf.error("Failed to load talukas");
//         }
//     });

//     // district?.addEventListener("change", async () => {

//     //     const districtId = district.value;

//     //     console.log("District selected:", districtId);

//     //     const tomCircle = (circle as any).tomselect;
//     //     const tomOffice = (office as any).tomselect;

//     //     // 🔥 HARD RESET (only once)
//     //     tomCircle.clear(true);
//     //     tomCircle.clearOptions();
//     //     tomCircle.options = {};        // 🔥 important
//     //     tomCircle.refreshOptions(true);

//     //     tomOffice.clear(true);
//     //     tomOffice.clearOptions();
//     //     tomOffice.options = {};        // 🔥 important
//     //     tomOffice.refreshOptions(true);

//     //     if (!districtId) return;

//     //     try {
//     //         const response = await axios.get(`/api/get-talukas/${districtId}`);
//     //         const talukas = response.data;

//     //         console.log("NEW TALUKAS:", talukas);

//     //         talukas.forEach((item: any) => {
//     //             tomCircle.addOption({
//     //                 value: item.taluka_id,
//     //                 text: item.name.trim() // 🔥 remove trailing spaces
//     //             });
//     //         });

//     //         tomCircle.refreshOptions(false);

//     //     } catch (error: any) {
//     //         console.log(error?.response);
//     //         notyf.error("Failed to load talukas");
//     //     }
//     // });
   
   
//     circle?.addEventListener("change", async () => {
//         const talukaId = circle.value;

//         console.log("Taluka selected:", talukaId);

//         if (!office) return;

//         const tom = (office as any).tomselect;

        
//         tom.clear();
//         tom.clearOptions();
        
//         if (!talukaId) return;

//         try {
//             const response = await axios.get(`/api/get-offices/${talukaId}`);
//             const offices = response.data;

//             console.log("Offices:", offices);

//             offices.forEach((item: any) => {
//                 tom.addOption({
//                     value: item.office_id,
//                     text: item.name
//                 });
//             });

//             tom.refreshOptions(false);
//             // tom.sync();

//         } catch (error: any) {
//             console.log(error?.response);
//             notyf.error("Failed to load offices");
//         }
//     });
// });