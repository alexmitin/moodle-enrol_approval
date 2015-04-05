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
 * Contains class enrol_approval_bulk_enrolment_change_form
 *
 * @package   enrol_approval
 * @copyright 2015 Alex Mitin
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->dirroot/enrol/bulkchange_forms.php");

/**
 * Base form for the bulk actions in enrol_approval
 *
 * @package   enrol_approval
 * @copyright 2015 Alex Mitin
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class enrol_approval_bulk_enrolment_change_form extends enrol_bulk_enrolment_change_form {
    /**
     * Returns an array of status options
     * @return array
     */
    protected function get_status_options() {
        return array(-1                   => get_string('nochange', 'enrol'),
                     ENROL_USER_ACTIVE    => get_string('participationactive', 'enrol_approval'),
                     ENROL_USER_SUSPENDED => get_string('participationsuspended', 'enrol_approval'));
    }
}
