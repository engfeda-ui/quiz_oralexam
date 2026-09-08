<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Evaluator helper class for quiz_oralexam.
 *
 * Handles candidate fetching (students only), question & competency resolution,
 * programmatic attempt creation, and question-by-question scoring.
 *
 * @package    quiz_oralexam
 * @copyright  2026 Mahmoud Salem
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace quiz_oralexam;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');

class evaluator {

    /**
     * Get the quiz settings object compatible across Moodle versions.
     *
     * @param int $quizid
     * @param int $userid
     * @return object
     */
    public static function get_quiz_object(int $quizid, int $userid = 0) {
        if (class_exists('\\mod_quiz\\quiz_settings')) {
            return \mod_quiz\quiz_settings::create($quizid, $userid);
        }
        return \quiz_settings::create($quizid, $userid);
    }

    /**
     * Fetch enrolled student candidates for this quiz with their current oral evaluation status.
     * Strictly filters to users with student role (excluding teachers, trainers, and managers).
     *
     * @param int $courseid
     * @param \context_module $context
     * @param int $quizid
     * @param int $groupid
     * @return array
     */
    public static function get_candidates(int $courseid, \context_module $context, int $quizid, int $groupid = 0): array {
        global $DB;

        $coursecontext = \context_course::instance($courseid);
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);

        $userfields = 'u.id, u.firstname, u.lastname, u.idnumber, u.department, u.institution, u.email, u.picture, u.imagealt';

        if ($studentrole) {
            $users = get_role_users(
                $studentrole->id,
                $coursecontext,
                false,
                $userfields,
                'u.lastname ASC, u.firstname ASC',
                false,
                $groupid
            );
        } else {
            $allusers = get_enrolled_users(
                $context,
                'mod/quiz:attempt',
                $groupid,
                $userfields,
                'u.lastname ASC, u.firstname ASC'
            );
            $users = [];
            foreach ($allusers as $u) {
                // Exclude teachers/graders.
                if (!has_capability('mod/quiz:grade', $context, $u)) {
                    $users[$u->id] = $u;
                }
            }
        }

        if (empty($users)) {
            return [];
        }

        // Preload all attempts for this quiz to avoid N+1 queries.
        $attempts = $DB->get_records('quiz_attempts', ['quiz' => $quizid], 'attempt ASC');
        $userattempts = [];
        foreach ($attempts as $att) {
            $userattempts[$att->userid][] = $att;
        }

        $candidates = [];
        foreach ($users as $u) {
            $useratts = $userattempts[$u->id] ?? [];
            $lastatt = !empty($useratts) ? end($useratts) : null;

            $status = 'pending';
            $grade = null;
            $attemptid = 0;
            $attemptnumber = 0;
            $timefinish = 0;

            if ($lastatt) {
                $attemptid = (int)$lastatt->id;
                $attemptnumber = (int)$lastatt->attempt;
                $timefinish = (int)$lastatt->timefinish;

                if ($lastatt->state === \mod_quiz\quiz_attempt::FINISHED || $lastatt->state === 'finished') {
                    $status = 'evaluated';
                    $grade = (float)$lastatt->sumgrades;
                } else if ($lastatt->state === \mod_quiz\quiz_attempt::IN_PROGRESS || $lastatt->state === 'inprogress') {
                    $status = 'inprogress';
                }
            }

            $candidates[$u->id] = (object)[
                'user'          => $u,
                'status'        => $status,
                'attemptid'     => $attemptid,
                'attemptnumber' => $attemptnumber,
                'grade'         => $grade,
                'timefinish'    => $timefinish,
                'attemptcount'  => count($useratts),
            ];
        }

