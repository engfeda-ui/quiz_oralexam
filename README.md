# 🎙️ Moodle Quiz Report Plugin: Oral & Practical Exam Evaluator (`quiz_oralexam`)

[![Moodle Compatibility](https://img.shields.io/badge/Moodle-4.0%20to%205.0%2B-orange.svg?style=flat-square)](https://moodle.org)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%20%7C%208.2%20%7C%208.3-blue.svg?style=flat-square)](https://php.net)
[![License](https://img.shields.io/badge/License-GPL%20v3-green.svg?style=flat-square)](http://www.gnu.org/copyleft/gpl.html)
[![Version](https://img.shields.io/badge/Version-v1.0.0-blue.svg?style=flat-square)](https://github.com/engfeda-ui/quiz_oralexam)

A specialized Moodle Quiz Report sub-plugin designed for **in-person Oral and Practical Examinations (OSCE / Technical Workshops)**. It allows examiners and instructors to directly assess students question-by-question live on behalf of the student without student submission, linking each question directly to its competency from `qbank_comp_ext`.

---

## ✨ Key Features

- **Direct Examiner Grading:** Conduct oral and practical exams where the teacher marks questions one-by-one live without requiring the student to log in or submit.
- **Competency-Aware Assessment:** Questions display linked competencies directly from `qbank_comp_ext_qmap` and question tags.
- **Touch & Tablet Friendly UI:** Fast-click scoring buttons (`0%`, `50%`, `100%`) alongside custom score inputs and question-level feedback.
- **Full Gradebook & Analytics Integration:** Programmatically starts and finalizes official Moodle attempts (`quiz_attempt`), pushing grades directly into the Course Gradebook and updating `local_comp_report_ext` and SANAD reports instantly.
- **Group Filtering & Candidate Tracking:** Group selectors with instant identification of evaluated vs. pending students.
- **Retake & Multi-Attempt Support:** Easily review past attempts or initiate new retakes.
- **Bilingual Support:** Full English and Arabic (`ar`) language packs.

---

## 📋 Requirements

| Component | Minimum Version |
|---|---|
| **Moodle Core** | 4.0, 4.1, 4.2, 4.3, 4.4, 4.5, 5.0+ |
| **PHP Runtime** | PHP 8.1, PHP 8.2, PHP 8.3 |
| **Dependencies** | `mod_quiz` (core), `qbank_comp_ext` (recommended for competency mapping) |

---

## 🚀 Installation

Place this plugin directory under your Moodle installation at:
```
mod/quiz/report/oralexam/
```
Then visit **Site administration > Notifications** to complete the installation.

---

## 📋 Changelog

### v1.0.0 (2026-09-07)
- Initial production release.
- Interactive oral exam evaluation dashboard (`report.php`).
- Programmatic attempt generation and question grading engine (`evaluator.php`).
- Direct competency integration with `qbank_comp_ext_qmap`.
- Bilingual Arabic & English translations.
- Fully integrated packaging and deployment configuration.
