#!/usr/bin/env python3
"""
Yarim tayyor mahsulot va xomashyoni solish-ortish (yuklash-tushirish) ishchilarining
mehnat xavfsizligini inson omili va ergonomik yuklanishlarni kompleks baholovchi dastur
(Alyuminiy profillarini ishlab chiqaruvchilari misolida).

Ishga tushirish:  python3 app.py
Talablar: Python 3.9+ (tkinter va sqlite3 standart kutubxona bilan birga keladi,
qo'shimcha paket o'rnatish shart emas).
"""

from __future__ import annotations

import tkinter as tk
from tkinter import messagebox, ttk
from typing import Any

import engine
import storage

ALUMINUM_EXAMPLE: dict[str, Any] = {
    "employee_name": "Aliyev Anvar",
    "worker_gender": "erkak",
    "employee_age": "34",
    "experience_years": "4",
    "operation_type": "qolda",
    "load_description": "6 metrli alyuminiy profil bog'lami (ekstruziya sexidan omborga)",
    "load_mass_kg": "22",
    "lifts_per_shift": "180",
    "lifts_per_minute": "4",
    "shift_duration_hours": "8",
    "duration_category": "long",
    "horizontal_distance_cm": "30",
    "vertical_location_cm": "75",
    "vertical_travel_cm": "60",
    "asymmetry_angle_deg": "30",
    "coupling_quality": "fair",
    "posture_type": "davriy_noqulay",
    "static_load_kgs": "25000",
    "body_inclinations_per_shift": "150",
    "walking_distance_km": "6",
    "attention_concentration_percent": "40",
    "signals_per_hour": "60",
    "responsibility_level": "jamoa_uchun",
    "monotony_operations_count": "5",
    "night_shift": False,
    "temperature_c": "34",
    "metal_dust_mg_m3": "5",
    "noise_level_db": "82",
    "uses_ppe": True,
    "training_completed": True,
    "last_training_at": "",
    "fatigue_self_score": "3",
    "health_group": "soglom",
    "prior_incidents_count": "0",
    "notes": "",
}

GENDER_OPTIONS = [("erkak", "Erkak"), ("ayol", "Ayol")]
OPERATION_OPTIONS = [
    ("qolda", "Qo'lda (mexanizatsiyalashmagan)"),
    ("yarim_mexanizatsiya", "Yarim mexanizatsiyalashgan"),
    ("mexanizatsiya", "To'liq mexanizatsiyalashgan"),
]
DURATION_OPTIONS = [
    ("short", "Qisqa (<= 1 soat)"),
    ("moderate", "O'rta (<= 2 soat)"),
    ("long", "Uzoq (<= 8 soat)"),
]
COUPLING_OPTIONS = [
    ("good", "Yaxshi (dastak/tutqich bor)"),
    ("fair", "O'rtacha"),
    ("poor", "Yomon (silliq/qulaysiz yuzalar)"),
]
POSTURE_OPTIONS = [
    ("erkin", "Erkin, qulay"),
    ("epizodik_noqulay", "Epizodik noqulay holat"),
    ("davriy_noqulay", "Davriy noqulay/majburiy holat"),
    ("majburiy", "Ko'p vaqt majburiy holat"),
    ("qattiq_majburiy", "Qattiq majburiy, cheklangan harakat"),
]
RESPONSIBILITY_OPTIONS = [
    ("ozi_uchun", "Faqat o'z ishi uchun"),
    ("jamoa_uchun", "Jamoa natijasi uchun"),
    ("xavfsizlik_uchun", "Boshqalar xavfsizligi uchun"),
]
FATIGUE_OPTIONS = [(str(i), label) for i, label in enumerate(
    ["1 — charchamagan", "2", "3 — o'rtacha", "4", "5 — juda charchagan"], start=1
)]
HEALTH_OPTIONS = [
    ("soglom", "Sog'lom"),
    ("cheklangan", "Tibbiy cheklovlar mavjud"),
    ("nogironligi_bor", "Nogironligi bor"),
]

