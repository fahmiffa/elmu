import md5 from "blueimp-md5";

export function trixEditor() {
    return {
        content: "",
        updateTimer: null,
        eventHandlers: [],

        init() {
            const trixEditorElement = this.$refs.trix;
            const inputElement = this.$refs.input;

            if (!trixEditorElement || !inputElement) {
                console.error("Trix editor or input element not found");
                return;
            }

            // Debounced update function
            const updateContent = () => {
                if (this.updateTimer) clearTimeout(this.updateTimer);

                this.updateTimer = setTimeout(() => {
                    if (trixEditorElement && inputElement) {
                        inputElement.value = trixEditorElement.value;
                        this.content = trixEditorElement.value;
                    }
                }, 50);
            };

            // Setup event listeners
            const setupListeners = () => {
                const handlers = [
                    { event: "trix-change", handler: updateContent },
                    {
                        event: "trix-initialize",
                        handler: () => console.log("Trix initialized"),
                    },
                ];

                handlers.forEach(({ event, handler }) => {
                    trixEditorElement.addEventListener(event, handler);
                    this.eventHandlers.push({
                        element: trixEditorElement,
                        event,
                        handler,
                    });
                });
            };

            setupListeners();
        },

        showContent() {
            if (this.$refs.trix) {
                this.content = this.$refs.trix.value;
            }
        },

        clear() {
            if (this.$refs.trix && this.$refs.trix.editor) {
                this.$refs.trix.editor.loadHTML("");
                this.content = "";
            }
        },

        destroy() {
            // Cleanup event listeners
            this.eventHandlers.forEach(({ element, event, handler }) => {
                element.removeEventListener(event, handler);
            });
            this.eventHandlers = [];

            if (this.updateTimer) {
                clearTimeout(this.updateTimer);
            }
        },
    };
}

export function videoForm(initialRole = "") {
    return {
        role: initialRole,
        error: "",
        progress: 0,
        uploading: false,
        success: false, // 🔥 notifikasi sukses

        validateFile(event) {
            this.error = "";
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 4 * 1024 * 1024) {
                this.error = "File maksimal 4MB";
                event.target.value = "";
                return;
            }

            const ext = file.name.split(".").pop().toLowerCase();
            if (ext !== "mp4") {
                this.error = "Format file harus MP4";
                event.target.value = "";
                return;
            }
        },

        upload(event) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);

            this.uploading = true;
            this.progress = 0;

            const xhr = new XMLHttpRequest();
            xhr.open(form.method, form.action);

            xhr.upload.onprogress = (e) => {
                if (e.lengthComputable) {
                    this.progress = Math.round((e.loaded / e.total) * 100);
                }
            };

            xhr.onload = () => {
                this.uploading = false;

                if (xhr.status >= 200 && xhr.status < 300) {
                    this.success = true;

                    // 🔥 Tampilkan notifikasi 1.5 detik, lalu redirect
                    setTimeout(() => {
                        window.location.href = "/dashboard/video";
                    }, 1500);
                } else {
                    this.error = "Upload gagal.";
                }
            };

            xhr.onerror = () => {
                this.uploading = false;
                this.error = "Upload error.";
            };

            xhr.send(formData);
        },
    };
}

