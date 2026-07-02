package uz.mehnatattest.ergonomic

import android.content.Intent
import android.os.Bundle
import android.view.Gravity
import android.view.View
import android.widget.ArrayAdapter
import android.widget.CheckBox
import android.widget.EditText
import android.widget.LinearLayout
import android.widget.Spinner
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import com.google.android.material.card.MaterialCardView
import com.google.android.material.snackbar.Snackbar
import uz.mehnatattest.ergonomic.databinding.ActivityMainBinding

class MainActivity : AppCompatActivity() {

    private lateinit var binding: ActivityMainBinding
    private lateinit var dbHelper: AssessmentDbHelper

    // Maydon kaliti -> widget (EditText, CheckBox yoki Spinner)
    private val widgets = mutableMapOf<String, View>()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)
        setSupportActionBar(binding.toolbar)

        dbHelper = AssessmentDbHelper(this)

        buildForm()
        clearForm()

        binding.btnExample.setOnClickListener { fillExample() }
        binding.btnClear.setOnClickListener { clearForm() }
        binding.btnHistory.setOnClickListener { startActivity(Intent(this, HistoryActivity::class.java)) }
        binding.btnCalculate.setOnClickListener { submit() }
    }

    private fun buildForm() {
        val density = resources.displayMetrics.density
        fun dp(value: Int) = (value * density).toInt()

        for ((title, fields) in FormSpec.SECTIONS) {
            val card = MaterialCardView(this).apply {
                layoutParams = LinearLayout.LayoutParams(
                    LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT
                ).also { it.bottomMargin = dp(12) }
                radius = dp(12).toFloat()
                cardElevation = dp(1).toFloat()
                useCompatPadding = true
            }
            val cardContent = LinearLayout(this).apply {
                orientation = LinearLayout.VERTICAL
                setPadding(dp(16), dp(16), dp(16), dp(16))
            }
            card.addView(cardContent)

            val titleView = TextView(this).apply {
                text = title
                textSize = 15f
                setTypeface(typeface, android.graphics.Typeface.BOLD)
                setPadding(0, 0, 0, dp(8))
            }
            cardContent.addView(titleView)

            for (field in fields) {
                val label = TextView(this).apply {
                    text = field.label
                    textSize = 13f
                    setPadding(0, dp(8), 0, dp(4))
                }
                cardContent.addView(label)

                val widget: View = when (field.kind) {
                    FieldKind.ENTRY -> EditText(this).apply {
                        layoutParams = LinearLayout.LayoutParams(
                            LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT
                        )
                    }
                    FieldKind.TEXT -> EditText(this).apply {
                        layoutParams = LinearLayout.LayoutParams(
                            LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT
                        )
                        minLines = 3
                        gravity = Gravity.TOP
                    }
                    FieldKind.CHECK -> CheckBox(this).apply {
                        layoutParams = LinearLayout.LayoutParams(
                            LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT
                        )
                        text = ""
                    }
                    FieldKind.COMBO -> Spinner(this).apply {
                        layoutParams = LinearLayout.LayoutParams(
                            LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT
                        )
                        adapter = ArrayAdapter(
                            this@MainActivity,
                            android.R.layout.simple_spinner_dropdown_item,
                            field.options.map { it.second }
                        )
                        tag = field.options
                    }
                }
                cardContent.addView(widget)
                widgets[field.key] = widget
            }

            binding.formContainer.addView(card)
        }
    }

    private fun setFieldValue(key: String, value: String) {
        when (val widget = widgets[key]) {
            is EditText -> widget.setText(value)
            is Spinner -> {
                @Suppress("UNCHECKED_CAST")
                val options = widget.tag as List<Pair<String, String>>
                val index = options.indexOfFirst { it.first == value }
                widget.setSelection(if (index >= 0) index else 0)
            }
            else -> Unit
        }
    }

    private fun setCheckValue(key: String, value: Boolean) {
        (widgets[key] as? CheckBox)?.isChecked = value
    }

    private fun getFieldValue(key: String): String = when (val widget = widgets[key]) {
        is EditText -> widget.text.toString().trim()
        is Spinner -> {
            @Suppress("UNCHECKED_CAST")
            val options = widget.tag as List<Pair<String, String>>
            options.getOrNull(widget.selectedItemPosition)?.first ?: ""
        }
        else -> ""
    }

    private fun getCheckValue(key: String): Boolean = (widgets[key] as? CheckBox)?.isChecked ?: false

    private fun clearForm() {
        for ((_, fields) in FormSpec.SECTIONS) {
            for (field in fields) {
                when (field.kind) {
                    FieldKind.CHECK -> setCheckValue(field.key, FormSpec.DEFAULT_CHECKS[field.key] ?: false)
                    FieldKind.COMBO -> setFieldValue(field.key, FormSpec.DEFAULTS[field.key] ?: field.options.first().first)
                    else -> setFieldValue(field.key, FormSpec.DEFAULTS[field.key] ?: "")
                }
            }
        }
    }

    private fun fillExample() {
        for ((_, fields) in FormSpec.SECTIONS) {
            for (field in fields) {
                if (field.kind == FieldKind.CHECK) {
                    FormSpec.ALUMINUM_EXAMPLE_CHECKS[field.key]?.let { setCheckValue(field.key, it) }
                } else {
                    FormSpec.ALUMINUM_EXAMPLE[field.key]?.let { setFieldValue(field.key, it) }
                }
            }
        }
    }

    private fun readForm(): AssessmentInput? {
        fun d(key: String): Double? = getFieldValue(key).ifBlank { null }?.replace(",", ".")?.toDoubleOrNull()
        fun i(key: String): Int? = getFieldValue(key).ifBlank { null }?.toDoubleOrNull()?.toInt()

        val loadMass = d("load_mass_kg")
        val horizontal = d("horizontal_distance_cm")
        val vertical = d("vertical_location_cm")
        val travel = d("vertical_travel_cm")

        if (loadMass == null || loadMass <= 0 || horizontal == null || vertical == null || travel == null) {
            Snackbar.make(binding.root, getString(R.string.error_required_fields), Snackbar.LENGTH_LONG).show()
            return null
        }

        return AssessmentInput(
            employeeName = getFieldValue("employee_name").ifBlank { null },
            workerGender = getFieldValue("worker_gender"),
            employeeAge = i("employee_age"),
            experienceYears = d("experience_years"),
            operationType = getFieldValue("operation_type"),
            loadDescription = getFieldValue("load_description").ifBlank { null },
            loadMassKg = loadMass,
            liftsPerShift = i("lifts_per_shift") ?: 0,
            liftsPerMinute = d("lifts_per_minute") ?: 0.0,
            shiftDurationHours = d("shift_duration_hours") ?: 8.0,
            durationCategory = getFieldValue("duration_category"),
            horizontalDistanceCm = horizontal,
            verticalLocationCm = vertical,
            verticalTravelCm = travel,
            asymmetryAngleDeg = d("asymmetry_angle_deg") ?: 0.0,
            couplingQuality = getFieldValue("coupling_quality"),
            postureType = getFieldValue("posture_type"),
            staticLoadKgs = d("static_load_kgs"),
            bodyInclinationsPerShift = i("body_inclinations_per_shift") ?: 0,
            walkingDistanceKm = d("walking_distance_km") ?: 0.0,
            attentionConcentrationPercent = i("attention_concentration_percent") ?: 0,
            signalsPerHour = i("signals_per_hour") ?: 0,
            responsibilityLevel = getFieldValue("responsibility_level"),
            monotonyOperationsCount = i("monotony_operations_count") ?: 0,
            nightShift = getCheckValue("night_shift"),
            temperatureC = d("temperature_c"),
            metalDustMgM3 = d("metal_dust_mg_m3"),
            noiseLevelDb = d("noise_level_db"),
            usesPpe = getCheckValue("uses_ppe"),
            trainingCompleted = getCheckValue("training_completed"),
            lastTrainingAt = getFieldValue("last_training_at").ifBlank { null },
            fatigueSelfScore = getFieldValue("fatigue_self_score").toIntOrNull() ?: 1,
            healthGroup = getFieldValue("health_group"),
            priorIncidentsCount = i("prior_incidents_count") ?: 0,
            notes = getFieldValue("notes").ifBlank { null },
        )
    }

    private fun submit() {
        val input = readForm() ?: return

        val result = Engine.evaluate(input)
        val id = dbHelper.insert(input, result)

        Snackbar.make(binding.root, getString(R.string.saved_success), Snackbar.LENGTH_SHORT).show()

        startActivity(Intent(this, ReportActivity::class.java).putExtra(ReportActivity.EXTRA_ID, id))
    }
}
