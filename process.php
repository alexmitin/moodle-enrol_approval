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
 * Process action on enrol_approval (approve, decline, etc.)
 *
 * @package    enrol_approval
 * @copyright  2015 Alex Mitin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once("$CFG->dirroot/enrol/locallib.php"); // Required for the course enrolment manager.
require_once("$CFG->dirroot/enrol/locallib.php"); // Required for the course enrolment manager.
require_once("$CFG->dirroot/enrol/renderer.php"); // Required for the course enrolment users table.

$ueid = required_param('ue', PARAM_INT);
$redirecturl = optional_param('redirecturl', null, PARAM_LOCALURL);
$action = optional_param('action', null, PARAM_ALPHA);

$ue = $DB->get_record('user_enrolments', array('id' => $ueid), '*', MUST_EXIST);
$user = $DB->get_record('user', array('id' => $ue->userid), '*', MUST_EXIST);
$instance = $DB->get_record('enrol', array('id' => $ue->enrolid), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $instance->courseid), '*', MUST_EXIST);

// The URL of the enrolled users page for the course.
$usersurl = new moodle_url('/enrol/users.php', array('id' => $course->id));

if (($instance->enrol !== 'approval') ||
        !($plugin = enrol_get_plugin($instance->enrol)) ||
        !$plugin->allow_manage($instance)) {
   redirect($usersurl);
}

require_login($course);
require_sesskey();

// The user must be able to manage enrolments within the course.
require_capability('enrol/approval:manage', $PAGE->context);

if ($action === 'approve' && $ue->status == ENROL_USER_SUSPENDED) {
    $plugin->update_user_enrol($instance, $user->id, ENROL_USER_ACTIVE);
}

if ($action === 'decline' && $ue->status == ENROL_USER_SUSPENDED) {
    $plugin->unenrol_user($instance, $user->id);
}

redirect($redirecturl ? $redirecturl : $usersurl, 'Redirecting', 10);
