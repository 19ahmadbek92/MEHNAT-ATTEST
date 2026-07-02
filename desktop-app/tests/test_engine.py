import os
import sys
import unittest

sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

import engine  # noqa: E402


def base_input(**overrides):
    data = {
        "load_mass_kg": 20,
        "lifts_per_shift": 60,
        "lifts_per_minute": 1,
        "shift_duration_hours": 8,
        "worker_gender": "erkak",
        "horizontal_distance_cm": 25,
        "vertical_location_cm": 75,
        "vertical_travel_cm": 25,
        "asymmetry_angle_deg": 0,
        "coupling_quality": "good",
        "duration_category": "short",
        "posture_type": "erkin",
        "body_inclinations_per_shift": 10,
        "walking_distance_km": 1,
        "attention_concentration_percent": 20,
        "signals_per_hour": 10,
        "responsibility_level": "ozi_uchun",
        "monotony_operations_count": 20,
        "night_shift": False,
        "uses_ppe": True,
        "fatigue_self_score": 1,
        "health_group": "soglom",
        "prior_incidents_count": 0,
        "experience_years": 10,
        "training_completed": True,
        "employee_age": 30,
    }
    data.update(overrides)
    return data


class ErgonomicEngineTest(unittest.TestCase):
    def test_niosh_rwl_and_lifting_index_for_a_favourable_task(self):
        # H=25 -> HM=1, V=75 -> VM=1, D=25 -> DM=1.0, A=0 -> AM=1,
        # freq=1/min short duration V>=75 -> FM=0.94, coupling good -> CM=1.
        # RWL = 23 * 0.94 = 21.62
        result = engine.evaluate(base_input())

        self.assertAlmostEqual(result["rwl_kg"], 21.62, delta=0.05)
        self.assertAlmostEqual(result["lifting_index"], 20 / 21.62, delta=0.02)

    def test_lifting_index_rises_when_horizontal_distance_and_load_increase(self):
        favourable = engine.evaluate(base_input())
        strained = engine.evaluate(base_input(
            load_mass_kg=35,
            horizontal_distance_cm=55,
            vertical_travel_cm=90,
            lifts_per_minute=8,
            duration_category="long",
        ))

        self.assertGreater(strained["lifting_index"], favourable["lifting_index"])
        self.assertGreater(favourable["integral_safety_index"], strained["integral_safety_index"])

    def test_women_lifting_above_legal_limit_is_flagged_hazardous(self):
        result = engine.evaluate(base_input(worker_gender="ayol", load_mass_kg=20))

        self.assertEqual(result["severity_class"], "4")
        self.assertTrue(any("Ayol ishchilar" in r for r in result["recommendations"]))

    def test_human_factor_risk_increases_with_inexperience_and_no_training(self):
        experienced = engine.evaluate(base_input())
        novice = engine.evaluate(base_input(
            experience_years=0.5,
            training_completed=False,
            fatigue_self_score=5,
            prior_incidents_count=2,
        ))

        self.assertGreater(novice["human_factor_risk_score"], experienced["human_factor_risk_score"])
        self.assertGreater(experienced["integral_safety_index"], novice["integral_safety_index"])

    def test_integral_safety_index_stays_within_bounds(self):
        worst_case = engine.evaluate(base_input(
            load_mass_kg=60,
            horizontal_distance_cm=60,
            vertical_travel_cm=150,
            lifts_per_minute=15,
            duration_category="long",
            posture_type="qattiq_majburiy",
            static_load_kgs=300000,
            body_inclinations_per_shift=400,
            walking_distance_km=20,
            attention_concentration_percent=90,
            signals_per_hour=400,
            responsibility_level="xavfsizlik_uchun",
            monotony_operations_count=2,
            night_shift=True,
            shift_duration_hours=13,
            temperature_c=45,
            metal_dust_mg_m3=15,
            noise_level_db=95,
            uses_ppe=False,
            experience_years=0,
            training_completed=False,
            fatigue_self_score=5,
            health_group="nogironligi_bor",
            prior_incidents_count=5,
        ))

        self.assertGreaterEqual(worst_case["integral_safety_index"], 0)
        self.assertLessEqual(worst_case["integral_safety_index"], 100)
        self.assertEqual(worst_case["risk_category"], "Xavfli (4)")
        self.assertTrue(worst_case["recommendations"])

    def test_aluminum_profile_example_matches_php_service_output(self):
        # Ushbu kirish qiymatlari veb-versiyadagi (PHP) "Namuna bilan to'ldirish"
        # tugmasi bilan bir xil - ikkala dastur bir xil natija berishi kerak.
        result = engine.evaluate(base_input(
            worker_gender="erkak",
            employee_age=34,
            experience_years=4,
            load_mass_kg=22,
            lifts_per_shift=180,
            lifts_per_minute=4,
            shift_duration_hours=8,
            duration_category="long",
            horizontal_distance_cm=30,
            vertical_location_cm=75,
            vertical_travel_cm=60,
            asymmetry_angle_deg=30,
            coupling_quality="fair",
            posture_type="davriy_noqulay",
            static_load_kgs=25000,
            body_inclinations_per_shift=150,
            walking_distance_km=6,
            attention_concentration_percent=40,
            signals_per_hour=60,
            responsibility_level="jamoa_uchun",
            monotony_operations_count=5,
            night_shift=False,
            temperature_c=34,
            metal_dust_mg_m3=5,
            noise_level_db=82,
            uses_ppe=True,
            training_completed=True,
            fatigue_self_score=3,
            health_group="soglom",
            prior_incidents_count=0,
        ))

        self.assertAlmostEqual(result["rwl_kg"], 6.98, delta=0.05)
        self.assertAlmostEqual(result["lifting_index"], 3.15, delta=0.05)
        self.assertEqual(result["integral_safety_index"], 38)
        self.assertEqual(result["risk_category"], "Zararli — yuqori daraja (3.3-3.4)")


if __name__ == "__main__":
    unittest.main()
