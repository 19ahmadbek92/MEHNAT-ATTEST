package uz.mehnatattest.ergonomic

import android.content.ContentValues
import android.content.Context
import android.database.sqlite.SQLiteDatabase
import android.database.sqlite.SQLiteOpenHelper
import org.json.JSONArray
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

data class HistoryRow(
    val id: Long,
    val createdAt: String,
    val loadDescription: String?,
    val liftingIndex: Double,
    val severityClass: String,
    val integralSafetyIndex: Int,
    val riskCategory: String,
)

/** Mahalliy (telefon ichidagi) SQLite bazasiga baholashlar tarixini saqlaydi. */
class AssessmentDbHelper(context: Context) : SQLiteOpenHelper(context, DB_NAME, null, DB_VERSION) {

    override fun onCreate(db: SQLiteDatabase) {
        db.execSQL(
            """
            CREATE TABLE assessments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                created_at TEXT NOT NULL,
                employee_name TEXT,
                worker_gender TEXT,
                employee_age INTEGER,
                experience_years REAL,
                operation_type TEXT,
                load_description TEXT,
                load_mass_kg REAL,
                lifts_per_shift INTEGER,
                lifts_per_minute REAL,
                shift_duration_hours REAL,
                duration_category TEXT,
                horizontal_distance_cm REAL,
                vertical_location_cm REAL,
                vertical_travel_cm REAL,
                asymmetry_angle_deg REAL,
                coupling_quality TEXT,
                posture_type TEXT,
                static_load_kgs REAL,
                body_inclinations_per_shift INTEGER,
                walking_distance_km REAL,
                attention_concentration_percent INTEGER,
                signals_per_hour INTEGER,
                responsibility_level TEXT,
                monotony_operations_count INTEGER,
                night_shift INTEGER,
                temperature_c REAL,
                metal_dust_mg_m3 REAL,
                noise_level_db REAL,
                uses_ppe INTEGER,
                training_completed INTEGER,
                last_training_at TEXT,
                fatigue_self_score INTEGER,
                health_group TEXT,
                prior_incidents_count INTEGER,
                notes TEXT,
                rwl_kg REAL,
                lifting_index REAL,
                severity_class TEXT,
                strain_class TEXT,
                human_factor_risk_score INTEGER,
                integral_safety_index INTEGER,
                risk_category TEXT,
                recommendations TEXT
            )
            """.trimIndent()
        )
    }

    override fun onUpgrade(db: SQLiteDatabase, oldVersion: Int, newVersion: Int) {
        db.execSQL("DROP TABLE IF EXISTS assessments")
        onCreate(db)
    }

    fun insert(input: AssessmentInput, result: AssessmentResult): Long {
        val values = ContentValues().apply {
            put("created_at", SimpleDateFormat("yyyy-MM-dd HH:mm", Locale.US).format(Date()))
            put("employee_name", input.employeeName)
            put("worker_gender", input.workerGender)
            put("employee_age", input.employeeAge)
            put("experience_years", input.experienceYears)
            put("operation_type", input.operationType)
            put("load_description", input.loadDescription)
            put("load_mass_kg", input.loadMassKg)
            put("lifts_per_shift", input.liftsPerShift)
            put("lifts_per_minute", input.liftsPerMinute)
            put("shift_duration_hours", input.shiftDurationHours)
            put("duration_category", input.durationCategory)
            put("horizontal_distance_cm", input.horizontalDistanceCm)
            put("vertical_location_cm", input.verticalLocationCm)
            put("vertical_travel_cm", input.verticalTravelCm)
            put("asymmetry_angle_deg", input.asymmetryAngleDeg)
            put("coupling_quality", input.couplingQuality)
            put("posture_type", input.postureType)
            put("static_load_kgs", input.staticLoadKgs)
            put("body_inclinations_per_shift", input.bodyInclinationsPerShift)
            put("walking_distance_km", input.walkingDistanceKm)
            put("attention_concentration_percent", input.attentionConcentrationPercent)
            put("signals_per_hour", input.signalsPerHour)
            put("responsibility_level", input.responsibilityLevel)
            put("monotony_operations_count", input.monotonyOperationsCount)
            put("night_shift", if (input.nightShift) 1 else 0)
            put("temperature_c", input.temperatureC)
            put("metal_dust_mg_m3", input.metalDustMgM3)
            put("noise_level_db", input.noiseLevelDb)
            put("uses_ppe", if (input.usesPpe) 1 else 0)
            put("training_completed", if (input.trainingCompleted) 1 else 0)
            put("last_training_at", input.lastTrainingAt)
            put("fatigue_self_score", input.fatigueSelfScore)
            put("health_group", input.healthGroup)
            put("prior_incidents_count", input.priorIncidentsCount)
            put("notes", input.notes)
            put("rwl_kg", result.rwlKg)
            put("lifting_index", result.liftingIndex)
            put("severity_class", result.severityClass)
            put("strain_class", result.strainClass)
            put("human_factor_risk_score", result.humanFactorRiskScore)
            put("integral_safety_index", result.integralSafetyIndex)
            put("risk_category", result.riskCategory)
            put("recommendations", JSONArray(result.recommendations).toString())
        }
        return writableDatabase.insert("assessments", null, values)
    }

