# 🎙️ Moodle Quiz Report Plugin: Oral & Practical Exam Evaluator (`quiz_oralexam`)

[![Moodle Compatibility](https://img.shields.io/badge/Moodle-4.0%20to%205.0%2B-orange.svg?style=flat-square)](https://moodle.org)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%20%7C%208.2%20%7C%208.3-blue.svg?style=flat-square)](https://php.net)
[![License](https://img.shields.io/badge/License-GPL%20v3-green.svg?style=flat-square)](http://www.gnu.org/copyleft/gpl.html)
[![Version](https://img.shields.io/badge/Version-v1.1.9-blue.svg?style=flat-square)](https://github.com/engfeda-ui/quiz_oralexam)

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

### v1.1.9 (2026-09-08)
- **Enforced Sub-Plugin Dependency (`quizaccess_oralexam`):** Declared `$plugin->dependencies['quizaccess_oralexam'] = 2026090800` in `version.php`. Moodle installer will now automatically require both `quiz_oralexam` and `quizaccess_oralexam` to be installed together, preventing standalone installations and ensuring strict student access control and automatic evaluation locking.

### v1.1.8 (2026-09-08)
- **Enhanced Candidate Card Typography & Compact Status Badges:** Replaced the wide text-heavy `Pending Evaluation` label with a sleek, circular pending clock icon badge (??) with native tooltip hover. Expanded the left sidebar width to `365px` to provide ample breathing room for full student names and academic ID pills without text truncations, while retaining the score badge (`30 pts`) for evaluated candidates.

### v1.1.7 (2026-09-08)
- **Fixed Candidate List Collapsing / Shrinking in All Participants View:** Fixed CSS flexbox layout issue where `.oralexam-candidate-item` lacked `flex-shrink: 0` and `min-height: 52px`, causing all 40 student cards in the "All participants" sidebar to squish into 18px empty bars when squeezed inside the flex container. Added explicit scroll boundaries (`max-height: 720px`, `min-height: 0`, `flex: 1 1 auto`) to ensure smooth scrolling and complete visibility of student names, IDs, and evaluation status badges.

### v1.1.6 (2026-09-08)
- **Fixed PHP Type Hint Mismatch (`cm_info` vs `stdClass`):** Removed rigid `\stdClass` type hint on `$cm`, `$quiz`, and `$course` in `evaluator::submit_evaluation()`. In Moodle 4.x/5.x, `$cm` is an instance of `\cm_info`, which caused a PHP `TypeError` during evaluation submission.
- **Upgraded Exception Handling:** Changed `catch (\Exception $e)` to `catch (\Throwable $e)` in `report.php` to safely capture any potential runtime errors or type errors and display user-friendly notifications.

### v1.1.5 (2026-09-08)
- **Fixed Form POST Routing & Redirect Loop:** Added missing hidden fields (`id`, `mode`, `action`) to the oral exam evaluation form and disabled HTML entity escaping on the form action URL (`$actionurl->out(false)`). This prevents Moodle core `mod/quiz/report.php` from missing the `$mode` parameter and dropping POST submissions into an overview redirect.
- **Robust Multi-Attempt Submission:** Ensured subsequent attempts (Attempt #2, #3, #4, ...) submitted from the oral assessment sheet are correctly recognized and processed by the POST evaluator.

### v1.1.4 (2026-09-08)
- **Security & Parameter Hardening:** Changed `$action` parameter handling from `PARAM_ALPHA` to `PARAM_ALPHANUMEXT` (ensuring `submit_eval` passes cleanly) and sanitized user feedback notes via `PARAM_CLEANHTML`.
- **Database Optimization (No N+1 Queries):** Replaced per-candidate loop queries in `evaluator::get_candidates()` with a single batch `quiz_attempts` fetch, and unified `get_question_competencies()` into an optimized prioritized SQL query.
- **Course Enrollment Verification:** Added explicit `is_enrolled()` check in `submit_evaluation()` before accepting assessments.
- **Enhanced Bilingual i18n:** Added translation strings for empty student prompt, enrollment errors, and unrated question warning modal (`clickstudentprompt`, `studentnotenrolled`, `unratedwarning`).
- **Normalized Arabic Search:** Enhanced live student search filter with Arabic diacritics removal and character normalization (أ/إ/آ -> ا, ة -> ه, ى -> ي).
- **Codebase & CSS Cleanup:** Eliminated duplicate `.oralexam-controls-card` definition in `styles.css`, fixed redundant `global $DB` declaration, and secured competency idnumber output escaping.

### v1.1.3 (2026-09-08)
- **Automatic Multi-Attempt Incrementing:** Each evaluation recorded for a student with completed attempts now creates a brand new attempt (#1, #2, #3, ...) in `quiz_attempts` and updates Moodle Gradebook results.
- **Attempt History Display:** Added visual indicator showing previous attempt scores and current attempt number being recorded (`تسجيل محاولة جديدة رقم #X`).
- **Unrated Question Warning & Auto-Zero:** Interactive confirmation warning alert when questions are left unrated, automatically filling them with zero (0.0) upon confirmation.
- **Fixed PHP Parse Error:** Resolved character 0x07 syntax error in `evaluator.php` event trigger.
- **Bilingual i18n Strings:** Added Arabic and English translation keys for new attempt indicators.

### v1.0.0 (2026-09-07)
- Initial production release.
- Interactive oral exam evaluation dashboard (`report.php`).
- Programmatic attempt generation and question grading engine (`evaluator.php`).
- Direct competency integration with `qbank_comp_ext_qmap`.
- Bilingual Arabic & English translations.
- Fully integrated packaging and deployment configuration.
