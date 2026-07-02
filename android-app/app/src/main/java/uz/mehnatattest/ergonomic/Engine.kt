package uz.mehnatattest.ergonomic

import java.text.SimpleDateFormat
import java.util.Calendar
import java.util.Locale
import kotlin.math.abs
import kotlin.math.max
import kotlin.math.min
import kotlin.math.round

/**
 * Yarim tayyor mahsulot va xomashyoni qo'lda solish-ortish (yuklash-tushirish) ishlarida
 * band ishchilarning mehnat xavfsizligini baholovchi hisoblash mexanizmi:
 *  - jismoniy (ergonomik) yuklanish - NIOSH (1994) ko'tarish tenglamasi + R2.2.2006-05
 *    uslubiyatiga asoslangan og'irlik darajasi ko'rsatkichlari;
 *  - psixofiziologik zo'riqish ko'rsatkichlari;
 *  - inson omili (tajriba, o'qitilganlik, charchoq, sog'liq holati) xavfi;
 *  - alyuminiy profil ishlab chiqarishga xos ish muhiti omillari.
 *
 * Bu fayl MEHNAT-ATTEST loyihasidagi App\Services\ErgonomicAssessmentService (PHP) va
 * desktop-app/engine.py (Python) bilan bir xil formula va chegaralarga asoslanadi -
 * uchala dastur bir xil kirish ma'lumotlari uchun bir xil natija beradi.
 */

data class AssessmentInput(
    val employeeName: String? = null,
    val workerGender: String = "erkak",
    val employeeAge: Int? = null,
    val experienceYears: Double? = null,
    val operationType: String = "qolda",
    val loadDescription: String? = null,
    val loadMassKg: Double,
    val liftsPerShift: Int = 0,
    val liftsPerMinute: Double = 0.0,
    val shiftDurationHours: Double = 8.0,
    val durationCategory: String = "long",
    val horizontalDistanceCm: Double,
    val verticalLocationCm: Double,
    val verticalTravelCm: Double,
    val asymmetryAngleDeg: Double = 0.0,
    val couplingQuality: String = "fair",
    val postureType: String = "erkin",
    val staticLoadKgs: Double? = null,
    val bodyInclinationsPerShift: Int = 0,
    val walkingDistanceKm: Double = 0.0,
    val attentionConcentrationPercent: Int = 0,
    val signalsPerHour: Int = 0,
    val responsibilityLevel: String = "ozi_uchun",
    val monotonyOperationsCount: Int = 0,
    val nightShift: Boolean = false,
    val temperatureC: Double? = null,
    val metalDustMgM3: Double? = null,
    val noiseLevelDb: Double? = null,
    val usesPpe: Boolean = true,
    val trainingCompleted: Boolean = false,
    val lastTrainingAt: String? = null, // "yyyy-MM-dd"
    val fatigueSelfScore: Int = 1,
    val healthGroup: String = "soglom",
    val priorIncidentsCount: Int = 0,
    val notes: String? = null,
)

data class AssessmentResult(
    val rwlKg: Double,
    val liftingIndex: Double,
    val severityClass: String,
    val strainClass: String,
    val humanFactorRiskScore: Int,
    val integralSafetyIndex: Int,
    val riskCategory: String,
    val recommendations: List<String>,
)

object Engine {

    private const val LC = 23.0
    private const val WOMEN_CONTINUOUS_LIMIT_KG = 10.0
    private const val WOMEN_ALTERNATING_LIMIT_KG = 15.0

