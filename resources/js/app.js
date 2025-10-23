import Alpine from "alpinejs";
import "trix";
import "trix/dist/trix.css";
import {
    layout,
    dataTable,
    dropdownSelect,
    editor,
    generateBill,
    reg,
    jadwal,
    salesChart,
    payChart,
    countUp,
    schedule,
    dataTableReg,
    dataTablePay,
    jadwalForm,
    currencyInput,
    paket
} from "./custom.js";

window.Alpine = Alpine;
Alpine.data("dropdownSelect", dropdownSelect);
Alpine.data("layout", layout);
Alpine.data("editor", editor);
Alpine.data("dataTable", dataTable);
Alpine.data("dataTableReg", dataTableReg);
Alpine.data("dataTablePay", dataTablePay);
Alpine.data("generateBill", generateBill);
Alpine.data("jadwal", jadwal);
Alpine.data("reg", reg);
Alpine.data('salesChart', salesChart);
Alpine.data('payChart', payChart);
Alpine.data('countUp', countUp);
Alpine.data('schedule', schedule);
Alpine.data('jadwalForm', jadwalForm);
Alpine.data('currencyInput', currencyInput);
Alpine.data('paket', paket);
Alpine.store("unit");

Alpine.start();