# (bo'lim sarlavhasi, [ (kalit, yorliq, tur, {qo'shimcha}) ])
FIELD_SECTIONS: list[tuple[str, list[tuple[str, str, str, dict]]]] = [
    ("1. Umumiy ma'lumot", [
        ("employee_name", "Xodim F.I.Sh. (ixtiyoriy)", "entry", {}),
        ("worker_gender", "Jinsi", "combo", {"options": GENDER_OPTIONS}),
        ("employee_age", "Yoshi", "entry", {}),
        ("experience_years", "Ish staji (yil)", "entry", {}),
        ("operation_type", "Mexanizatsiya darajasi", "combo", {"options": OPERATION_OPTIONS}),
        ("load_description", "Yuk tavsifi", "entry", {}),
    ]),
    ("2. Yuk ko'tarish parametrlari (NIOSH tenglamasi)", [
        ("load_mass_kg", "Yuk massasi, kg", "entry", {}),
        ("lifts_per_shift", "Smena davomida ko'tarishlar soni", "entry", {}),
        ("lifts_per_minute", "Chastota, ko'tarish/daqiqa", "entry", {}),
        ("shift_duration_hours", "Smena davomiyligi, soat", "entry", {}),
        ("duration_category", "Davomiylik toifasi", "combo", {"options": DURATION_OPTIONS}),
        ("horizontal_distance_cm", "Gorizontal masofa (H), sm", "entry", {}),
        ("vertical_location_cm", "Vertikal balandlik (V), sm", "entry", {}),
        ("vertical_travel_cm", "Vertikal harakat masofasi (D), sm", "entry", {}),
        ("asymmetry_angle_deg", "Tana burilish burchagi (A), gradus", "entry", {}),
        ("coupling_quality", "Yukni ushlash sifati", "combo", {"options": COUPLING_OPTIONS}),
    ]),
    ("3. Og'irlik darajasi: ish holati va statik yuk", [
        ("posture_type", "Ish holati (poza)", "combo", {"options": POSTURE_OPTIONS}),
        ("static_load_kgs", "Statik yuk, kgf·s (ixtiyoriy)", "entry", {}),
        ("body_inclinations_per_shift", "Tana egilishi, smenada marta", "entry", {}),
        ("walking_distance_km", "Piyoda yurish masofasi, km/smena", "entry", {}),
    ]),
    ("4. Psixofiziologik zo'riqish", [
        ("attention_concentration_percent", "Diqqat konsentratsiyasi, smena vaqtidan %", "entry", {}),
        ("signals_per_hour", "Soatiga signal/axborot soni", "entry", {}),
        ("responsibility_level", "Mas'uliyat darajasi", "combo", {"options": RESPONSIBILITY_OPTIONS}),
        ("monotony_operations_count", "Bir xil operatsiyalar soni (monotoniya)", "entry", {}),
        ("night_shift", "Tungi smenada ishlaydi", "check", {}),
    ]),
    ("5. Ish muhiti (alyuminiy profil ishlab chiqarishga xos)", [
        ("temperature_c", "Harorat, °C (masalan, ekstruziya pressi yaqinida)", "entry", {}),
        ("metal_dust_mg_m3", "Metall changi konsentratsiyasi, mg/m³", "entry", {}),
        ("noise_level_db", "Shovqin darajasi, dB", "entry", {}),
        ("uses_ppe", "Shaxsiy himoya vositalaridan muntazam foydalanadi", "check", {}),
    ]),
    ("6. Inson omili", [
        ("training_completed", "Mehnat xavfsizligi bo'yicha o'qitishdan o'tgan", "check", {}),
        ("last_training_at", "Oxirgi o'qitish sanasi (YYYY-MM-DD, ixtiyoriy)", "entry", {}),
        ("fatigue_self_score", "O'z-o'zini charchoq bahosi", "combo", {"options": FATIGUE_OPTIONS}),
        ("health_group", "Sog'liq holati guruhi", "combo", {"options": HEALTH_OPTIONS}),
        ("prior_incidents_count", "Oldingi baxtsiz hodisalar / near-miss soni", "entry", {}),
    ]),
    ("Qo'shimcha", [
        ("notes", "Izoh", "text", {}),
    ]),
]

