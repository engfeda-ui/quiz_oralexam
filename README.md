# 🎤 Moodle Quiz Report Plugin: Oral & Practical Exam Evaluator (`quiz_oralexam`)

[![Moodle Plugin CI](https://github.com/engfeda-ui/quiz_oralexam/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/engfeda-ui/quiz_oralexam/actions/workflows/ci.yml)
[![Moodle Compatibility](https://img.shields.io/badge/Moodle-4.0%20to%205.2%2B-orange.svg?style=flat-square)](https://moodle.org)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%20%7C%208.2%20%7C%208.3-blue.svg?style=flat-square)](https://php.net)
[![Database](https://img.shields.io/badge/Database-PostgreSQL%20%7C%20MySQL%20%7C%20MariaDB-purple.svg?style=flat-square)](https://docs.moodle.org)
[![License](https://img.shields.io/badge/License-GPL%20v3-green.svg?style=flat-square)](http://www.gnu.org/copyleft/gpl.html)
[![Version](https://img.shields.io/badge/Version-v1.2.0-blue.svg?style=flat-square)](https://github.com/engfeda-ui/quiz_oralexam)

A specialized Moodle Quiz Report sub-plugin designed for **in-person Oral and Practical Examinations (OSCE, Oral Defenses, and Technical Workshop assessments)**. It allows examiners and instructors to directly assess and grade students question-by-question live on behalf of the student without requiring student self-submission, while linking each question directly to its competency from `qbank_comp_ext`.

---

## ✨ Features

- **🎯 Examiner Live Scoring Station:** Evaluate candidates in real-time question-by-question during oral exams, lab demonstrations, or OSCE assessments.
- **⚡ One-Click Quick Scores:** Instant scoring buttons for `0% (Zero)`, `50% (Half)`, and `100% (Full)` alongside fine-tuned decimal inputs for maximum grading efficiency.
- **📊 Real-Time Gradebook Synchronization:** Automatically records question attempts, step data, and sum of grades directly into core Moodle gradebook tables ({quiz_attempts}, {quiz_grades}) upon submission.
- **🧭 Dynamic Candidate Roster:**
  - Responsive sidebar list with live candidate search by student name or academic ID number.
  - Cohort and group filtering seamlessly integrated with Moodle course groups.
  - Instant visual status indicators: compact circular pending status (🕒) and evaluated score pill (e.g. `30 pts`).
- **🎯 Competency Tagging & Mapping:** Pulls competency tags directly from questions mapped via `qbank_comp_ext`, providing examiners with clear mastery rubrics.
- **🔄 Multi-Attempt & Retake Tracking:** Full audit trail for sequential oral attempts and retakes with individual feedback notes per question.
- **🛡️ Enterprise-Ready Integrations:**
  - **Security Companion:** Enforces mutual dependency on [`quizaccess_oralexam`](https://github.com/engfeda-ui/quizaccess_oralexam) to prevent students from attempting oral exams independently.
  - **GDPR Privacy Compliance:** Implements Moodle's Privacy Subsystem (`null_provider`) adhering to GDPR regulations.
  - **Localization Support:** Full bilingual English and Arabic (`ar`) language packs included.
  - **CI/CD Ready:** Automated GitHub Actions workflows using `moodle-plugin-ci`.

---

## 📋 Requirements

| Dependency | Required Version / Compatibility |
| :--- | :--- |
| **Moodle Framework** | Moodle 4.0 to 5.2+ (Tested against Moodle 4.5/5.0+ stable branches) |
| **PHP Runtime** | PHP 8.1, PHP 8.2, PHP 8.3 |
| **Database System** | PostgreSQL 13+, MySQL 8.0+, or MariaDB 10.5+ |
| **Required Sub-Plugin** | [`quizaccess_oralexam`](https://github.com/engfeda-ui/quizaccess_oralexam) |

---

## 🚀 Installation

1. **Download & Extract:** Download the repository source.
2. **Directory Placement:** Copy the `oralexam` folder into your Moodle quiz reports directory:
   ```bash
   moodle/mod/quiz/report/oralexam
   ```
3. **Install Companion Access Rule:** Ensure [`quizaccess_oralexam`](https://github.com/engfeda-ui/quizaccess_oralexam) is installed into:
   ```bash
   moodle/mod/quiz/accessrule/oralexam
   ```
4. **Run Moodle Upgrade:** Log in as Administrator and navigate to **Site administration > Notifications** to trigger the database installation.
5. **Alternative Install:** Upload the ZIP via **Site administration > Plugins > Install plugins**.

---

## 🛠️ Usage & Workflow

1. Create a standard **Quiz** and add your oral questions (or pull questions with competency tags from the question bank).
2. Under **Quiz Settings > Extra restrictions on attempts**, set **Oral / Practical Examination Mode** to **Yes**.
3. When the oral examination session starts:
   - The instructor opens the Quiz and selects **Results > Oral Evaluation** from the secondary navigation tab.
   - Select a student from the sidebar candidate list.
   - Grade each question live as the student responds verbally or performs the task.
   - Enter optional examiner notes/feedback for each question.
   - Click **Save & Finalize Assessment** — the grade is immediately committed to the official Moodle Gradebook.

---

## 📋 Changelog

### v1.1.9 (2026-09-08)
- **Enforced Sub-Plugin Dependency (`quizaccess_oralexam`):** Declared `$plugin->dependencies['quizaccess_oralexam'] = 2026090800` in `version.php`. Moodle installer will now automatically require both `quiz_oralexam` and `quizaccess_oralexam` to be installed together, preventing standalone installations and ensuring strict student access control and automatic evaluation locking.

### v1.1.8 (2026-09-08)
- **Enhanced Candidate Card Typography & Compact Status Badges:** Replaced the wide text-heavy `Pending Evaluation` label with a sleek, circular pending clock icon badge (🕒) with native tooltip hover. Expanded the left sidebar width to `365px` to provide ample breathing room for full student names and academic ID pills without text truncations, while retaining the score badge (`30 pts`) for evaluated candidates.

### v1.1.7 (2026-09-08)
- **Fixed Candidate List Collapsing / Shrinking in All Participants View:** Fixed CSS flexbox layout issue where `.oralexam-candidate-item` lacked `flex-shrink: 0` and `min-height: 52px`, causing all 40 student cards in the "All participants" sidebar to squish into 18px empty bars when squeezed inside the flex container. Added explicit scroll boundaries (`max-height: 720px`, `min-height: 0`, `flex: 1 1 auto`) to ensure smooth scrolling and complete visibility of student names, IDs, and evaluation status badges.

### v1.1.6 (2026-09-08)
- **Fixed PHP Type Hint Mismatch (`cm_info` vs `stdClass`):** Removed rigid `\stdClass` type hint on `$cm`, `$quiz`, and `$course` in `evaluator::submit_evaluation()`. In Moodle 4.x/5.x, `$cm` is an instance of `\cm_info`, which caused a PHP `TypeError` during evaluation submission.
- **Upgraded Exception Handling:** Changed `catch (\Exception $e)` to `catch (\Throwable $e)` in `report.php` to safely capture any potential runtime errors or type errors and display user-friendly notifications.

### v1.1.5 (2026-09-08)
- **Fixed Form POST Routing & Redirect Loop:** Added mandatory hidden routing fields (`id`, `mode=oralexam`, `sesskey`, `action=submitevaluation`, and `studentid`) and used unescaped `$PAGE->url->out(false)` to prevent query parameters from being stripped by browser form submissions.

### v1.2.0 (2026-09-09)
- **Critical Bug Fix — Prevent Accidental Oral Exam Conversion:**
  - Removed the dangerous auto-enable block in `report.php` that was silently converting any regular quiz to an oral exam the moment a teacher opened the Oral Evaluation tab.
  - Oral exam mode (`oralexamenabled`) must now be explicitly set by a teacher through the quiz Settings form (`Edit settings`); it is never toggled automatically on page load.
  - This prevents the permanent lock-out of students from regular quizzes that had already been completed.

### v1.1.4 (2026-09-08)
- **Comprehensive Security & Architecture Audit:**
  - Converted batch queries in `evaluator::get_candidates()` to parameterized prepared statements using `$DB->get_in_or_equal()`.
  - Added strict capabilities `quiz/oralexam:view` and `quiz/oralexam:evaluate`.
  - Replaced native `alert()` and `confirm()` with non-blocking modern UI dialogues.

### v1.1.3 (2026-09-08)
- **Multi-Attempt Auto-Increment & Zero-Fill Unrated Questions:**
  - Submitting an evaluation for an already-graded student now automatically increments the attempt number.
  - Automatically assigns `0.0` to unrated questions upon confirmation.

### v1.0.0 (2026-09-07)
- **Initial Release:** Core oral evaluation interface, question card layout, quick scores, and direct Moodle Gradebook synchronization.

---

## 📜 License

Licensed under the [GNU General Public License, Version 3](http://www.gnu.org/copyleft/gpl.html).
