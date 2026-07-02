package uz.mehnatattest.ergonomic

import android.graphics.Color
import android.os.Bundle
import android.widget.LinearLayout
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import uz.mehnatattest.ergonomic.databinding.ActivityReportBinding

class ReportActivity : AppCompatActivity() {

    private lateinit var binding: ActivityReportBinding

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityReportBinding.inflate(layoutInflater)
        setContentView(binding.root)
        setSupportActionBar(binding.toolbar)
        supportActionBar?.setDisplayHomeAsUpEnabled(true)
        binding.toolbar.setNavigationOnClickListener { finish() }

        val id = intent.getLongExtra(EXTRA_ID, -1L)
        val record = AssessmentDbHelper(this).get(id)
        if (record == null) {
            finish()
            return
        }
        val (input, result) = record
        render(input, result)
    }

    private fun render(input: AssessmentInput, result: AssessmentResult) {
        val subject = listOfNotNull(input.employeeName, input.loadDescription)
            .joinToString(" — ")
            .ifBlank { getString(R.string.report_title) }
        binding.tvSubject.text = subject

        val integral = result.integralSafetyIndex
        val color = when {
            integral >= 70 -> Color.parseColor("#176B3A")
            integral >= 40 -> Color.parseColor("#B8841E")
            else -> Color.parseColor("#B83232")
        }

        binding.tvIntegral.text = "$integral/100"
        binding.tvIntegral.setTextColor(color)
        binding.tvRiskCategory.text = result.riskCategory
        binding.tvRiskCategory.setTextColor(color)

        binding.statsContainer.removeAllViews()
        addStatRow(getString(R.string.result_lifting_index), result.liftingIndex.toString())
        addStatRow(getString(R.string.result_rwl), "${result.rwlKg} kg")
        addStatRow(getString(R.string.result_severity_class), result.severityClass)
        addStatRow(getString(R.string.result_strain_class), result.strainClass)
        addStatRow(getString(R.string.result_human_factor), "${result.humanFactorRiskScore}/100")

        binding.tvRecommendations.text = result.recommendations.joinToString("\n\n") { "• $it" }
    }

    private fun addStatRow(label: String, value: String) {
        val row = LinearLayout(this).apply {
            orientation = LinearLayout.HORIZONTAL
            layoutParams = LinearLayout.LayoutParams(
                LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT
            ).also { it.bottomMargin = (4 * resources.displayMetrics.density).toInt() }
        }
        row.addView(TextView(this).apply {
            text = label
            layoutParams = LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.WRAP_CONTENT, 1f)
        })
        row.addView(TextView(this).apply {
            text = value
            setTypeface(typeface, android.graphics.Typeface.BOLD)
        })
        binding.statsContainer.addView(row)
    }

    companion object {
        const val EXTRA_ID = "assessment_id"
    }
}
