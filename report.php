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
 * Quiz report subplugin: Oral Exam Evaluator.
 *
 * @package    quiz_oralexam
 * @copyright  2026 Mahmoud Salem
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/report/default.php');
require_once(__DIR__ . '/classes/evaluator.php');

class quiz_oralexam_report extends quiz_default_report {

    /**
     * Display the oral exam evaluation interface.
     *
     * @param stdClass $quiz
     * @param stdClass $cm
     * @param stdClass $course
     * @return bool
     */
    public function display($quiz, $cm, $course) {
        global $CFG, $DB, $PAGE, $OUTPUT, $USER;

        $context = context_module::instance($cm->id);
        require_capability('quiz/oralexam:view', $context);
        $canevaluate = has_capability('quiz/oralexam:evaluate', $context);

        // Group handling: respect Moodle's active group.
        $currentgroup = groups_get_activity_group($cm, true);
        if ($currentgroup === false) {
            $currentgroup = optional_param('group', 0, PARAM_INT);
        }
        $selectedgroup = $currentgroup;

        $selectedstudent = optional_param('student', 0, PARAM_INT);
        $action = optional_param('action', '', PARAM_ALPHA);
        $attemptid = optional_param('attemptid', 0, PARAM_INT);
        $isnewattempt = optional_param('newattempt', 0, PARAM_INT);

        $baseurl = new moodle_url('/mod/quiz/report.php', [
            'id'   => $cm->id,
            'mode' => 'oralexam',
        ]);
        if ($selectedgroup > 0) {
            $baseurl->param('group', $selectedgroup);
        }

        $PAGE->set_url($baseurl);
        $PAGE->set_pagelayout('incourse');
        $PAGE->requires->css('/mod/quiz/report/oralexam/styles.css');

        // Auto-ensure quizaccess_oralexam is enabled for this quiz when opened in oral evaluation mode.
        if ($canevaluate && $DB->get_manager()->table_exists('quizaccess_oralexam')) {
            $rule = $DB->get_record('quizaccess_oralexam', ['quizid' => $quiz->id]);
            if (!$rule) {
                $DB->insert_record('quizaccess_oralexam', (object)[
                    'quizid'          => $quiz->id,
                    'oralexamenabled' => 1,
                ]);
            } else if (empty($rule->oralexamenabled)) {
                $DB->set_field('quizaccess_oralexam', 'oralexamenabled', 1, ['quizid' => $quiz->id]);
            }
        }


        // Handle POST submission: Save evaluation.
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && confirm_sesskey() && $canevaluate && $action === 'submit_eval') {
            $poststudentid = required_param('student', PARAM_INT);
            $postmarks = optional_param_array('marks', [], PARAM_FLOAT);
            $postfeedback = optional_param_array('feedback', [], PARAM_RAW);
            $generalnotes = optional_param('generalfeedback', '', PARAM_RAW);
            $targetattemptid = optional_param('attemptid', 0, PARAM_INT);

            if ($isnewattempt) {
                $targetattemptid = 0; // Force brand new attempt.
            }

            try {
                \quiz_oralexam\evaluator::submit_evaluation(
                    $quiz,
                    $cm,
                    $course,
                    $poststudentid,
                    $postmarks,
                    $postfeedback,
                    $generalnotes,
                    $targetattemptid
                );

                $studentrec = $DB->get_record('user', ['id' => $poststudentid], 'firstname, lastname');
                $studentname = fullname($studentrec);

                \core\notification::success(get_string('evaluationsaved', 'quiz_oralexam', $studentname));

                // Redirect back keeping candidate selected.
                $redirecturl = clone $baseurl;
                $redirecturl->param('student', $poststudentid);
                redirect($redirecturl);
            } catch (\Exception $e) {
                \core\notification::error(get_string('evaluationfailed', 'quiz_oralexam') . ' ' . $e->getMessage());
            }
        }

        // Print header.
        $this->print_header_and_tabs($cm, $course, $quiz, 'oralexam');

        // Fetch students only.
        $candidates = \quiz_oralexam\evaluator::get_candidates($course->id, $context, $quiz->id, $selectedgroup);

        // Stats calculation.
        $totalcandidates = count($candidates);
        $evaluatedcount = 0;
        $pendingcount = 0;
        $totalscore = 0.0;

