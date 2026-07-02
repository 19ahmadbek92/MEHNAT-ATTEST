"""Dastur ishga tushirilgan papkada mahalliy SQLite fayliga baholashlar tarixini saqlaydi."""

from __future__ import annotations

import json
import sqlite3
from pathlib import Path
from typing import Any

DEFAULT_DB_PATH = Path(__file__).with_name("ergonomic_assessments.db")

INPUT_COLUMNS = [
    "employee_name",
    "worker_gender",
    "employee_age",
    "experience_years",
    "operation_type",
    "load_description",
    "load_mass_kg",
    "lifts_per_shift",
    "lifts_per_minute",
    "shift_duration_hours",
    "duration_category",
    "horizontal_distance_cm",
    "vertical_location_cm",
    "vertical_travel_cm",
    "asymmetry_angle_deg",
    "coupling_quality",
    "posture_type",
    "static_load_kgs",
    "body_inclinations_per_shift",
    "walking_distance_km",
    "attention_concentration_percent",
    "signals_per_hour",
    "responsibility_level",
    "monotony_operations_count",
    "night_shift",
    "temperature_c",
    "metal_dust_mg_m3",
    "noise_level_db",
    "uses_ppe",
    "training_completed",
    "last_training_at",
    "fatigue_self_score",
    "health_group",
    "prior_incidents_count",
    "notes",
]

RESULT_COLUMNS = [
    "rwl_kg",
    "lifting_index",
    "severity_class",
    "strain_class",
    "human_factor_risk_score",
    "integral_safety_index",
    "risk_category",
    "recommendations",
]


def connect(db_path: Path | str = DEFAULT_DB_PATH) -> sqlite3.Connection:
    conn = sqlite3.connect(db_path)
    conn.row_factory = sqlite3.Row
    _init_schema(conn)
    return conn


def _init_schema(conn: sqlite3.Connection) -> None:
    columns_sql = ",\n".join(f"{col} TEXT" for col in INPUT_COLUMNS + RESULT_COLUMNS)
    conn.execute(
        f"""
        CREATE TABLE IF NOT EXISTS assessments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            created_at TEXT NOT NULL DEFAULT (datetime('now', 'localtime')),
            {columns_sql}
        )
        """
    )
    conn.commit()


def save_assessment(conn: sqlite3.Connection, data: dict[str, Any], result: dict[str, Any]) -> int:
    record = {**data, **result}
    record["recommendations"] = json.dumps(record.get("recommendations", []), ensure_ascii=False)

    columns = INPUT_COLUMNS + RESULT_COLUMNS
    placeholders = ", ".join("?" for _ in columns)
    values = [record.get(col) for col in columns]

    cursor = conn.execute(
        f"INSERT INTO assessments ({', '.join(columns)}) VALUES ({placeholders})",
        values,
    )
    conn.commit()
    return cursor.lastrowid


def list_assessments(conn: sqlite3.Connection) -> list[sqlite3.Row]:
    return conn.execute(
        "SELECT id, created_at, load_description, load_mass_kg, lifting_index, "
        "severity_class, integral_safety_index, risk_category "
        "FROM assessments ORDER BY id DESC"
    ).fetchall()


def get_assessment(conn: sqlite3.Connection, assessment_id: int) -> dict[str, Any] | None:
    row = conn.execute("SELECT * FROM assessments WHERE id = ?", (assessment_id,)).fetchone()
    if row is None:
        return None

    record = dict(row)
    record["recommendations"] = json.loads(record.get("recommendations") or "[]")
    return record


def delete_assessment(conn: sqlite3.Connection, assessment_id: int) -> None:
    conn.execute("DELETE FROM assessments WHERE id = ?", (assessment_id,))
    conn.commit()