DEFAULTS: dict[str, Any] = {
    "worker_gender": "erkak",
    "operation_type": "qolda",
    "duration_category": "long",
    "coupling_quality": "fair",
    "posture_type": "erkin",
    "responsibility_level": "ozi_uchun",
    "fatigue_self_score": "1",
    "health_group": "soglom",
    "shift_duration_hours": "8",
    "prior_incidents_count": "0",
    "body_inclinations_per_shift": "0",
    "walking_distance_km": "0",
    "attention_concentration_percent": "0",
    "signals_per_hour": "0",
    "monotony_operations_count": "0",
    "uses_ppe": True,
    "training_completed": False,
    "night_shift": False,
}


def _to_float(value: str, default: float | None = 0.0) -> float | None:
    value = (value or "").strip()
    if not value:
        return default
    return float(value.replace(",", "."))


def _to_int(value: str, default: int | None = 0) -> int | None:
    value = (value or "").strip()
    if not value:
        return default
    return int(float(value))


class App(tk.Tk):
    def __init__(self) -> None:
        super().__init__()
        self.title("Ergonomik va inson omili xavfsizlik baholash dasturi — Alyuminiy profil sanoati")
        self.geometry("1000x760")
        self.minsize(820, 600)

        self.conn = storage.connect()
        self.widgets: dict[str, Any] = {}
        self.combo_lookup: dict[str, dict[str, str]] = {}

        notebook = ttk.Notebook(self)
        notebook.pack(fill="both", expand=True)

        self.form_tab = ttk.Frame(notebook)
        self.history_tab = ttk.Frame(notebook)
        notebook.add(self.form_tab, text="Yangi baholash")
        notebook.add(self.history_tab, text="Tarix")
        notebook.bind("<<NotebookTabChanged>>", lambda e: self._maybe_refresh_history(notebook))
        self._notebook = notebook

        self._build_form_tab(self.form_tab)
        self._build_history_tab(self.history_tab)

    # ------------------------------------------------------------------ FORM

    def _build_form_tab(self, parent: ttk.Frame) -> None:
        header = tk.Label(
            parent,
            text=(
                "Metodika: NIOSH (1994) ko'tarish tenglamasi, R2.2.2006-05 / SanQvaM 0069-24 uslubidagi "
                "og'irlik va zo'riqish darajalari, hamda inson omili xavfi bali."
            ),
            wraplength=940, justify="left", fg="#555",
        )
        header.pack(fill="x", padx=12, pady=(10, 4))

        toolbar = ttk.Frame(parent)
        toolbar.pack(fill="x", padx=12)
        ttk.Button(toolbar, text="Namuna bilan to'ldirish (Alyuminiy profil bog'lami)",
                   command=self._fill_example).pack(side="left")
        ttk.Button(toolbar, text="Tozalash", command=self._clear_form).pack(side="left", padx=6)

        canvas = tk.Canvas(parent, borderwidth=0, highlightthickness=0)
        scrollbar = ttk.Scrollbar(parent, orient="vertical", command=canvas.yview)
        scroll_frame = ttk.Frame(canvas)

        scroll_frame.bind("<Configure>", lambda e: canvas.configure(scrollregion=canvas.bbox("all")))
        canvas.create_window((0, 0), window=scroll_frame, anchor="nw")
        canvas.configure(yscrollcommand=scrollbar.set)

        def _on_mousewheel(event):
            canvas.yview_scroll(int(-1 * (event.delta / 120)), "units")

        canvas.bind_all("<MouseWheel>", _on_mousewheel)

        canvas.pack(side="left", fill="both", expand=True, padx=(12, 0), pady=8)
        scrollbar.pack(side="left", fill="y", pady=8)

        for title, fields in FIELD_SECTIONS:
            section = ttk.LabelFrame(scroll_frame, text=title)
            section.pack(fill="x", padx=4, pady=6)

            for row, (key, label, kind, extra) in enumerate(fields):
                tk.Label(section, text=label, anchor="w").grid(row=row, column=0, sticky="w", padx=6, pady=4)
                self._build_field(section, key, kind, extra).grid(row=row, column=1, sticky="we", padx=6, pady=4)
            section.columnconfigure(1, weight=1)

        actions = ttk.Frame(parent)
        actions.pack(fill="x", padx=12, pady=10)
        ttk.Button(actions, text="Hisoblash va saqlash", command=self._submit).pack(side="right")

        self._clear_form()

    def _build_field(self, parent: tk.Widget, key: str, kind: str, extra: dict) -> tk.Widget:
        if kind == "entry":
            var = tk.StringVar()
            widget = ttk.Entry(parent, textvariable=var)
            self.widgets[key] = var
            return widget

        if kind == "check":
            var = tk.BooleanVar()
            widget = ttk.Checkbutton(parent, variable=var)
            self.widgets[key] = var
            return widget

        if kind == "combo":
            options: list[tuple[str, str]] = extra["options"]
            self.combo_lookup[key] = {label: value for value, label in options}
            var = tk.StringVar()
            widget = ttk.Combobox(parent, textvariable=var, state="readonly",
                                   values=[label for _, label in options])
            self.widgets[key] = var
            return widget

        if kind == "text":
            widget = tk.Text(parent, height=3, wrap="word")
            self.widgets[key] = widget
            return widget

        raise ValueError(f"Noma'lum maydon turi: {kind}")

    def _set_field(self, key: str, kind: str, value: Any) -> None:
        widget_or_var = self.widgets[key]

        if kind == "combo":
            options = next(f[3]["options"] for _, fields in FIELD_SECTIONS for f in fields if f[0] == key)
            label = next((lbl for val, lbl in options if val == value), options[0][1])
            widget_or_var.set(label)
        elif kind == "check":
            widget_or_var.set(bool(value))
        elif kind == "text":
            widget_or_var.delete("1.0", "end")
            widget_or_var.insert("1.0", value or "")
        else:
            widget_or_var.set("" if value is None else str(value))

    def _fill_example(self) -> None:
        for _, fields in FIELD_SECTIONS:
            for key, _, kind, _ in fields:
                if key in ALUMINUM_EXAMPLE:
                    self._set_field(key, kind, ALUMINUM_EXAMPLE[key])

    def _clear_form(self) -> None:
        for _, fields in FIELD_SECTIONS:
            for key, _, kind, _ in fields:
                self._set_field(key, kind, DEFAULTS.get(key, ""))

    def _read_form(self) -> dict[str, Any]:
        raw: dict[str, Any] = {}
        for _, fields in FIELD_SECTIONS:
            for key, _, kind, _ in fields:
                widget_or_var = self.widgets[key]
                if kind == "combo":
                    label = widget_or_var.get()
                    raw[key] = self.combo_lookup[key].get(label, "")
                elif kind == "check":
                    raw[key] = bool(widget_or_var.get())
                elif kind == "text":
                    raw[key] = widget_or_var.get("1.0", "end").strip()
                else:
                    raw[key] = widget_or_var.get().strip()

        data: dict[str, Any] = {
            "employee_name": raw["employee_name"] or None,
            "worker_gender": raw["worker_gender"],
            "employee_age": _to_int(raw["employee_age"], None),
            "experience_years": _to_float(raw["experience_years"], None),
            "operation_type": raw["operation_type"],
            "load_description": raw["load_description"] or None,
            "load_mass_kg": _to_float(raw["load_mass_kg"]),
            "lifts_per_shift": _to_int(raw["lifts_per_shift"]),
            "lifts_per_minute": _to_float(raw["lifts_per_minute"]),
            "shift_duration_hours": _to_float(raw["shift_duration_hours"], 8.0),
            "duration_category": raw["duration_category"],
            "horizontal_distance_cm": _to_float(raw["horizontal_distance_cm"]),
            "vertical_location_cm": _to_float(raw["vertical_location_cm"]),
            "vertical_travel_cm": _to_float(raw["vertical_travel_cm"]),
            "asymmetry_angle_deg": _to_float(raw["asymmetry_angle_deg"], 0.0),
            "coupling_quality": raw["coupling_quality"],
            "posture_type": raw["posture_type"],
            "static_load_kgs": _to_float(raw["static_load_kgs"], None),
            "body_inclinations_per_shift": _to_int(raw["body_inclinations_per_shift"]),
            "walking_distance_km": _to_float(raw["walking_distance_km"]),
            "attention_concentration_percent": _to_int(raw["attention_concentration_percent"]),
            "signals_per_hour": _to_int(raw["signals_per_hour"]),
            "responsibility_level": raw["responsibility_level"],
            "monotony_operations_count": _to_int(raw["monotony_operations_count"]),
            "night_shift": raw["night_shift"],
            "temperature_c": _to_float(raw["temperature_c"], None),
            "metal_dust_mg_m3": _to_float(raw["metal_dust_mg_m3"], None),
            "noise_level_db": _to_float(raw["noise_level_db"], None),
            "uses_ppe": raw["uses_ppe"],
            "training_completed": raw["training_completed"],
            "last_training_at": raw["last_training_at"] or None,
            "fatigue_self_score": _to_int(raw["fatigue_self_score"], 1),
            "health_group": raw["health_group"],
            "prior_incidents_count": _to_int(raw["prior_incidents_count"]),
            "notes": raw["notes"] or None,
        }
        return data

    def _submit(self) -> None:
        try:
            data = self._read_form()
            if not data["load_mass_kg"] or data["load_mass_kg"] <= 0:
                raise ValueError("Yuk massasi kiritilishi shart.")
            if not data["horizontal_distance_cm"] or not data["vertical_location_cm"] or not data["vertical_travel_cm"]:
                raise ValueError("NIOSH parametrlari (H, V, D) to'liq kiritilishi shart.")
        except ValueError as exc:
            messagebox.showerror("Xatolik", str(exc))
            return
        except Exception:
            messagebox.showerror("Xatolik", "Raqamli maydonlarni to'g'ri kiriting.")
            return

        result = engine.evaluate(data)
        storage.save_assessment(self.conn, data, result)

        self._show_report(data, result)
        self._refresh_history()

    # ---------------------------------------------------------------- REPORT

    def _show_report(self, data: dict[str, Any], result: dict[str, Any]) -> None:
        window = tk.Toplevel(self)
        window.title("Ergonomik baholash natijasi")
        window.geometry("640x720")

        integral = result["integral_safety_index"]
        color = "#176b3a" if integral >= 70 else ("#b8841e" if integral >= 40 else "#b83232")

        subject = " — ".join(filter(None, [data.get("employee_name"), data.get("load_description")])) or "Baholash natijasi"

        header = tk.Frame(window)
        header.pack(fill="x", padx=16, pady=(16, 8))

        tk.Label(header, text=subject, font=("Helvetica", 11), wraplength=600, justify="center").pack()
        tk.Label(header, text=f"{integral}/100", font=("Helvetica", 32, "bold"), fg=color).pack()
        tk.Label(header, text=result["risk_category"], font=("Helvetica", 13, "bold"), fg=color).pack()

        stats = ttk.Frame(window)
        stats.pack(fill="x", padx=16, pady=8)
        self._stat_row(stats, "Ko'tarish indeksi (NIOSH LI)", result["lifting_index"])
        self._stat_row(stats, "RWL (tavsiya etilgan chegara)", f"{result['rwl_kg']} kg")
        self._stat_row(stats, "Og'irlik darajasi klassi", result["severity_class"])
        self._stat_row(stats, "Zo'riqish klassi", result["strain_class"])
        self._stat_row(stats, "Inson omili xavfi", f"{result['human_factor_risk_score']}/100")

        tk.Label(window, text="Tavsiyalar:", font=("Helvetica", 11, "bold"), anchor="w").pack(
            fill="x", padx=16, pady=(12, 4)
        )
        rec_text = tk.Text(window, height=10, wrap="word")
        rec_text.pack(fill="both", expand=True, padx=16, pady=(0, 8))
        for rec in result["recommendations"]:
            rec_text.insert("end", f"• {rec}\n\n")
        rec_text.configure(state="disabled")

        ttk.Button(window, text="Yopish", command=window.destroy).pack(pady=10)

    def _stat_row(self, parent: tk.Widget, label: str, value: Any) -> None:
        row = ttk.Frame(parent)
        row.pack(fill="x", pady=2)
        tk.Label(row, text=label, anchor="w").pack(side="left")
        tk.Label(row, text=str(value), font=("Helvetica", 10, "bold"), anchor="e").pack(side="right")

    # --------------------------------------------------------------- HISTORY

    def _build_history_tab(self, parent: ttk.Frame) -> None:
        toolbar = ttk.Frame(parent)
        toolbar.pack(fill="x", padx=12, pady=8)
        ttk.Button(toolbar, text="Yangilash", command=self._refresh_history).pack(side="left")
        ttk.Button(toolbar, text="Ko'rish", command=self._open_selected).pack(side="left", padx=6)
        ttk.Button(toolbar, text="O'chirish", command=self._delete_selected).pack(side="left")

        columns = ("id", "sana", "yuk", "li", "klass", "integral", "toifa")
        self.history_tree = ttk.Treeview(parent, columns=columns, show="headings")
        headings = {
            "id": "ID", "sana": "Sana", "yuk": "Yuk tavsifi", "li": "LI",
            "klass": "Og'irlik klassi", "integral": "Integral", "toifa": "Xavf toifasi",
        }
        widths = {"id": 40, "sana": 130, "yuk": 260, "li": 60, "klass": 100, "integral": 70, "toifa": 200}
        for col in columns:
            self.history_tree.heading(col, text=headings[col])
            self.history_tree.column(col, width=widths[col], anchor="w")

        self.history_tree.pack(fill="both", expand=True, padx=12, pady=(0, 12))
        self.history_tree.bind("<Double-1>", lambda e: self._open_selected())

        self._history_loaded = False

    def _maybe_refresh_history(self, notebook: ttk.Notebook) -> None:
        if notebook.index(notebook.select()) == 1:
            self._refresh_history()

    def _refresh_history(self) -> None:
        for item in self.history_tree.get_children():
            self.history_tree.delete(item)
        for row in storage.list_assessments(self.conn):
            self.history_tree.insert("", "end", iid=str(row["id"]), values=(
                row["id"], row["created_at"], row["load_description"] or "—",
                row["lifting_index"], row["severity_class"], row["integral_safety_index"],
                row["risk_category"],
            ))
        self._history_loaded = True

    def _selected_id(self) -> int | None:
        selection = self.history_tree.selection()
        return int(selection[0]) if selection else None

    def _open_selected(self) -> None:
        assessment_id = self._selected_id()
        if assessment_id is None:
            messagebox.showinfo("Tanlanmagan", "Iltimos, ro'yxatdan yozuvni tanlang.")
            return
        record = storage.get_assessment(self.conn, assessment_id)
        if record is None:
            return
        result = {
            "rwl_kg": record["rwl_kg"],
            "lifting_index": record["lifting_index"],
            "severity_class": record["severity_class"],
            "strain_class": record["strain_class"],
            "human_factor_risk_score": record["human_factor_risk_score"],
            "integral_safety_index": int(float(record["integral_safety_index"])),
            "risk_category": record["risk_category"],
            "recommendations": record["recommendations"],
        }
        self._show_report(record, result)

    def _delete_selected(self) -> None:
        assessment_id = self._selected_id()
        if assessment_id is None:
            messagebox.showinfo("Tanlanmagan", "Iltimos, ro'yxatdan yozuvni tanlang.")
            return
        if messagebox.askyesno("Tasdiqlash", "Ushbu yozuvni o'chirishni tasdiqlaysizmi?"):
            storage.delete_assessment(self.conn, assessment_id)
            self._refresh_history()


def main() -> None:
    app = App()
    app.mainloop()


if __name__ == "__main__":
    main()