export function materiForm({ initialRole }) {
    return {
        role: initialRole,
        error: "",
        progress: 0,
        uploading: false,
        success: false,

        validateFile(event) {
            this.error = "";
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                this.error = "File maksimal 2MB";
                event.target.value = "";
                return;
            }

            if (!file.name.toLowerCase().endsWith(".pdf")) {
                this.error = "Format file harus PDF";
                event.target.value = "";
                return;
            }
        },

        upload(e) {
            const form = e.target.closest("form");
            const formData = new FormData(form);

            this.uploading = true;
            this.progress = 0;

            const xhr = new XMLHttpRequest();
            xhr.open(form.method, form.action);

            xhr.upload.addEventListener("progress", (evt) => {
                if (evt.lengthComputable) {
                    this.progress = Math.round((evt.loaded / evt.total) * 100);
                }
            });

            xhr.onload = () => {
                this.uploading = false;

                if (xhr.status >= 200 && xhr.status < 300) {
                    this.success = true;

                    setTimeout(() => {
                        window.location.href = "/dashboard/master/materi";
                    }, 1500);
                } else {
                    this.error = "Upload gagal.";
                }
            };

            xhr.onerror = () => {
                this.uploading = false;
                this.error = "Terjadi error saat upload.";
            };

            xhr.send(formData);
        },
    };
}

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
    console.log(data);
    return {
        search: "",
        filterUnit: "",
        filterProgram: "",
        filterRole: "",
        sortColumn: "name",
        sortAsc: true,
        currentPage: 1,
        perPage: 10,
        rows: data,
        selectedRow: null,
        open: false,
        modalOpen: false,
        selectedItem: null,

        openModal(item) {
            this.selectedItem = item;
            this.modalOpen = true;
        },
        closeModal() {
            this.modalOpen = false;
            this.selectedItem = null;
        },

        sortBy(column) {
            if (this.sortColumn === column) {
                this.sortAsc = !this.sortAsc;
            } else {
                this.sortColumn = column;
                this.sortAsc = true;
            }
        },

        filteredData() {
            let temp = this.rows.filter((row) => {
                const keyword = this.search.toLowerCase();
                const name = (row.name ?? row.users?.name ?? "").toLowerCase();
                const panggilan = (
                    row.nama_panggilan ??
                    row.users?.data?.nama_panggilan ??
                    ""
                ).toLowerCase();
                const email = (row.email ?? "").toLowerCase();
                const matchesSearch =
                    name.includes(keyword) ||
                    panggilan.includes(keyword) ||
                    email.includes(keyword);

                const matchesUnit =
                    this.filterUnit === "" || row.unit == this.filterUnit;
                const matchesProgram =
                    this.filterProgram === "" ||
                    row.program == this.filterProgram;

                const matchesRole =
                    this.filterRole === "" || row.role == this.filterRole;

                return (
                    matchesSearch &&
                    matchesUnit &&
                    matchesProgram &&
                    matchesRole
                );
            });

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
            if (this.perPage === "all") {
                return this.filteredData();
            }

            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredData().slice(
                start,
                start + Number(this.perPage),
            );
        },

        totalPages() {
            if (this.perPage === "all") return 1;
            return Math.ceil(this.filteredData().length / Number(this.perPage));
        },

        nextPage() {
            if (this.currentPage < this.totalPages()) this.currentPage++;
        },

        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        },

        resetPage() {
            this.currentPage = 1;
        },

        deleteRow(e) {
            if (confirm("Yakin ingin menghapus data?")) {
                e.target.submit();
            }
        },
        formatNumber(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
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
        groupJadwalByDay(jadwalArray) {
            return jadwalArray.reduce((groups, item) => {
                (groups[item.parse] = groups[item.parse] || []).push(item);
                return groups;
            }, {});
        },
    };
};