        return $candidates;
    }

    /**
     * Fetch questions in the quiz with their linked competencies and current marks.
     *
     * @param int $quizid
     * @param int $courseid
     * @param int $studentid
     * @param int $attemptid
     * @return array
     */
    public static function get_quiz_questions(int $quizid, int $courseid, int $studentid = 0, int $attemptid = 0): array {
        global $DB;

        $quizobj = self::get_quiz_object($quizid, $studentid);
        $structure = $quizobj->get_structure();
        $slots = $structure->get_slots();

        $quba = null;
        if ($attemptid > 0) {
                    // Auto-lock and register this quiz as an Oral Exam in quizaccess_oralexam.
        if ($DB->get_manager()->table_exists('quizaccess_oralexam')) {
            $existingrule = $DB->get_record('quizaccess_oralexam', ['quizid' => $quiz->id]);
            if (!$existingrule) {
                $DB->insert_record('quizaccess_oralexam', (object)[
                    'quizid'          => $quiz->id,
                    'oralexamenabled' => 1,
                ]);
            } else if (empty($existingrule->oralexamenabled)) {
                $DB->set_field('quizaccess_oralexam', 'oralexamenabled', 1, ['quizid' => $quiz->id]);
            }
        }

        $attempt = $DB->get_record('quiz_attempts', ['id' => $attemptid, 'quiz' => $quizid]);
            if ($attempt && $attempt->uniqueid) {
                try {
                    $quba = \question_engine::load_questions_usage_by_activity($attempt->uniqueid);
                } catch (\Exception $e) {
                    $quba = null;
                }
            }
        }

        $questionsdata = [];
        $slotindex = 1;

        foreach ($slots as $slot) {
            $slotno = (int)$slot->slot;
            $maxmark = (float)$slot->maxmark;
            $questionid = (int)$slot->questionid;

            $qrecord = $DB->get_record('question', ['id' => $questionid]);
            if (!$qrecord) {
                continue;
            }

            $comps = self::get_question_competencies($questionid, $courseid);

            $currentmark = null;
            $currentfeedback = '';
            if ($quba) {
                try {
                    $qa = $quba->get_question_attempt($slotno);
                    if ($qa) {
                        $currentmark = $qa->get_mark();
                        $rawcomment = $qa->get_manual_comment();
                        if (is_array($rawcomment) && isset($rawcomment[0])) {
                            $currentfeedback = (string)$rawcomment[0];
                        } else if (is_string($rawcomment)) {
                            $currentfeedback = $rawcomment;
                        } else {
                            $currentfeedback = '';
                        }
                        if (trim($currentfeedback) === 'Array') {
                            $currentfeedback = '';
                        }
                    }
                } catch (\Exception $e) {
                    // Slot not in usage yet.
                }
            }

            $questionsdata[] = (object)[
                'slot'            => $slotno,
                'slotindex'       => $slotindex++,
                'questionid'      => $questionid,
                'name'            => $qrecord->name,
                'questiontext'    => format_text($qrecord->questiontext, $qrecord->questiontextformat),
                'qtype'           => $qrecord->qtype,
                'maxmark'         => $maxmark,
                'competencies'    => $comps,
                'currentmark'     => $currentmark,
                'currentfeedback' => $currentfeedback,
            ];
        }

        return $questionsdata;
    }

    /**
     * Retrieve competencies associated with a question.
     * Integrates with qbank_comp_ext_qmap and falls back to question tags.
     *
     * @param int $questionid
     * @param int $courseid
     * @return array
     */
    public static function get_question_competencies(int $questionid, int $courseid): array {
        global $DB;

        // 1. Direct mapping in qbank_comp_ext_qmap with course filter.
        $sql = "SELECT c.id, c.idnumber, c.shortname, c.description
                  FROM {qbank_comp_ext_qmap} m
                  JOIN {competency} c ON c.id = m.competencyid
                 WHERE m.questionid = :qid AND m.courseid = :cid";
        $records = $DB->get_records_sql($sql, ['qid' => $questionid, 'cid' => $courseid]);
        if (!empty($records)) {
            return array_values($records);
        }

        // 2. Direct mapping without course filter.
        $sql2 = "SELECT c.id, c.idnumber, c.shortname, c.description
                   FROM {qbank_comp_ext_qmap} m
                   JOIN {competency} c ON c.id = m.competencyid
                  WHERE m.questionid = :qid";
        $records2 = $DB->get_records_sql($sql2, ['qid' => $questionid]);
        if (!empty($records2)) {
            return array_values($records2);
        }

        // 3. Fallback to question tags (e.g. comp-101, comp-safety).
        $tagsql = "SELECT t.id, t.rawname
                     FROM {tag_instance} ti
                     JOIN {tag} t ON t.id = ti.tagid
                    WHERE ti.itemtype = 'question' AND ti.itemid = :qid";
        $tags = $DB->get_records_sql($tagsql, ['qid' => $questionid]);
        $compstags = [];
        foreach ($tags as $t) {
            if (stripos($t->rawname, 'comp-') === 0) {
                $compstags[] = (object)[
                    'id'          => $t->id,
                    'idnumber'    => $t->rawname,
                    'shortname'   => strtoupper($t->rawname),
                    'description' => '',
                ];
            }
        }

        return $compstags;
    }

    /**
     * Submit and finalize an oral evaluation on behalf of the student.
     *
     * @param \stdClass $quiz
     * @param \stdClass $cm
     * @param \stdClass $course
     * @param int $studentid
     * @param array $marks [slot => mark]
     * @param array $comments [slot => comment]
     * @param string $generalfeedback
     * @param int $existingattemptid
     * @return \stdClass
     */
    public static function submit_evaluation(
        \stdClass $quiz,
        \stdClass $cm,
        \stdClass $course,
        int $studentid,
        array $marks,
        array $comments,
        string $generalfeedback = '',
        int $existingattemptid = 0
    ): \stdClass {
        global $DB, $USER;

        $quizobj = self::get_quiz_object($quiz->id, $studentid);
        $structure = $quizobj->get_structure();
        $slots = $structure->get_slots();
        $timenow = time();

        if ($existingattemptid > 0) {
            // Update existing attempt.
                    // Auto-lock and register this quiz as an Oral Exam in quizaccess_oralexam.
        if ($DB->get_manager()->table_exists('quizaccess_oralexam')) {
            $existingrule = $DB->get_record('quizaccess_oralexam', ['quizid' => $quiz->id]);
            if (!$existingrule) {
                $DB->insert_record('quizaccess_oralexam', (object)[
                    'quizid'          => $quiz->id,
                    'oralexamenabled' => 1,
                ]);
            } else if (empty($existingrule->oralexamenabled)) {
                $DB->set_field('quizaccess_oralexam', 'oralexamenabled', 1, ['quizid' => $quiz->id]);
            }
        }

        $attempt = $DB->get_record('quiz_attempts', [
                'id'     => $existingattemptid,
                'quiz'   => $quiz->id,
                'userid' => $studentid,
            ], '*', MUST_EXIST);

            $quba = \question_engine::load_questions_usage_by_activity($attempt->uniqueid);
        } else {
            // Create a brand new attempt on behalf of the student.
            $attempts = quiz_get_user_attempts($quiz->id, $studentid, 'all');
            $attemptnumber = count($attempts) + 1;
            $lastattempt = !empty($attempts) ? end($attempts) : false;

            $attempt = quiz_prepare_and_start_new_attempt(
                $quizobj,
                $attemptnumber,
                $lastattempt,
                false,
                [],
                [],
                $studentid
            );

            $quba = \question_engine::load_questions_usage_by_activity($attempt->uniqueid);
        }

        // 1. In Moodle question engine, questions MUST be finished before manual_grade can be applied.
        $quba->finish_all_questions($timenow);

        // 2. Apply examiner marks and feedback for each slot.
        foreach ($slots as $slot) {
            $slotno = (int)$slot->slot;
            $maxmark = (float)$slot->maxmark;

            $mark = (isset($marks[$slotno]) && $marks[$slotno] !== '') ? (float)$marks[$slotno] : 0.0;
            if ($mark < 0) {
                $mark = 0.0;
            }
            if ($mark > $maxmark) {
                $mark = $maxmark;
            }

            $comment = isset($comments[$slotno]) ? clean_text($comments[$slotno]) : '';
            if (trim($comment) === 'Array') {
                $comment = '';
            }

            $quba->manual_grade($slotno, $comment, $mark, FORMAT_HTML);
        }

        // 3. Save question usage state.
        \question_engine::save_questions_usage_by_activity($quba);

        // Finalize attempt record.
        $attempt->state        = \mod_quiz\quiz_attempt::FINISHED;
        $attempt->timefinish   = $timenow;
        $attempt->timemodified = $timenow;
        $attempt->sumgrades    = $quba->get_total_mark();
        $DB->update_record('quiz_attempts', $attempt);

        // Update Moodle Gradebook and save best grade.
        quiz_save_best_grade($quizobj->get_quiz(), $studentid);

        // Trigger attempt_submitted event.
        try {
            $event = \mod_quiz\event\attempt_submitted::create([
                'objectid'      => $attempt->id,
                'relateduserid' => $studentid,
                'courseid'      => $course->id,
                'context'       => $quizobj->get_context(),
                'other'         => [
                    'quizid'      => $quiz->id,
                    'submitterid' => $USER->id,
                ],
            ]);
            $event->trigger();
        } catch (\Exception $e) {
            // Event failure should not break grading.
        }

        return $attempt;
    }
}