    // NIOSH (1994) chastota multiplikatori jadvali (Table 5), soddalashtirilgan.
    private val FREQUENCY_TABLE: Map<String, List<Pair<Double, Map<String, Double>>>> = mapOf(
        "short" to listOf(
            0.2 to mapOf("lt75" to 1.00, "ge75" to 1.00),
            0.5 to mapOf("lt75" to 0.97, "ge75" to 0.97),
            1.0 to mapOf("lt75" to 0.94, "ge75" to 0.94),
            2.0 to mapOf("lt75" to 0.91, "ge75" to 0.91),
            3.0 to mapOf("lt75" to 0.88, "ge75" to 0.88),
            4.0 to mapOf("lt75" to 0.84, "ge75" to 0.84),
            5.0 to mapOf("lt75" to 0.80, "ge75" to 0.80),
            6.0 to mapOf("lt75" to 0.75, "ge75" to 0.75),
            7.0 to mapOf("lt75" to 0.70, "ge75" to 0.70),
            8.0 to mapOf("lt75" to 0.60, "ge75" to 0.60),
            9.0 to mapOf("lt75" to 0.52, "ge75" to 0.52),
            10.0 to mapOf("lt75" to 0.45, "ge75" to 0.45),
            11.0 to mapOf("lt75" to 0.41, "ge75" to 0.41),
            12.0 to mapOf("lt75" to 0.37, "ge75" to 0.37),
            13.0 to mapOf("lt75" to 0.00, "ge75" to 0.34),
            14.0 to mapOf("lt75" to 0.00, "ge75" to 0.31),
            15.0 to mapOf("lt75" to 0.00, "ge75" to 0.28),
        ),
        "moderate" to listOf(
            0.2 to mapOf("lt75" to 0.95, "ge75" to 0.95),
            0.5 to mapOf("lt75" to 0.92, "ge75" to 0.92),
            1.0 to mapOf("lt75" to 0.88, "ge75" to 0.88),
            2.0 to mapOf("lt75" to 0.84, "ge75" to 0.84),
            3.0 to mapOf("lt75" to 0.79, "ge75" to 0.79),
            4.0 to mapOf("lt75" to 0.72, "ge75" to 0.72),
            5.0 to mapOf("lt75" to 0.60, "ge75" to 0.60),
            6.0 to mapOf("lt75" to 0.50, "ge75" to 0.50),
            7.0 to mapOf("lt75" to 0.42, "ge75" to 0.42),
            8.0 to mapOf("lt75" to 0.35, "ge75" to 0.35),
            9.0 to mapOf("lt75" to 0.30, "ge75" to 0.30),
            10.0 to mapOf("lt75" to 0.26, "ge75" to 0.26),
            11.0 to mapOf("lt75" to 0.00, "ge75" to 0.23),
            12.0 to mapOf("lt75" to 0.00, "ge75" to 0.21),
        ),
        "long" to listOf(
            0.2 to mapOf("lt75" to 0.85, "ge75" to 0.85),
            0.5 to mapOf("lt75" to 0.81, "ge75" to 0.81),
            1.0 to mapOf("lt75" to 0.75, "ge75" to 0.75),
            2.0 to mapOf("lt75" to 0.65, "ge75" to 0.65),
            3.0 to mapOf("lt75" to 0.55, "ge75" to 0.55),
            4.0 to mapOf("lt75" to 0.45, "ge75" to 0.45),
            5.0 to mapOf("lt75" to 0.35, "ge75" to 0.35),
            6.0 to mapOf("lt75" to 0.27, "ge75" to 0.27),
            7.0 to mapOf("lt75" to 0.22, "ge75" to 0.22),
            8.0 to mapOf("lt75" to 0.18, "ge75" to 0.18),
            9.0 to mapOf("lt75" to 0.15, "ge75" to 0.15),
            10.0 to mapOf("lt75" to 0.13, "ge75" to 0.13),
        ),
    )

    fun evaluate(data: AssessmentInput): AssessmentResult {
        val (rwl, liftingIndex) = calculateNiosh(data)
        val (severityClass, severityScore) = classifySeverity(data, liftingIndex)
        val (strainClass, strainScore) = classifyStrain(data)
        val humanFactorScore = scoreHumanFactor(data)
        val envPenalty = environmentalPenalty(data)

        val integral = integralSafetyIndex(severityScore, strainScore, humanFactorScore, envPenalty)
        val riskCategory = riskCategoryLabel(integral)

        val recommendations = buildRecommendations(data, rwl, liftingIndex, severityClass, strainClass, humanFactorScore)

        return AssessmentResult(
            rwlKg = rwl,
            liftingIndex = liftingIndex,
            severityClass = severityClass,
            strainClass = strainClass,
            humanFactorRiskScore = humanFactorScore,
            integralSafetyIndex = integral,
            riskCategory = riskCategory,
            recommendations = recommendations,
        )
    }

    /** NIOSH (1994) ko'tarish tenglamasi: RWL = LC x HM x VM x DM x AM x FM x CM. */
    private fun calculateNiosh(data: AssessmentInput): Pair<Double, Double> {
        val h = max(data.horizontalDistanceCm, 25.0)
        val v = min(max(data.verticalLocationCm, 0.0), 175.0)
        val d = max(data.verticalTravelCm, 25.0)
        val a = data.asymmetryAngleDeg

        val hm = if (h > 63) 0.0 else 25 / h
        val vm = 1 - (0.003 * abs(v - 75))
        val dm = if (d > 175) 0.0 else 0.82 + (4.5 / d)
        val am = if (a > 135) 0.0 else 1 - (0.0032 * a)
        val fm = frequencyMultiplier(data.liftsPerMinute, data.durationCategory, v)
        val cm = couplingMultiplier(data.couplingQuality, v)

        var rwl = LC * hm * vm * dm * am * fm * cm
        rwl = round(max(rwl, 0.0) * 100) / 100

        val li = if (rwl > 0) round((data.loadMassKg / rwl) * 100) / 100 else 99.99

        return rwl to li
    }

