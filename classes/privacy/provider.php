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
 * Privacy Subsystem implementation for quiz_oralexam.
 *
 * @package    quiz_oralexam
 * @copyright  2026 Mahmoud Salem
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace quiz_oralexam\privacy;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider implementation for quiz_oralexam.
 *
 * This plugin is an evaluation and grading interface for mod_quiz. It records all attempts,
 * student grades, question responses, and feedback directly into core Moodle tables
 * ({quiz_attempts}, {quiz_grades}, {question_attempts}, and {question_attempt_steps}).
 * All such personal data is managed, exported, and deleted by mod_quiz and core_question privacy providers.
 * This plugin does not maintain any separate personal user data tables of its own.
 */
class provider implements \core_privacy\local\metadata\null_provider {

    /**
     * Get the reason why this plugin does not store personal data on its own.
     *
     * @return string
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }
}
