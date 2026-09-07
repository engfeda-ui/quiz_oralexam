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
 * English strings for quiz_oralexam.
 *
 * @package    quiz_oralexam
 * @copyright  2026 Mahmoud Salem
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Oral & Practical Exam Evaluator';
$string['oralexam'] = 'Oral Evaluation';
$string['oralexam:view'] = 'View oral exam evaluations';
$string['oralexam:evaluate'] = 'Conduct and submit oral exam evaluations';
$string['privacy:metadata'] = 'The Oral Exam Evaluator plugin records grades directly in Moodle core quiz and question engine tables and does not store private personal data on its own.';

$string['evaluationsheet'] = 'Oral Assessment Sheet';
$string['selectstudent'] = 'Select a student to evaluate';
$string['nostudentsfound'] = 'No enrolled students found matching the selected group or filter.';
$string['allgroups'] = 'All Groups';
$string['allstatuses'] = 'All Students';
$string['status_evaluated'] = 'Evaluated';
$string['status_pending'] = 'Pending Evaluation';
$string['searchstudent'] = 'Search by name or ID...';

$string['totalstudents'] = 'Total Students';
$string['evaluatedstudents'] = 'Evaluated';
$string['pendingstudents'] = 'Pending';
$string['averagegrade'] = 'Class Average';

$string['evaluating'] = 'Evaluating';
$string['evaluatedby'] = 'Evaluated by {$a->examiner} on {$a->date}';
$string['lastattemptgrade'] = 'Current Grade: {$a->grade} / {$a->maxgrade} ({$a->percent}%)';
$string['newattempt'] = 'Record New Attempt (Retake)';
$string['editingattempt'] = 'Editing Attempt #{$a}';

$string['questionno'] = 'Question #{$a}';
$string['competency'] = 'Competency';
$string['nocompetency'] = 'No competency linked';
$string['maxmark'] = 'Max: {$a} pts';
$string['quickscore'] = 'Quick Score:';
$string['zero'] = '0% (Zero)';
$string['half'] = '50% (Half)';
$string['full'] = '100% (Full)';
$string['custommark'] = 'Mark';
$string['examinernotes'] = 'Examiner feedback / notes:';
$string['generalfeedback'] = 'Overall Oral Exam Remarks';
$string['generalfeedback_placeholder'] = 'Enter any overarching observations, communication skills assessment, or general remarks for this student...';

$string['saveandfinish'] = 'Save & Finalize Assessment';
$string['submitting'] = 'Saving & Recording Grade...';
$string['evaluationsaved'] = 'Oral evaluation for {$a} was recorded and finalized successfully in the gradebook!';
$string['evaluationfailed'] = 'An error occurred while saving the evaluation. Please try again.';
$string['backtolist'] = 'Back to Students List';
$string['confirmfinish'] = 'Are you sure you want to finalize this oral evaluation? The grade will be updated in the official Gradebook immediately.';
$string['computedtotal'] = 'Live Total:';