    private fun frequencyMultiplier(f: Double, duration: String, v: Double): Double {
        val column = if (v < 75) "lt75" else "ge75"
        val rows = FREQUENCY_TABLE[duration] ?: FREQUENCY_TABLE.getValue("long")

        for ((freq, values) in rows) {
            if (f <= freq) return values.getValue(column)
        }
        return 0.0
    }

    private fun couplingMultiplier(quality: String, v: Double): Double = when (quality) {
        "good" -> 1.00
        "poor" -> 0.90
        else -> if (v < 75) 0.95 else 1.00 // fair
    }

    /** Og'irlik darajasi (jismoniy yuklanish) klassini eng noqulay ko'rsatkich bo'yicha aniqlaydi. */
    private fun classifySeverity(data: AssessmentInput, liftingIndex: Double): Pair<String, Int> {
        val scores = mutableListOf(liftingIndexScore(liftingIndex))

        data.staticLoadKgs?.let { scores.add(bracketScore(it, listOf(43000.0, 97000.0, 208000.0, 245000.0))) }

        scores.add(
            when (data.postureType) {
                "erkin" -> 0
                "epizodik_noqulay" -> 1
                "davriy_noqulay" -> 2
                "majburiy" -> 3
                "qattiq_majburiy" -> 4
                else -> 1
            }
        )

        scores.add(bracketScore(data.bodyInclinationsPerShift.toDouble(), listOf(50.0, 100.0, 300.0, 300.0)))
        scores.add(bracketScore(data.walkingDistanceKm, listOf(4.0, 10.0, 15.0, 15.0)))

        if (data.workerGender == "ayol") {
            when {
                data.loadMassKg > WOMEN_ALTERNATING_LIMIT_KG -> scores.add(5)
                data.loadMassKg > WOMEN_CONTINUOUS_LIMIT_KG -> scores.add(3)
            }
        }

        val worst = scores.maxOrNull()!!
        return scoreToClassLabel(worst) to worst
    }

    private fun liftingIndexScore(li: Double): Int = when {
        li <= 0.5 -> 0
        li <= 1.0 -> 1
        li <= 1.5 -> 2
        li <= 2.0 -> 3
        li <= 4.0 -> 4
        else -> 5
    }

    /** limits = [class1_max, class2_max, class3.1_max, class3.2_max] */
    private fun bracketScore(value: Double, limits: List<Double>): Int = when {
        value <= limits[0] -> 0
        value <= limits[1] -> 1
        value <= limits[2] -> 2
        value <= limits[3] -> 3
        else -> 4
    }

    private fun scoreToClassLabel(score: Int): String = when {
        score <= 0 -> "1"
        score == 1 -> "2"
        score == 2 -> "3.1"
        score == 3 -> "3.2"
        score == 4 -> "3.3"
        else -> "4"
    }

    /** Zo'riqish (psixofiziologik yuklanish) klassi. */
    private fun classifyStrain(data: AssessmentInput): Pair<String, Int> {
        val scores = mutableListOf(
            bracketScore(data.attentionConcentrationPercent.toDouble(), listOf(25.0, 50.0, 75.0, 75.0)),
            bracketScore(data.signalsPerHour.toDouble(), listOf(75.0, 175.0, 300.0, 300.0)),
            bracketScore(data.monotonyOperationsCount.toDouble(), listOf(10.0, 6.0, 4.0, 4.0)),
            when (data.responsibilityLevel) {
                "ozi_uchun" -> 0
                "jamoa_uchun" -> 2
                "xavfsizlik_uchun" -> 3
                else -> 1
            },
        )

        when {
            data.shiftDurationHours > 12 -> scores.add(4)
            data.shiftDurationHours > 8 -> scores.add(2)
        }

        if (data.nightShift) scores.add(2)

        val worst = scores.maxOrNull()!!
        return scoreToClassLabel(worst) to worst
    }

    /** Inson omili xavf balli (0-100, kattaroq = xavfliroq). */
    private fun scoreHumanFactor(data: AssessmentInput): Int {
        var score = 0.0

        val experience = data.experienceYears ?: 0.0
        score += when {
            experience < 1 -> 25
            experience < 3 -> 15
            experience < 5 -> 5
            else -> 0
        }

        if (!data.trainingCompleted) {
            score += 20
        } else if (data.lastTrainingAt != null && monthsSince(data.lastTrainingAt) > 12) {
            score += 10
        }

        val fatigue = data.fatigueSelfScore
        score += max(0, fatigue - 1) * 5

        val age = data.employeeAge ?: 30
        if (age in 1..17 || age > 55) score += 10

        if (data.healthGroup == "cheklangan" || data.healthGroup == "nogironligi_bor") score += 15

        score += min(data.priorIncidentsCount * 10, 30)

        return min(score.toInt(), 100)
    }

