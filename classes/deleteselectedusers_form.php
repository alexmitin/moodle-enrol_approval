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
 * Contains class enrol_approval_deleteselectedusers_form
 *
 * @package   enrol_approval
 * @copyright 2015 Alex Mitin
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Confirmation form for the bulk delete
 *
 * @package   enrol_approval
 * @copyright 2015 Alex Mitin
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_approval_deleteselectedusers_form extends enrol_approval_bulk_enrolment_confirm_form {
    /** @var int|null required enrolment status of users in this bulk action */
    protected $requiredstatus = ENROL_USER_ACTIVE;
}