        foreach ($candidates as $cand) {
            if ($cand->status === 'evaluated') {
                $evaluatedcount++;
                $totalscore += (float)$cand->grade;
            } else {
                $pendingcount++;
            }
        }
        $avgscore = $evaluatedcount > 0 ? round($totalscore / $evaluatedcount, 1) : 0;
        $quizsumgrades = (float)$quiz->sumgrades;

        echo html_writer::start_div('oralexam-container');

        // 1. Stats Bar.
        echo html_writer::start_div('oralexam-stats-grid');
        $this->render_stat_card(get_string('totalstudents', 'quiz_oralexam'), $totalcandidates, 'stat-total', 'fa-users');
        $this->render_stat_card(get_string('evaluatedstudents', 'quiz_oralexam'), $evaluatedcount, 'stat-evaluated', 'fa-check-circle');
        $this->render_stat_card(get_string('pendingstudents', 'quiz_oralexam'), $pendingcount, 'stat-pending', 'fa-clock-o');
        $this->render_stat_card(get_string('averagegrade', 'quiz_oralexam'), "$avgscore / $quizsumgrades", 'stat-avg', 'fa-graduation-cap');
        echo html_writer::end_div(); // End stats grid.

        // 2. Filter Bar (Groups & Search & Results link).
        echo html_writer::start_div('oralexam-controls-card');
        echo html_writer::start_div('controls-group-select');
        groups_print_activity_menu($cm, $baseurl);
        echo html_writer::end_div();

        echo html_writer::start_div('controls-actions');
        echo html_writer::link(
            new moodle_url('/mod/quiz/report.php', ['id' => $cm->id, 'mode' => 'overview']),
            '<i class="fa fa-list-alt mr-1"></i> ' . get_string('viewquizresults', 'quiz_oralexam'),
            ['class' => 'btn btn-outline-secondary btn-sm font-weight-bold', 'target' => '_blank']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();

        // 3. Two-Column Layout: Left (Student Selector List), Right (Evaluation Sheet).
        echo html_writer::start_div('oralexam-main-grid');

        // Column Left: Student Selector.
        echo html_writer::start_div('oralexam-sidebar');
        echo html_writer::tag('h3', get_string('selectstudent', 'quiz_oralexam'), ['class' => 'oralexam-section-title']);

        if (empty($candidates)) {
            echo html_writer::div(get_string('nostudentsfound', 'quiz_oralexam'), 'alert alert-info');
        } else {
            // Live search input for students.
            echo '<div class="oralexam-search-box">';
            echo '<input type="text" id="candidateSearch" placeholder="' . get_string('searchstudent', 'quiz_oralexam') . '" class="form-control" onkeyup="filterCandidates()">';
            echo '</div>';

                        echo html_writer::start_tag('ul', ['class' => 'oralexam-candidate-list', 'id' => 'candidateList']);
            foreach ($candidates as $uid => $cand) {
                $u = $cand->user;
                $activeclass = ($selectedstudent == $uid) ? ' active' : '';
                $statusclass = $cand->status === 'evaluated' ? 'status-done' : 'status-wait';
                $statuslabel = $cand->status === 'evaluated' ? get_string('status_evaluated', 'quiz_oralexam') : get_string('status_pending', 'quiz_oralexam');
                $scorebadge = ($cand->grade !== null) ? round($cand->grade, 1) . ' pts' : '—';

                $candurl = clone $baseurl;
                $candurl->param('student', $uid);

                echo html_writer::start_tag('li', [
                    'class' => 'oralexam-candidate-item' . $activeclass,
                    'data-name' => strtolower(fullname($u) . ' ' . $u->idnumber),
                    'onclick' => "window.location.href=" . json_encode($candurl->out(false)),
                ]);
                echo html_writer::start_tag('a', [
                    'href' => $candurl->out(false),
                    'class' => 'candidate-link',
                ]);

                echo html_writer::start_div('cand-avatar-wrap');
                echo $OUTPUT->user_picture($u, ['size' => 36, 'link' => false]);
                echo html_writer::end_div();

                echo html_writer::start_div('cand-main-info');
                echo html_writer::tag('span', fullname($u), ['class' => 'cand-name']);
                if (!empty($u->idnumber)) {
                    echo html_writer::tag('span', $u->idnumber, ['class' => 'cand-idnumber-pill']);
                }
                echo html_writer::end_div();

                echo html_writer::start_div('cand-meta-box');
                echo html_writer::start_div('cand-status-pill ' . $statusclass);
                if ($cand->status === 'evaluated') {
                    echo '<i class="fa fa-check-circle mr-1"></i>';
                    echo html_writer::tag('span', $scorebadge, ['class' => 'cand-score-val']);
                } else {
                    echo '<i class="fa fa-clock-o mr-1"></i>';
                    echo html_writer::tag('span', $statuslabel, ['class' => 'cand-pending-val']);
                }
                echo html_writer::end_div();
                echo '<i class="fa fa-angle-left cand-chevron rtl-flip"></i>';
                echo html_writer::end_div();

                echo html_writer::end_tag('a');
                echo html_writer::end_tag('li');
            }
            echo html_writer::end_tag('ul');
        }
        echo html_writer::end_div(); // End Left Sidebar.

        // Column Right: Active Evaluation Sheet.
        echo html_writer::start_div('oralexam-content');

        // Check if selected student exists in candidates, or load user directly if selected.
        $activecand = null;
        if ($selectedstudent > 0) {
            if (isset($candidates[$selectedstudent])) {
                $activecand = $candidates[$selectedstudent];
            } else {
                // If student was selected from another group filter, load user details.
                $selecteduser = $DB->get_record('user', ['id' => $selectedstudent]);
                if ($selecteduser) {
                    $candatts = $DB->get_records('quiz_attempts', ['quiz' => $quiz->id, 'userid' => $selectedstudent], 'attempt ASC');
                    $lastatt = !empty($candatts) ? end($candatts) : null;
                    $status = ($lastatt && ($lastatt->state === 'finished' || $lastatt->state === \mod_quiz\quiz_attempt::FINISHED)) ? 'evaluated' : 'pending';
                    $activecand = (object)[
                        'user'          => $selecteduser,
                        'status'        => $status,
                        'attemptid'     => $lastatt ? (int)$lastatt->id : 0,
                        'attemptnumber' => $lastatt ? (int)$lastatt->attempt : 0,
                        'grade'         => $lastatt ? (float)$lastatt->sumgrades : null,
                        'timefinish'    => $lastatt ? (int)$lastatt->timefinish : 0,
                        'attemptcount'  => count($candatts),
                    ];
                }
            }
        }

        if ($activecand) {
            $this->render_evaluation_sheet($quiz, $cm, $course, $activecand, $baseurl, $canevaluate, $isnewattempt);
        } else {
            echo html_writer::start_div('oralexam-empty-state');
            echo html_writer::tag('i', '', ['class' => 'fa fa-user-circle-o fa-5x text-muted']);
            echo html_writer::tag('h3', get_string('selectstudent', 'quiz_oralexam'));
            echo html_writer::tag('p', 'اضغط على أي طالب من القائمة الجانبية لبدء استمارة التقييم الشفهي ورصد الدرجات.', ['class' => 'text-muted']);
            echo html_writer::end_div();
        }

        echo html_writer::end_div(); // End Right Content.
        echo html_writer::end_div(); // End Main Grid.
        echo html_writer::end_div(); // End Container.

        // Inline JS for candidate filtering and live score calculation.
        $this->render_inline_scripts();

        return true;
    }