export const dataTableReg = (data) => {
    return {
        search: "",
        filterUnit: "",
        filterProgram: "",
        sortColumn: "name",
        sortAsc: true,
        currentPage: 1,
        perPage: 10,
        rows: data,
        selectedRow: null,
        modalOpen: false,
        selectedItem: null,

        openModal(item) {
            this.selectedItem = item;
            this.modalOpen = true;
        },
        closeModal() {
            this.modalOpen = false;
            this.selectedItem = null;
        },

        sortBy(column) {
            if (this.sortColumn === column) {
                this.sortAsc = !this.sortAsc;
            } else {
                this.sortColumn = column;
                this.sortAsc = true;
            }
        },

        filteredData() {
            let temp = this.rows.filter((row) => {
                const keyword = this.search.toLowerCase();
                const name = (row.murid?.name ?? "").toLowerCase();
                const panggilan = (
                    row.murid?.nama_panggilan ?? ""
                ).toLowerCase();
                const matchesSearch =
                    name.includes(keyword) || panggilan.includes(keyword);

                const matchesUnit =
                    this.filterUnit === "" || row.unit == this.filterUnit;
                const matchesProgram =
                    this.filterProgram === "" ||
                    row.program == this.filterProgram;

                return matchesSearch && matchesUnit && matchesProgram;
            });

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
            if (this.perPage === "all") {
                return this.filteredData();
            }
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredData().slice(start, start + this.perPage);
        },

        totalPages() {
            if (this.perPage === "all") return 1;
            return Math.ceil(this.filteredData().length / Number(this.perPage));
        },

        nextPage() {
            if (this.currentPage < this.totalPages()) this.currentPage++;
        },

        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        },

        resetPage() {
            this.currentPage = 1; // Reset page saat perPage berubah
        },

        deleteRow(e) {
            if (confirm("Yakin ingin menghapus data?")) {
                e.target.submit();
            }
        },

        formatNumber(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
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
};

export const dataTablePay = (data) => {
    return {
        search: "",
        filterUnit: "",
        filterProgram: "",
        sortColumn: "name",
        sortAsc: true,
        currentPage: 1,
        perPage: 10,
        rows: data,
        selectedRow: null,
        startDate: "",
        endDate: "",
        tabStatus: "tagihan",

        sortBy(column) {
            if (this.sortColumn === column) {
                this.sortAsc = !this.sortAsc;
            } else {
                this.sortColumn = column;
                this.sortAsc = true;
            }
        },

        filteredData() {
            let temp = this.rows.filter((row) => {
                const keyword = this.search.toLowerCase();
                const name = (row.reg?.murid?.name ?? "").toLowerCase();
                const panggilan = (
                    row.reg?.murid?.nama_panggilan ?? ""
                ).toLowerCase();
                const matchesSearch =
                    name.includes(keyword) || panggilan.includes(keyword);

                const matchesUnit =
                    this.filterUnit === "" || row.reg.unit == this.filterUnit;
                const matchesProgram =
                    this.filterProgram === "" ||
                    row.reg.program == this.filterProgram;

                let matchesDate = true;
                if (this.startDate || this.endDate) {
                    const rowDate = new Date(row.created_at);
                    rowDate.setHours(0, 0, 0, 0);

                    if (this.startDate) {
                        const start = new Date(this.startDate);
                        start.setHours(0, 0, 0, 0);
                        if (rowDate < start) matchesDate = false;
                    }
                    if (this.endDate) {
                        const end = new Date(this.endDate);
                        end.setHours(23, 59, 59, 999);
                        if (rowDate > end) matchesDate = false;
                    }
                }

                let matchesStatus = true;
                if (this.tabStatus === "riwayat") {
                    matchesStatus = row.status == 1;
                } else {
                    matchesStatus = row.status != 1;
                }

                return (
                    matchesSearch &&
                    matchesUnit &&
                    matchesProgram &&
                    matchesDate &&
                    matchesStatus
                );
            });

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
            if (this.perPage === "all") {
                return this.filteredData();
            }

            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredData().slice(
                start,
                start + Number(this.perPage),
            );
        },

        totalPages() {
            if (this.perPage === "all") return 1;
            return Math.ceil(this.filteredData().length / Number(this.perPage));
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

export function paket(data = []) {
    return {
        kelas: "",
        selectedId: "",
        fields: data,
        addField() {
            if (!this.selectedId) return;
            if (this.fields.find((f) => f.id == this.selectedId)) {
                return;
            }
            this.fields.push({
                id: this.selectedId,
                value: "",
                name: this.getSelectedText(),
            });
        },
        removeField(index) {
            this.fields.splice(index, 1);
        },
        getSelectedText() {
            const select = this.$refs.kelasSelect;
            return select.options[select.selectedIndex].text;
        },
        formatCurrency(value) {
            const numberString = value.replace(/[^,\d]/g, "");
            const split = numberString.split(",");
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            const ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                const separator = sisa ? "." : "";
                rupiah += separator + ribuan.join(".");
            }

            rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
            return rupiah;
        },
        formatFieldValue(index) {
            this.fields[index].value = this.formatCurrency(
                this.fields[index].value,
            );
        },
    };
}

export function currencyInput(initialValue = "") {
    const cleanValue = (initialValue || "").toString().replace(/\D/g, "");
    return {
        display: formatNumber(cleanValue),
        raw: cleanValue,

        formatInput() {
            const number = this.display.replace(/\D/g, "");
            this.raw = number;
            this.display = formatNumber(number);
        },
    };

    function formatNumber(value) {
        if (!value) return "";
        return value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
}

export function reg(kelas, initial = {}) {
    const val = kelas.map((e) => ({ value: e.id, label: e.name }));
    return {
        selectedKelas: "",
        optionsKelas: [{ value: "", label: "Pilih Kelas" }, ...val],
        selectedProgram: "",
        selectedUnit: [{ value: "", label: "Pilih Unit" }],
        get filteredPrograms() {
            if (!this.selectedKelas)
                return [{ value: "", label: "Pilih Program" }];

            var program = kelas
                .filter(
                    function (p) {
                        return p.id == Number(this.selectedKelas);
                    }.bind(this),
                )
                .flatMap(function (e) {
                    return e.program.map(function (unit) {
                        return {
                            value: unit.id,
                            label: unit.name,
                        };
                    });
                });

            return [{ value: "", label: "Pilih Program" }, ...program];
        },
        get filteredUnits() {
            if (!this.selectedKelas)
                return [{ value: "", label: "Pilih Unit" }];

            var units = kelas
                .filter(
                    function (p) {
                        return p.id == Number(this.selectedKelas);
                    }.bind(this),
                )
                .flatMap(function (e) {
                    return e.units.map(function (unit) {
                        return {
                            value: unit.id,
                            label: unit.name,
                        };
                    });
                });

            return [{ value: "", label: "Pilih Unit" }, ...units];
        },
    };
}

export function schedule(data, initial = {}) {
    return {
        selectedUnit: initial.unit || "",
        selectedJadwal: initial.jadwal || "",
        selectedMurid: initial.murid || [],
        tom: null,
        tomJadwal: null,

        get jadwals() {
            if (!this.selectedUnit)
                return [{ value: "", label: "Pilih Jadwal" }];

            const found = data.find((p) => p.unit == this.selectedUnit);

            if (!found || !found.units || !Array.isArray(found.units.jadwal)) {
                return [{ value: "", label: "Pilih Jadwal" }];
            }

            const jadwalList = found.units.jadwal.map((j) => ({
                value: j.id,
                label: `${j.parse} - ${j.name} (${j.start.slice(
                    0,
                    5,
                )} - ${j.end.slice(0, 5)})`,
            }));

            return [{ value: "", label: "Pilih Jadwal" }, ...jadwalList];
        },

        init() {
            this.$nextTick(() => {
                this.initTomSelect();

                this.$watch("selectedUnit", () => {
                    this.selectedJadwal = initial.jadwal;
                    this.updateOptions();
                });
            });
        },

        getFilteredJadwal() {
            if (!this.selectedUnit) return [];

            const found = data.find((p) => p.unit == this.selectedUnit);

            if (!found || !found.units || !Array.isArray(found.units.jadwal))
                return [];

            return found.units.jadwal.map((j) => ({
                value: j.id,
                text: `${j.parse} - ${j.name} (${j.start.slice(
                    0,
                    5,
                )} - ${j.end.slice(0, 5)})`,
            }));
        },

        initTomSelect() {
            const muridEl = this.$refs.selectMurid;
            const jadwalEl = this.$refs.selectJadwal;

            if (muridEl.tomselect) {
                this.tom = muridEl.tomselect;
            } else {
                this.tom = new TomSelect(muridEl, {
                    plugins: ["remove_button"],
                    placeholder: "Pilih Murid",
                    options: this.getFilteredMurid(),
                    items: this.selectedMurid,
                });
            }

            if (jadwalEl.tomselect) {
                this.tomJadwal = jadwalEl.tomselect;
            } else {
                this.tomJadwal = new TomSelect(jadwalEl, {
                    plugins: ["remove_button"],
                    placeholder: "Pilih Jadwal",
                    options: this.getFilteredJadwal(),
                    items: this.selectedJadwal,
                });
            }
        },

        getFilteredMurid() {
            if (!this.selectedUnit) return [];

            return data
                .filter((p) => p.unit == this.selectedUnit)
                .map((p) => ({
                    value: p.id,
                    text: `${p.murid.name} (${p.programs.name})`,
                }));
        },

        updateOptions() {
            if (this.tom) {
                this.tom.clear();
                const newMuridOptions = this.getFilteredMurid();
                this.tom.clearOptions();
                this.tom.addOptions(newMuridOptions);

                if (this.selectedMurid.length) {
                    this.tom.setValue(this.selectedMurid);
                }
                this.tom.refreshOptions(false);
            }

            if (this.tomJadwal) {
                this.tomJadwal.clear();
                const newJadwalOptions = this.getFilteredJadwal();
                this.tomJadwal.clearOptions();
                this.tomJadwal.addOptions(newJadwalOptions);

                if (this.selectedJadwal && this.selectedJadwal.length) {
                    this.tomJadwal.setValue(this.selectedJadwal);
                }
                this.tomJadwal.refreshOptions(false);
            }
        },
    };
}

export function jadwal(par) {
    console.log(par);
    return {
        pertemuanList: par ?? [
            {
                nama: "Pertemuan 1",
                tanggalList: [{ tanggal: "2025-08-15T10:00" }],
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

import Chart from "@toast-ui/chart";
import "@toast-ui/chart/dist/toastui-chart.min.css";

export function salesChart(par, reg) {
    return {
        selectedMonth: new Date().getMonth() + 1,
        selectedYear: new Date().getFullYear(),
        months: [
            "Januari",
            "Februari",
            "Maret",
            "April",
            "Mei",
            "Juni",
            "Juli",
            "Agustus",
            "September",
            "Oktober",
            "November",
            "Desember",
        ],

        async fetchData(actionUrl) {
            const method = "GET";
            fetch(actionUrl, {
                method,
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        "meta[name=csrf-token]",
                    ),
                },
            })
                .then((res) => res.json())
                .then((da) => {
                    this.years = da.Year;
                    this.dummyData = da.data;
                    this.$nextTick(() => {
                        this.renderChart();
                    });
                });
        },

        chartInstance: null,

        updateChart() {
            this.renderChart();
        },

        renderChart() {
            const dataByYear = this.dummyData[this.selectedYear] || {};
            const categories = Object.keys(dataByYear);
            const total = Object.values(dataByYear);

            const chartData = {
                categories: categories,
                series: [
                    {
                        name: par,
                        data: total,
                    },
                ],
            };

            const options = {
                chart: {
                    width: 700,
                    height: 400,
                    title: par,
                    // title: `Grafik Penjualan Tahun ${this.selectedYear}`,
                },
                xAxis: {
                    title: "Bulan",
                },
                yAxis: {
                    title: "Jumlah",
                },
                series: {
                    verticalAlign: true,
                },
                responsive: {
                    animation: true,
                },
            };

            const container = document.getElementById(reg);
            container.innerHTML = "";

            this.chartInstance = Chart.columnChart({
                el: container,
                data: chartData,
                options,
            });
        },
    };
}

export function payChart(par, reg) {
    return {
        selectedMonth: new Date().getMonth() + 1,
        selectedYear: new Date().getFullYear(),
        months: [
            "Januari",
            "Februari",
            "Maret",
            "April",
            "Mei",
            "Juni",
            "Juli",
            "Agustus",
            "September",
            "Oktober",
            "November",
            "Desember",
        ],

        async fetchData(actionUrl) {
            const method = "GET";
            fetch(actionUrl, {
                method,
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        "meta[name=csrf-token]",
                    ),
                },
            })
                .then((res) => res.json())
                .then((da) => {
                    this.years = da.Year;
                    this.dummyData = da.data;
                    this.$nextTick(() => {
                        this.renderChart();
                    });
                });
        },

        chartInstance: null,

        updateChart() {
            this.renderChart();
        },

        renderChart() {
            const dataByYear = this.dummyData[this.selectedYear] || {};
            const categories = Object.keys(dataByYear);
            const total = Object.values(dataByYear);

            const bayarData = categories.map(
                (month) => dataByYear[month]?.bayar || 0,
            );
            const belumData = categories.map(
                (month) => dataByYear[month]?.belum || 0,
            );

            const chartData = {
                categories: categories,
                series: [
                    {
                        name: "Bayar",
                        data: bayarData,
                    },
                    {
                        name: "Belum Bayar",
                        data: belumData,
                    },
                ],
            };

            const options = {
                chart: {
                    width: 700,
                    height: 400,
                    title: par,
                    // title: `Grafik Penjualan Tahun ${this.selectedYear}`,
                },
                xAxis: {
                    title: "Bulan",
                },
                yAxis: {
                    title: "Jumlah",
                },
                series: {
                    verticalAlign: true,
                },
                responsive: {
                    animation: true,
                },
            };

            const container = document.getElementById(reg);
            container.innerHTML = "";

            this.chartInstance = Chart.columnChart({
                el: container,
                data: chartData,
                options,
            });
        },
    };
}

export function countUp(target) {
    return {
        display: "0",
        current: 0,
        target: target,
        duration: 1000, // in ms
        steps: 60,
        stepValue: 0,

        start() {
            this.stepValue = this.target / this.steps;
            let interval = this.duration / this.steps;
            let counter = setInterval(() => {
                this.current += this.stepValue;
                if (this.current >= this.target) {
                    this.current = this.target;
                    clearInterval(counter);
                }
                this.display = this.formatNumber(Math.floor(this.current));
            }, interval);
        },

        formatNumber(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },
    };
}

export function jadwalForm(initialJadwals = null) {
    console.log(initialJadwals);
    return {
        jadwals: initialJadwals || [
            { id: "", name: "", hari: "", start_time: "", end_time: "" },
        ],

        addJadwal() {
            this.jadwals.push({
                id: null,
                name: "",
                hari: "",
                start_time: "",
                end_time: "",
            });
        },

        removeJadwal(index) {
            this.jadwals.splice(index, 1);
        },
    };
}
