import md5 from "blueimp-md5";

export const layout = () => {
    return {
        sidebarOpen: true,
        modal: null,
        init() {
            this.sidebarOpen = localStorage.getItem("sidebarOpen") === "true";
            this.modal = this.modalHandler();
        },
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            localStorage.setItem("sidebarOpen", this.sidebarOpen);
            if (!isMobileDevice) {
            }
        },
        toggleSidebarMobile() {
            this.sidebarOpen = !this.sidebarOpen;
        },
        closeSidebarOnMobile() {
            if (window.innerWidth < 768) {
                this.sidebarOpen = false;
            }
        },
        md5Component(da) {
            return md5(da);
        },
        modalHandler() {
            return {
                activeModal: null,
                openModal(id) {
                    this.activeModal = id;
                    document.body.classList.add("overflow-hidden");
                },
                closeModal() {
                    this.activeModal = null;
                    document.body.classList.remove("overflow-hidden");
                },
            };
        },
    };
};

export const dataTable = (data) => {
    console.log(data)
    return {
        search: "",
        sortColumn: "name",
        sortAsc: true,
        currentPage: 1,
        perPage: 50,
        rows: data,
        selectedRow: null,
        open: false,

        sortBy(column) {
            if (this.sortColumn === column) {
                this.sortAsc = !this.sortAsc;
            } else {
                this.sortColumn = column;
                this.sortAsc = true;
            }
        },

        filteredData() {
            let temp = this.rows.filter((row) =>
                Object.values(row).some((val) =>
                    String(val)
                        .toLowerCase()
                        .includes(this.search.toLowerCase())
                )
            );

            temp.sort((a, b) => {
                let valA = a[this.sortColumn];
                let valB = b[this.sortColumn];

                if (typeof valA === "string") valA = valA.toLowerCase();
                if (typeof valB === "string") valB = valB.toLowerCase();

                if (valA < valB) return this.sortAsc ? -1 : 1;
                if (valA > valB) return this.sortAsc ? 1 : -1;
                return 0;
            });

            return temp;
        },

        paginatedData() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredData().slice(start, start + this.perPage);
        },

        totalPages() {
            return Math.ceil(this.filteredData().length / this.perPage);
        },

        nextPage() {
            if (this.currentPage < this.totalPages()) this.currentPage++;
        },

        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        },

        deleteRow(e) {
            if (confirm("Yakin ingin menghapus data?")) {
                e.target.submit();
            }
        },
        formatNumber(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },
    };
};

import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";

export function dropdownSelect() {
    return {
        init() {
            new TomSelect(this.$el, {
                plugins: ["remove_button"],
                placeholder: "Pilih Item",
            });
        },
    };
}

export function editor() {
    return {
        content: "",

        exec(command) {
            document.execCommand(command, false, null);
            this.updateContent();
        },

        updateContent() {
            this.content = this.$refs.editor.innerHTML;
        },
    };
}

export function generateBill() {
    return {
        selectedMonth: new Date().getMonth() + 1,
        progress: 0,
        jobId: null,
        interval: null,

        submitForm() {
            const token = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");

            fetch("/dashboard/bill", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": token,
                },
                body: JSON.stringify({
                    bulan: this.selectedMonth,
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    this.jobId = data.job_id;
                    this.pollProgress();
                })
                .catch((error) => {
                    console.error("Error:", error);
                });
        },

        pollProgress() {
            this.interval = setInterval(() => {
                fetch(`/dashboard/job-progress/${this.jobId}`)
                    .then((res) => res.json())
                    .then((data) => {
                        this.progress = data.progress;

                        if (this.progress >= 100) {
                            clearInterval(this.interval);
                        }
                    });
            }, 1000);
        },
    };
}

export function reg(kelas, program, unit) {
    const val = kelas.map((e) => ({ value: e.id, label: e.name }));
    return {
        selectedKelas: "",
        optionsKelas: [{ value: "", label: "Pilih Kelas" }, ...val],
        selectedProgram: "",
        selectedUnit: "",
        get filteredPrograms() {
            if (!this.selectedKelas)
                return [{ value: "", label: "Pilih Program" }];
            return program
                .filter((p) => p.kelas == Number(this.selectedKelas))
                .map((e) => ({ value: e.id, label: e.program.name }));
        },
        get filteredUnits() {
            if (!this.selectedKelas)
                return [{ value: "", label: "Pilih Unit" }];
            return unit
                .filter((p) => p.kelas_id == Number(this.selectedKelas))
                .map((e) => ({ value: e.unit.id, label: e.unit.name }));
        },
    };
}

export function jadwal() {
    return {
        pertemuanList: [
            {
                nama: "Pertemuan 1",
                tanggalList: [{ tanggal: "2025-08-15T10:00", materi: "" }],
            },
        ],
        addPertemuan() {
            this.pertemuanList.push({
                nama: "",
                tanggalList: [],
            });
        },
        addTanggal(index) {
            this.pertemuanList[index].tanggalList.push({
                tanggal: "",
                materi: "",
            });
        },
        formatWIB(datetimeStr) {
            if (!datetimeStr) return "";
            const date = new Date(datetimeStr);
            return (
                date.toLocaleString("id-ID", {
                    timeZone: "Asia/Jakarta",
                    day: "numeric",
                    month: "long",
                    year: "numeric",
                    hour: "2-digit",
                    minute: "2-digit",
                }) + " WIB"
            );
        },
    };
}