    /**
     * Render evaluation questions sheet for a student.
     */
    protected function render_evaluation_sheet($quiz, $cm, $course, $candidate, $baseurl, $canevaluate, $isnewattempt = 0) {
        global $OUTPUT, $USER;

        $u = $candidate->user;
        $targetattemptid = $isnewattempt ? 0 : $candidate->attemptid;

        $questions = \quiz_oralexam\evaluator::get_quiz_questions(
            $quiz->id,
            $course->id,
            $u->id,
            $targetattemptid
        );

        echo html_writer::start_div('oralexam-sheet-card');

        // Header: Student Info Bar.
        echo html_writer::start_div('student-header-bar');
        echo html_writer::start_div('student-header-main');
        echo $OUTPUT->user_picture($u, ['size' => 60]);
        echo html_writer::start_div('student-details');
        echo html_writer::tag('h2', fullname($u), ['class' => 'student-fullname']);
        echo html_writer::tag('span', 'Academic ID: ' . ($u->idnumber ?: '—'), ['class' => 'badge badge-secondary mr-2']);
        if (!empty($u->department)) {
            echo html_writer::tag('span', $u->department, ['class' => 'badge badge-info mr-2']);
        }
        echo html_writer::end_div();
        echo html_writer::end_div();

        // Right side of header: Actions (Retake button if already evaluated).
        echo html_writer::start_div('student-header-actions');
        if ($candidate->status === 'evaluated' && $canevaluate) {
            $retakeurl = clone $baseurl;
            $retakeurl->params(['student' => $u->id, 'newattempt' => 1]);
            echo html_writer::link($retakeurl, get_string('newattempt', 'quiz_oralexam'), [
                'class' => 'btn btn-outline-primary btn-sm',
            ]);
        }
        if ($isnewattempt) {
            echo html_writer::tag('span', 'Creating New Attempt (Retake)', ['class' => 'badge badge-warning p-2']);
        }
        echo html_writer::end_div();
        echo html_writer::end_div(); // End Header Bar.

        // Form start.
        $actionurl = new moodle_url('/mod/quiz/report.php', [
            'id'     => $cm->id,
            'mode'   => 'oralexam',
            'action' => 'submit_eval',
        ]);

        echo html_writer::start_tag('form', [
            'method' => 'POST',
            'action' => $actionurl->out(),
            'id'     => 'oralExamForm',
            'onsubmit' => 'return confirmSubmit()',
        ]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'student', 'value' => $u->id]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'group', 'value' => optional_param('group', 0, PARAM_INT)]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'attemptid', 'value' => $targetattemptid]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'newattempt', 'value' => $isnewattempt]);

        // Questions List.
        echo html_writer::start_div('oralexam-questions-deck');

        foreach ($questions as $q) {
            $slot = $q->slot;
            $maxmark = $q->maxmark;
            $currentmark = ($q->currentmark !== null) ? round($q->currentmark, 2) : '';

            echo html_writer::start_div('oralexam-qcard', ['id' => 'qcard-' . $slot]);

            // Question Header.
            echo html_writer::start_div('qcard-header');
            echo html_writer::tag('span', get_string('questionno', 'quiz_oralexam', $q->slotindex), ['class' => 'qcard-slot-badge']);
            echo html_writer::tag('span', get_string('maxmark', 'quiz_oralexam', $maxmark), ['class' => 'qcard-maxmark-badge']);
            echo html_writer::end_div();

            // Question Text.
            echo html_writer::start_div('qcard-body');
            echo html_writer::div($q->questiontext, 'qcard-questiontext');

            // Competency Badges.
            echo html_writer::start_div('qcard-competencies');
            if (!empty($q->competencies)) {
                foreach ($q->competencies as $comp) {
                    $comptitle = s($comp->shortname ?: $comp->idnumber);
                    echo html_writer::start_span('comp-pill', ['title' => s($comp->description)]);
                    echo html_writer::tag('i', '', ['class' => 'fa fa-tags mr-1']);
                    echo html_writer::tag('span', $comp->idnumber . ' - ' . $comptitle, ['class' => 'comp-pill-text']);
                    echo html_writer::end_span();
                }
            } else {
                echo html_writer::tag('span', get_string('nocompetency', 'quiz_oralexam'), ['class' => 'comp-pill text-muted']);
            }
            echo html_writer::end_div(); // End Competencies.

            // Scoring Bar.
            echo html_writer::start_div('qcard-scoring-bar');
            echo html_writer::tag('span', get_string('quickscore', 'quiz_oralexam'), ['class' => 'scoring-label font-weight-bold mr-2']);

            // Quick Click Buttons (0%, 50%, 100%).
            $halfmark = round($maxmark / 2, 2);
            echo html_writer::start_div('quick-btn-group');
            echo html_writer::tag('button', get_string('zero', 'quiz_oralexam'), [
                'type' => 'button',
                'class' => 'btn btn-outline-danger btn-sm quick-btn',
                'onclick' => "setMark($slot, 0)",
            ]);
            echo html_writer::tag('button', get_string('half', 'quiz_oralexam') . " ($halfmark)", [
                'type' => 'button',
                'class' => 'btn btn-outline-warning btn-sm quick-btn',
                'onclick' => "setMark($slot, $halfmark)",
            ]);
            echo html_writer::tag('button', get_string('full', 'quiz_oralexam') . " ($maxmark)", [
                'type' => 'button',
                'class' => 'btn btn-outline-success btn-sm quick-btn',
                'onclick' => "setMark($slot, $maxmark)",
            ]);
            echo html_writer::end_div();

            // Numeric Input.
            echo html_writer::start_div('mark-input-wrapper');
            echo html_writer::empty_tag('input', [
                'type' => 'number',
                'step' => '0.1',
                'min' => '0',
                'max' => $maxmark,
                'name' => "marks[$slot]",
                'id' => "mark_$slot",
                'value' => $currentmark,
                'class' => 'form-control mark-input text-center font-weight-bold',
                'placeholder' => '0.0',
                'oninput' => 'recalcTotal()',
                // Optional mark input
                'required' => false,
            ]);
            echo html_writer::tag('span', "/ $maxmark", ['class' => 'mark-denom']);
            echo html_writer::end_div();

            echo html_writer::end_div(); // End Scoring Bar.

            // Examiner notes on this question.
            echo html_writer::start_div('qcard-comment-wrapper mt-2');
            $feedbackval = ($q->currentfeedback !== 'Array' && $q->currentfeedback !== null) ? $q->currentfeedback : '';
            echo html_writer::tag('input', '', [
                'type' => 'text',
                'name' => "feedback[$slot]",
                'value' => $feedbackval,
                'placeholder' => get_string('examinernotes', 'quiz_oralexam'),
                'class' => 'form-control form-control-sm text-muted',
            ]);
            echo html_writer::end_div();

            echo html_writer::end_div(); // End Question Body.
            echo html_writer::end_div(); // End QCard.
        }

        echo html_writer::end_div(); // End Questions Deck.

        // Sticky Bottom Footer: Live Total & Submit Button.
        echo html_writer::start_div('oralexam-sticky-footer');
        echo html_writer::start_div('sticky-footer-content');

        echo html_writer::start_div('live-total-box');
        echo html_writer::tag('span', get_string('computedtotal', 'quiz_oralexam'), ['class' => 'total-label']);
        echo html_writer::tag('span', '0.0', ['id' => 'liveTotalScore', 'class' => 'total-score-value']);
        echo html_writer::tag('span', "/ {$quiz->sumgrades}", ['class' => 'total-denom']);
        echo html_writer::end_div();

        if ($canevaluate) {
            echo html_writer::tag('button', get_string('saveandfinish', 'quiz_oralexam'), [
                'type'  => 'submit',
                'class' => 'btn btn-primary btn-lg shadow-sm',
                'id'    => 'submitOralExamBtn',
            ]);
        }

        echo html_writer::end_div();
        echo html_writer::end_div(); // End Sticky Footer.

        echo html_writer::end_tag('form');
        echo html_writer::end_div(); // End Sheet Card.
    }

    /**
     * Render single KPI stat card.
     */
    protected function render_stat_card($title, $value, $class, $icon) {
        echo html_writer::start_div('oralexam-stat-card ' . $class);
        echo html_writer::tag('i', '', ['class' => "fa $icon stat-icon"]);
        echo html_writer::start_div('stat-card-text');
        echo html_writer::tag('span', $title, ['class' => 'stat-title']);
        echo html_writer::tag('span', $value, ['class' => 'stat-value']);
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    /**
     * Inline JavaScript for instant evaluation interactions.
     */
    protected function render_inline_scripts() {
        ?>
        <script>
        function filterCandidates() {
            var input = document.getElementById('candidateSearch');
            var filter = input.value.toLowerCase();
            var ul = document.getElementById('candidateList');
            if (!ul) return;
            var li = ul.getElementsByTagName('li');
            for (var i = 0; i < li.length; i++) {
                var name = li[i].getAttribute('data-name');
                if (name && name.indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        }

        function setMark(slot, val) {
            var input = document.getElementById('mark_' + slot);
            if (input) {
                input.value = val;
                recalcTotal();
            }
        }

        function recalcTotal() {
            var inputs = document.querySelectorAll('.mark-input');
            var total = 0.0;
            inputs.forEach(function(inp) {
                var v = parseFloat(inp.value);
                if (!isNaN(v)) {
                    total += v;
                }
            });
            var display = document.getElementById('liveTotalScore');
            if (display) {
                display.innerText = total.toFixed(1);
            }
        }

        function confirmSubmit() {
            var confirmMsg = <?php echo json_encode(get_string('confirmfinish', 'quiz_oralexam')); ?>;
            var btn = document.getElementById('submitOralExamBtn');
            if (confirm(confirmMsg)) {
                if (btn) {
                    btn.innerText = <?php echo json_encode(get_string('submitting', 'quiz_oralexam')); ?>;
                    setTimeout(function() {
                        btn.disabled = true;
                    }, 50);
                }
                return true;
            }
            return false;
        }

        // Initialize live total on load.
        document.addEventListener('DOMContentLoaded', function() {
            recalcTotal();
        });
        </script>
        <?php
    }
}