    fun listAll(): List<HistoryRow> {
        val rows = mutableListOf<HistoryRow>()
        readableDatabase.rawQuery(
            "SELECT id, created_at, load_description, lifting_index, severity_class, integral_safety_index, risk_category " +
                "FROM assessments ORDER BY id DESC",
            null
        ).use { cursor ->
            while (cursor.moveToNext()) {
                rows.add(
                    HistoryRow(
                        id = cursor.getLong(0),
                        createdAt = cursor.getString(1),
                        loadDescription = cursor.getString(2),
                        liftingIndex = cursor.getDouble(3),
                        severityClass = cursor.getString(4),
                        integralSafetyIndex = cursor.getInt(5),
                        riskCategory = cursor.getString(6),
                    )
                )
            }
        }
        return rows
    }

    fun get(id: Long): Pair<AssessmentInput, AssessmentResult>? {
        readableDatabase.rawQuery("SELECT * FROM assessments WHERE id = ?", arrayOf(id.toString())).use { cursor ->
            if (!cursor.moveToFirst()) return null

            fun col(name: String) = cursor.getColumnIndexOrThrow(name)
            fun str(name: String) = if (cursor.isNull(col(name))) null else cursor.getString(col(name))
            fun dbl(name: String) = if (cursor.isNull(col(name))) null else cursor.getDouble(col(name))
            fun int(name: String) = if (cursor.isNull(col(name))) null else cursor.getInt(col(name))
            fun bool(name: String) = cursor.getInt(col(name)) == 1

            val input = AssessmentInput(
                employeeName = str("employee_name"),
                workerGender = str("worker_gender") ?: "erkak",
                employeeAge = int("employee_age"),
                experienceYears = dbl("experience_years"),
                operationType = str("operation_type") ?: "qolda",
                loadDescription = str("load_description"),
                loadMassKg = dbl("load_mass_kg") ?: 0.0,
                liftsPerShift = int("lifts_per_shift") ?: 0,
                liftsPerMinute = dbl("lifts_per_minute") ?: 0.0,
                shiftDurationHours = dbl("shift_duration_hours") ?: 8.0,
                durationCategory = str("duration_category") ?: "long",
                horizontalDistanceCm = dbl("horizontal_distance_cm") ?: 0.0,
                verticalLocationCm = dbl("vertical_location_cm") ?: 0.0,
                verticalTravelCm = dbl("vertical_travel_cm") ?: 0.0,
                asymmetryAngleDeg = dbl("asymmetry_angle_deg") ?: 0.0,
                couplingQuality = str("coupling_quality") ?: "fair",
                postureType = str("posture_type") ?: "erkin",
                staticLoadKgs = dbl("static_load_kgs"),
                bodyInclinationsPerShift = int("body_inclinations_per_shift") ?: 0,
                walkingDistanceKm = dbl("walking_distance_km") ?: 0.0,
                attentionConcentrationPercent = int("attention_concentration_percent") ?: 0,
                signalsPerHour = int("signals_per_hour") ?: 0,
                responsibilityLevel = str("responsibility_level") ?: "ozi_uchun",
                monotonyOperationsCount = int("monotony_operations_count") ?: 0,
                nightShift = bool("night_shift"),
                temperatureC = dbl("temperature_c"),
                metalDustMgM3 = dbl("metal_dust_mg_m3"),
                noiseLevelDb = dbl("noise_level_db"),
                usesPpe = bool("uses_ppe"),
                trainingCompleted = bool("training_completed"),
                lastTrainingAt = str("last_training_at"),
                fatigueSelfScore = int("fatigue_self_score") ?: 1,
                healthGroup = str("health_group") ?: "soglom",
                priorIncidentsCount = int("prior_incidents_count") ?: 0,
                notes = str("notes"),
            )

            val recommendationsJson = str("recommendations") ?: "[]"
            val recommendations = mutableListOf<String>()
            val array = JSONArray(recommendationsJson)
            for (i in 0 until array.length()) recommendations.add(array.getString(i))

            val result = AssessmentResult(
                rwlKg = dbl("rwl_kg") ?: 0.0,
                liftingIndex = dbl("lifting_index") ?: 0.0,
                severityClass = str("severity_class") ?: "",
                strainClass = str("strain_class") ?: "",
                humanFactorRiskScore = int("human_factor_risk_score") ?: 0,
                integralSafetyIndex = int("integral_safety_index") ?: 0,
                riskCategory = str("risk_category") ?: "",
                recommendations = recommendations,
            )

            return input to result
        }
    }

    fun delete(id: Long) {
        writableDatabase.delete("assessments", "id = ?", arrayOf(id.toString()))
    }

    companion object {
        private const val DB_NAME = "ergonomic_assessments.db"
        private const val DB_VERSION = 1
    }
}
