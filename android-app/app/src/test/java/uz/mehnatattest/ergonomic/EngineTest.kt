package uz.mehnatattest.ergonomic

import org.junit.Assert.assertEquals
import org.junit.Assert.assertTrue
import org.junit.Test
import kotlin.math.abs

class EngineTest {

    private fun baseInput(
        loadMassKg: Double = 20.0,
        liftsPerMinute: Double = 1.0,
        horizontalDistanceCm: Double = 25.0,
        verticalLocationCm: Double = 75.0,
        verticalTravelCm: Double = 25.0,
        couplingQuality: String = "good",
        durationCategory: String = "short",
        workerGender: String = "erkak",
        experienceYears: Double? = 10.0,
        trainingCompleted: Boolean = true,
        fatigueSelfScore: Int = 1,
        priorIncidentsCount: Int = 0,
    ) = AssessmentInput(
        workerGender = workerGender,
        employeeAge = 30,
        experienceYears = experienceYears,
        loadMassKg = loadMassKg,
        liftsPerMinute = liftsPerMinute,
        durationCategory = durationCategory,
        horizontalDistanceCm = horizontalDistanceCm,
        verticalLocationCm = verticalLocationCm,
        verticalTravelCm = verticalTravelCm,
        couplingQuality = couplingQuality,
        bodyInclinationsPerShift = 10,
        walkingDistanceKm = 1.0,
        attentionConcentrationPercent = 20,
        signalsPerHour = 10,
        monotonyOperationsCount = 20,
        trainingCompleted = trainingCompleted,
        fatigueSelfScore = fatigueSelfScore,
        priorIncidentsCount = priorIncidentsCount,
    )

    @Test
    fun `niosh rwl and lifting index for a favourable task`() {
        // H=25 -> HM=1, V=75 -> VM=1, D=25 -> DM=1.0, A=0 -> AM=1,
        // freq=1/min short duration V>=75 -> FM=0.94, coupling good -> CM=1.
        // RWL = 23 * 0.94 = 21.62
        val result = Engine.evaluate(baseInput())

        assertTrue(abs(result.rwlKg - 21.62) < 0.05)
        assertTrue(abs(result.liftingIndex - (20.0 / 21.62)) < 0.02)
    }

    @Test
    fun `lifting index rises when horizontal distance and load increase`() {
        val favourable = Engine.evaluate(baseInput())
        val strained = Engine.evaluate(
            baseInput(loadMassKg = 35.0, horizontalDistanceCm = 55.0, verticalTravelCm = 90.0)
                .copy(liftsPerMinute = 8.0, durationCategory = "long")
        )

        assertTrue(strained.liftingIndex > favourable.liftingIndex)
        assertTrue(strained.integralSafetyIndex < favourable.integralSafetyIndex)
    }

    @Test
    fun `women lifting above legal limit is flagged hazardous`() {
        val result = Engine.evaluate(baseInput(workerGender = "ayol", loadMassKg = 20.0))

        assertEquals("4", result.severityClass)
        assertTrue(result.recommendations.any { it.contains("Ayol ishchilar") })
    }

    @Test
    fun `human factor risk increases with inexperience and no training`() {
        val experienced = Engine.evaluate(baseInput())
        val novice = Engine.evaluate(
            baseInput(experienceYears = 0.5, trainingCompleted = false, fatigueSelfScore = 5, priorIncidentsCount = 2)
        )

        assertTrue(novice.humanFactorRiskScore > experienced.humanFactorRiskScore)
        assertTrue(novice.integralSafetyIndex < experienced.integralSafetyIndex)
    }

    @Test
    fun `aluminum profile example matches web and desktop versions`() {
        val result = Engine.evaluate(
            AssessmentInput(
                employeeName = "Aliyev Anvar",
                workerGender = "erkak",
                employeeAge = 34,
                experienceYears = 4.0,
                operationType = "qolda",
                loadDescription = "6 metrli alyuminiy profil bog'lami",
                loadMassKg = 22.0,
                liftsPerShift = 180,
                liftsPerMinute = 4.0,
                shiftDurationHours = 8.0,
                durationCategory = "long",
                horizontalDistanceCm = 30.0,
                verticalLocationCm = 75.0,
                verticalTravelCm = 60.0,
                asymmetryAngleDeg = 30.0,
                couplingQuality = "fair",
                postureType = "davriy_noqulay",
                staticLoadKgs = 25000.0,
                bodyInclinationsPerShift = 150,
                walkingDistanceKm = 6.0,
                attentionConcentrationPercent = 40,
                signalsPerHour = 60,
                responsibilityLevel = "jamoa_uchun",
                monotonyOperationsCount = 5,
                temperatureC = 34.0,
                metalDustMgM3 = 5.0,
                noiseLevelDb = 82.0,
                usesPpe = true,
                trainingCompleted = true,
                fatigueSelfScore = 3,
                healthGroup = "soglom",
                priorIncidentsCount = 0,
            )
        )

        assertTrue(abs(result.rwlKg - 6.98) < 0.05)
        assertTrue(abs(result.liftingIndex - 3.15) < 0.05)
        assertEquals(38, result.integralSafetyIndex)
        assertEquals("Zararli — yuqori daraja (3.3-3.4)", result.riskCategory)
    }
}
