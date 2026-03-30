import TomSelect from "tom-select";

/**
 * Alpine.js component: raportForm
 * @param {Array}  students  - list of students
 * @param {Array}  units     - list of units
 * @param {Array}  kelas     - list of kelas
 * @param {Array}  programs  - list of programs
 * @param {Array}  teaches   - list of teachers
 * @param {Object} initial   - pre-filled values for edit mode
 */
export function raportForm(students, units, kelas, programs, teaches, initial = {}) {
    return {
        // --- data list ---
        students,
        units,
        kelas,
        programs,
        teaches,

        // --- filter state ---
        unitFilter: initial.unitFilter || "",
        kelasFilter: initial.kelasFilter || "",
        programFilter: initial.programFilter || "",

        // --- selected student ---
        studentId: initial.studentId || "",

        // --- auto-filled fields ---
        program: initial.program || "",
        levelPeriod: initial.levelPeriod || "",

        // --- TomSelect instances ---
        tomMurid: null,
        tomUnit: null,
        tomProgram: null,
        tomKelas: null,
        tomTeacher: null,

        init() {
            // Initialize Unit Search
            this.tomUnit = new TomSelect(this.$refs.selectUnit, {
                placeholder: "-- Semua Unit --",
                allowEmptyOption: true,
                onChange: (val) => {
                    this.unitFilter = val;
                },
            });

            // Initialize Program Search
            this.tomProgram = new TomSelect(this.$refs.selectProgram, {
                placeholder: "-- Semua Program --",
                allowEmptyOption: true,
                onChange: (val) => {
                    this.programFilter = val;
                },
            });

            // Initialize Kelas Search
            this.tomKelas = new TomSelect(this.$refs.selectKelas, {
                placeholder: "-- Semua Kelas --",
                allowEmptyOption: true,
                onChange: (val) => {
                    this.kelasFilter = val;
                },
            });

            // Initialize Teacher Search
            this.tomTeacher = new TomSelect(this.$refs.selectTeacher, {
                placeholder: "-- Pilih Pengajar --",
                allowEmptyOption: true,
                create: true, // Allow manual typing if teacher not in list
            });

            // Initialize Student Search
            this.tomMurid = new TomSelect(this.$refs.selectMurid, {
                placeholder: "-- Pilih Murid --",
                searchField: ["text"],
                onChange: (val) => {
                    this.studentId = val;
                    this.updateStudentData();
                },
            });

            // On edit mode, pre-select filters based on current student
            if (this.studentId) {
                const student = this.students.find(
                    (s) => String(s.user) === String(this.studentId),
                );
                if (student) {
                    this.unitFilter   = String(student.unit_id    || "");
                    this.kelasFilter  = String(student.kelas_id   || "");
                    this.programFilter = String(student.program_id || "");
                    
                    // Update TomSelect values
                    this.tomUnit.setValue(this.unitFilter);
                    this.tomKelas.setValue(this.kelasFilter);
                    this.tomProgram.setValue(this.programFilter);
                }
                this.$nextTick(() => {
                    this.refreshMuridOptions();
                    this.refreshTeacherOptions();
                    
                    // Pre-fill teacher if initial exists
                    if (initial.teacher) {
                        this.tomTeacher.addOption({value: initial.teacher, text: initial.teacher});
                        this.tomTeacher.setValue(initial.teacher);
                    }
                    
                    this.tomMurid.setValue(this.studentId);
                });
            } else {
                this.refreshMuridOptions();
                this.refreshTeacherOptions();
            }

            // Re-filter dropdown when filters change
            this.$watch("unitFilter",  (val) => {
                this.refreshMuridOptions();
                this.refreshTeacherOptions();
            });
            this.$watch("kelasFilter", () => this.refreshMuridOptions());
            this.$watch("programFilter", () => this.refreshMuridOptions());
        },

        refreshMuridOptions() {
            if (!this.tomMurid) return;

            this.tomMurid.clearOptions();

            const filtered = this.students.filter((s) => {
                const unitMatch =
                    !this.unitFilter ||
                    String(s.unit_id) === String(this.unitFilter);
                const kelasMatch =
                    !this.kelasFilter ||
                    String(s.kelas_id) === String(this.kelasFilter);
                const programMatch =
                    !this.programFilter ||
                    String(s.program_id) === String(this.programFilter);
                return unitMatch && kelasMatch && programMatch;
            });

            const options = filtered.map((s) => ({
                value: String(s.user),
                text : s.nama_panggilan
                    ? `${s.name} (${s.nama_panggilan})`
                    : s.name,
            }));

            this.tomMurid.addOptions(options);

            // Clear selection if current student is no longer in filtered list
            if (
                this.studentId &&
                !filtered.find((s) => String(s.user) === String(this.studentId))
            ) {
                this.tomMurid.clear();
                this.studentId  = "";
                this.program    = "";
                this.levelPeriod = "";
            }
        },

        refreshTeacherOptions() {
            if (!this.tomTeacher) return;
            
            const currentVal = this.tomTeacher.getValue();
            this.tomTeacher.clearOptions();

            const filtered = this.teaches.filter((t) => {
                return !this.unitFilter || String(t.unit_id) === String(this.unitFilter);
            });

            const options = filtered.map((t) => ({
                value: t.name,
                text : t.name
            }));

            this.tomTeacher.addOptions(options);
            
            // Restore value if it's still there or manually created
            if (currentVal) {
                if (!options.find(o => o.value === currentVal)) {
                    this.tomTeacher.addOption({value: currentVal, text: currentVal});
                }
                this.tomTeacher.setValue(currentVal);
            }
        },

        updateStudentData() {
            const student = this.students.find(
                (s) => String(s.user) === String(this.studentId),
            );
            if (student) {
                this.program     = student.program_name || "";
                this.levelPeriod = student.grade_name   || "";
            }
        },
    };
}