    private fun monthsSince(dateStr: String): Int {
        val format = SimpleDateFormat("yyyy-MM-dd", Locale.US)
        val parsed = runCatching { format.parse(dateStr) }.getOrNull() ?: return 0

        val then = Calendar.getInstance().apply { time = parsed }
        val now = Calendar.getInstance()

        val yearDiff = now.get(Calendar.YEAR) - then.get(Calendar.YEAR)
        val monthDiff = now.get(Calendar.MONTH) - then.get(Calendar.MONTH)
        return yearDiff * 12 + monthDiff
    }

    private fun environmentalPenalty(data: AssessmentInput): Int {
        var penalty = 0

        data.temperatureC?.let { if (it < 10 || it > 32) penalty += 8 }
        data.metalDustMgM3?.let { if (it > 6) penalty += 8 }
        data.noiseLevelDb?.let { if (it > 80) penalty += 6 }
        if (!data.usesPpe) penalty += 10

        return penalty
    }

    private fun integralSafetyIndex(severityScore: Int, strainScore: Int, humanFactorScore: Int, envPenalty: Int): Int {
        val penalty = (severityScore * 8) + (strainScore * 6) + (humanFactorScore * 0.3) + envPenalty
        return max(0, min(100, round(100 - penalty).toInt()))
    }

    private fun riskCategoryLabel(integral: Int): String = when {
        integral >= 85 -> "Optimal"
        integral >= 70 -> "Ruxsat etiladigan"
        integral >= 55 -> "Zararli — past daraja (3.1)"
        integral >= 40 -> "Zararli — o'rta daraja (3.2)"
        integral >= 25 -> "Zararli — yuqori daraja (3.3-3.4)"
        else -> "Xavfli (4)"
    }

    private fun buildRecommendations(
        data: AssessmentInput,
        rwl: Double,
        liftingIndex: Double,
        severityClass: String,
        strainClass: String,
        humanFactorScore: Int,
    ): List<String> {
        val recommendations = mutableListOf<String>()

        if (liftingIndex > 1.0) {
            recommendations.add(
                "Ko'tarish indeksi (LI=%.2f) me'yordan yuqori — yukni mexanizatsiyalash (ko'targich, konveyer), ".format(liftingIndex) +
                    "yuk massasini yoki ko'tarish chastotasini kamaytirish tavsiya etiladi."
            )
        }

        if (data.workerGender == "ayol" && data.loadMassKg > WOMEN_CONTINUOUS_LIMIT_KG) {
            recommendations.add(
                "Ayol ishchilar uchun qo'lda ko'tarish og'irligi qonuniy chegaradan (doimiy — 10 kg, " +
                    "navbatlashtirib — 15 kg) oshib ketgan — operatsiyani qayta tashkil eting."
            )
        }

        if (severityClass in listOf("3.3", "3.4", "4")) {
            recommendations.add(
                "Ish holati va statik yuk ko'rsatkichlari zararli darajada — ishchi holatini (poza) " +
                    "yaxshilash, tanaffuslar sonini oshirish zarur."
            )
        }

        if (strainClass in listOf("3.1", "3.2", "3.3", "3.4")) {
            recommendations.add(
                "Psixofiziologik zo'riqish yuqori — monoton operatsiyalar sonini kamaytirish, tungi " +
                    "smenalarni cheklash va qo'shimcha tanaffuslar joriy etish tavsiya etiladi."
            )
        }

        if (humanFactorScore >= 40) {
            recommendations.add(
                "Inson omili xavfi yuqori — ishchini mehnat xavfsizligi bo'yicha qayta o'qitish va " +
                    "tibbiy ko'rikdan o'tkazish tavsiya etiladi."
            )
        }

        data.temperatureC?.let {
            if (it < 10 || it > 32) {
                recommendations.add(
                    "Ish muhiti harorati me'yordan chetga chiqqan (alyuminiy pressi/eritish uchastkasi " +
                        "yaqinida) — issiqlikdan/sovuqdan himoya vositalari va tanaffuslar tashkil etilsin."
                )
            }
        }

        data.metalDustMgM3?.let {
            if (it > 6) {
                recommendations.add(
                    "Metall changi konsentratsiyasi me'yordan yuqori — mahalliy shamollatish va nafas olish " +
                        "organlarini himoya vositalaridan foydalanish shart."
                )
            }
        }

        if (!data.usesPpe) {
            recommendations.add(
                "Shaxsiy himoya vositalaridan (qo'lqop, poyabzal, kamar) muntazam foydalanilmayapti — " +
                    "ta'minot va nazoratni yo'lga qo'ying."
            )
        }

        if (recommendations.isEmpty()) {
            recommendations.add(
                "Asosiy ko'rsatkichlar me'yor doirasida — mavjud xavfsizlik tartib-qoidalarini saqlab qolish tavsiya etiladi."
            )
        }

        return recommendations
    }
}
