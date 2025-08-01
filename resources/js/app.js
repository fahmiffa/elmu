import Alpine from 'alpinejs';
import 'trix';
import 'trix/dist/trix.css';
import { layout, dataTable, dropdownSelect, editor, generateBill } from './custom.js';

window.Alpine = Alpine;
Alpine.data('dropdownSelect', dropdownSelect);
Alpine.data('layout', layout);
Alpine.data('editor', editor);
Alpine.data('dataTable', dataTable);
Alpine.data('generateBill', generateBill);
Alpine.store("unit");

Alpine.start();
