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
    return {
        search: "",
        sortColumn: "name",
        sortAsc: true,
        currentPage: 1,
        perPage: 10,
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
                Object.values(row).some((val) => {
                    return String(val)
                        .toLowerCase()
                        .includes(this.search.toLowerCase());
                })
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

export const dataTableReg = (data) => {
    console.log(data);
    return {
        search: "",
        sortColumn: "name",
        sortAsc: true,
        currentPage: 1,
        perPage: 10,
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
                row.murid.name
                    .toLowerCase()
                    .includes(this.search.toLowerCase())
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
    console.log(data);
    return {
        search: "",
        sortColumn: "name",
        sortAsc: true,
        currentPage: 1,
        perPage: 10,
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
                row.reg.murid.name
                    .toLowerCase()
                    .includes(this.search.toLowerCase())
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
                .map((e) => ({ value: e.program.id, label: e.program.name }));
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

// export function schedule(data) {
//     console.log(data);
//     return {
//         selectedUnit: "",
//         selectedPrgram: "",
//         selectedKelas: "",
//         tom: null,

//         get programs() {
//             let val = [];
//             if (!this.selectedUnit)
//                 return [{ value: "", label: "Pilih Program" }];
//             val = data
//                 .filter((p) => p.unit == Number(this.selectedUnit) && p.kelas == Number(this.selectedKelas))
//                 .map((e) => ({
//                     value: e.programs.id,
//                     label: e.programs.name,
//                 }))
//                 .filter(
//                     (item, index, self) =>
//                         index === self.findIndex((t) => t.value === item.value)
//                 );

//             if (val.length < 1) return [{ value: "", label: "Pilih Program" }];

//             return [
//                 { value: "", label: "Pilih Program" },
//                 ...Array.from(val.values()),
//             ];
//         },

//         get kelas() {
//             let val = [];
//             if (!this.selectedUnit)
//                 return [{ value: "", label: "Pilih Kelas" }];
//             val = data
//                 .filter((p) => p.unit == Number(this.selectedUnit))
//                 .map((e) => ({
//                     value: e.class.id,
//                     label: e.class.name,
//                 }))
//                 .filter(
//                     (item, index, self) =>
//                         index === self.findIndex((t) => t.value === item.value)
//                 );

//             if (val.length < 1) return [{ value: "", label: "Pilih Kelas" }];

//             return [
//                 { value: "", label: "Pilih Kelas" },
//                 ...Array.from(val.values()),
//             ];
//         },

//         init() {
//             this.$nextTick(() => {
//                 this.initTomSelect();

//                 this.$watch("selectedUnit", () => {
//                     this.selectedPrgram = ""; // reset program
//                     this.updateOptions();
//                 });

//                 this.$watch("selectedPrgram", () => {
//                     this.updateOptions();
//                 });
//             });
//         },

//         initTomSelect() {
//             const selectEl = this.$refs.selectMurid;

//             if (selectEl.tomselect) {
//                 this.tom = selectEl.tomselect;
//                 return;
//             }

//             this.tom = new TomSelect(selectEl, {
//                 plugins: ["remove_button"],
//                 placeholder: "Pilih Murid",
//                 options: this.getFilteredMurid(),
//                 items: [],
//             });
//         },

//         getFilteredMurid() {
//             if (!this.selectedUnit || !this.selectedPrgram) return [];

//             return data
//                 .filter(
//                     (p) =>
//                         Number(p.unit) === Number(this.selectedUnit) &&
//                         Number(p.program) === Number(this.selectedPrgram)
//                 )
//                 .map((e) => ({
//                     value: e.murid.id,
//                     text: e.murid.name,
//                 }));
//         },

//         updateOptions() {
//             if (!this.tom) return;
//             this.tom.clear();
//             const newOptions = this.getFilteredMurid();
//             this.tom.clearOptions();
//             this.tom.addOptions(newOptions);
//             this.tom.refreshOptions(false);
//         },
//     };
// }

export function schedule(data, initial = {}) {
    return {
        selectedUnit: initial.unit || "",
        selectedKelas: initial.kelas || "",
        selectedProgram: initial.program || "",
        selectedMurid: initial.murid || [],
        tom: null,

        get programs() {
            if (!this.selectedUnit || !this.selectedKelas)
                return [{ value: "", label: "Pilih Program" }];

            const filtered = data
                .filter(
                    (p) =>
                        p.unit == this.selectedUnit &&
                        p.kelas == this.selectedKelas
                )
                .map((p) => ({ value: p.programs.id, label: p.programs.name }));

            const unique = Array.from(
                new Map(filtered.map((i) => [i.value, i])).values()
            );

            return [{ value: "", label: "Pilih Program" }, ...unique];
        },

        get kelas() {
            if (!this.selectedUnit)
                return [{ value: "", label: "Pilih Kelas" }];

            const filtered = data
                .filter((p) => p.unit == this.selectedUnit)
                .map((p) => ({ value: p.class.id, label: p.class.name }));

            const unique = Array.from(
                new Map(filtered.map((i) => [i.value, i])).values()
            );

            return [{ value: "", label: "Pilih Kelas" }, ...unique];
        },

        init() {
            this.$nextTick(() => {
                this.initTomSelect();

                this.$watch("selectedUnit", () => {
                    this.selectedProgram = "";
                    this.updateOptions();
                });

                this.$watch("selectedKelas", () => {
                    this.selectedProgram = "";
                    this.updateOptions();
                });

                this.$watch("selectedProgram", () => {
                    this.updateOptions();
                });
            });
        },

        initTomSelect() {
            const el = this.$refs.selectMurid;

            if (el.tomselect) {
                this.tom = el.tomselect;
                return;
            }

            this.tom = new TomSelect(el, {
                plugins: ["remove_button"],
                placeholder: "Pilih Murid",
                options: this.getFilteredMurid(),
                items: this.selectedMurid,
            });
        },

        getFilteredMurid() {
            if (!this.selectedUnit || !this.selectedProgram) return [];

            return data
                .filter(
                    (p) =>
                        p.unit == this.selectedUnit &&
                        p.program == this.selectedProgram
                )
                .map((p) => ({
                    value: p.id,
                    text: p.murid.name,
                }));
        },

        updateOptions() {
            if (!this.tom) return;

            this.tom.clear();
            const newOptions = this.getFilteredMurid();

            this.tom.clearOptions();
            this.tom.addOptions(newOptions);

            if (this.selectedMurid.length) {
                this.tom.setValue(this.selectedMurid);
            }

            this.tom.refreshOptions(false);
        },
    };
}

export function jadwal(par) {
    console.log(par)
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
                        "meta[name=csrf-token]"
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
                        "meta[name=csrf-token]"
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
                (month) => dataByYear[month]?.bayar || 0
            );
            const belumData = categories.map(
                (month) => dataByYear[month]?.belum || 0
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
