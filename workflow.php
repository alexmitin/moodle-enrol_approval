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
 * Process action on enrol_approval (approve, decline, edit)
 *
 * @package    enrol_approval
 * @copyright  2015 Alex Mitin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once("$CFG->dirroot/enrol/locallib.php"); // Required for the course enrolment manager.
require_once("$CFG->dirroot/enrol/renderer.php"); // Required for the course enrolment users table.

$redirecturl = optional_param('redirecturl', null, PARAM_LOCALURL);
$action = optional_param('action', 'overview', PARAM_ALPHA);

$url = new moodle_url('/enrol/approval/workflow.php', array('action' => $action));
if ($action === 'approve' || $action === 'decline' || $action === 'edit') {
    $ueid = optional_param('ue', null, PARAM_INT); // User enrolment id.
    if ($ueid) {
        $ue = $DB->get_record('user_enrolments', array('id' => $ueid), '*', MUST_EXIST);
        $user = $DB->get_record('user', array('id' => $ue->userid), '*', MUST_EXIST);
        $instance = $DB->get_record('enrol', array('id' => $ue->enrolid), '*', MUST_EXIST);
        $url->param('ue', $ueid);
    } else {
        $instanceid = required_param('enrolid', PARAM_INT); // Instance id.
        $userid = required_param('userid', PARAM_INT); // User id.
        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
        $instance = $DB->get_record('enrol', array('id' => $instanceid), '*', MUST_EXIST);
        $ue = $DB->get_record('user_enrolments', array('userid' => $user->id, 'enrolid' => $instance->id));
        $url->param('userid', $user->id);
        $url->param('enrolid', $instance->id);
    }
} else if ($action === 'overview') {
    $instanceid = required_param('enrolid', PARAM_INT); // Instance id.
    $instance = $DB->get_record('enrol', array('id' => $instanceid), '*', MUST_EXIST);
    $url->param('enrolid', $instanceid);
} else {
    print_error('invalidaction');
}

$course = $DB->get_record('course', array('id' => $instance->courseid), '*', MUST_EXIST);

// The URL of the enrolled users page for the course.
$usersurl = new moodle_url('/enrol/users.php', array('id' => $course->id));

if (($instance->enrol !== 'approval') ||
        !($plugin = enrol_get_plugin($instance->enrol))) {
    redirect($usersurl);
}

$PAGE->set_url($url);
require_login($course, false);
$PAGE->set_pagelayout('admin');

if (!$redirecturl) {
    if (has_capability('moodle/course:enrolreview', $PAGE->context)) {
        $redirecturl = $usersurl;
    } else if (has_capability('enrol/approval:approve', $PAGE->context)) {
        $redirecturl = new moodle_url('/enrol/approval/workflow.php', array('enrolid' => $instance->id));
    } else {
        $redirecturl = new course_url($course->id);
    }
} else {
    $url->param('redirecturl', $redirecturl);
}

if ($action === 'overview') {
    $count = null;
    if (has_capability('moodle/course:enrolreview', $PAGE->context) &&
            ($count = $plugin->count_inactive_users($instance))) {
        // Redirect to the full interface.
        redirect(new moodle_url($usersurl,
                array('ifilter' => $instance->id, 'status' => ENROL_USER_SUSPENDED)));
    }
    // User does not have capability to review all enrolments, show only inactive.
    require_capability('enrol/approval:approve', $PAGE->context);

    $title = get_string('approveusers', 'enrol_approval');
    $PAGE->set_title($title);
    $PAGE->set_heading(format_string($course->fullname, true, array('context' => $PAGE->context)));

    echo $OUTPUT->header();
    echo $OUTPUT->heading($title);
    echo $plugin->overview_inactive_users($instance, $count);
    echo $OUTPUT->footer();
    exit;
}

if ($action === 'approve' || $action === 'decline') {
    require_capability('enrol/approval:approve', $PAGE->context);
    $manager = new course_enrolment_manager($PAGE, $course);
    if ($action === 'approve') {
        $op = new enrol_approval_approveselectedusers_operation($manager, $plugin);
    } else {
        $op = new enrol_approval_declineselectedusers_operation($manager, $plugin);
    }
    if ($ue && $ue->status == ENROL_USER_SUSPENDED) {
        $user->enrolments = array($ue);
        $form = $op->get_form($PAGE->url, array('users' => array($user)));
        if ($form->is_cancelled()) {
            redirect($redirecturl);
        } else if ($form->get_data()) {
            if ($action === 'approve') {
                $plugin->update_user_enrol($instance, $user->id, ENROL_USER_ACTIVE);
            } else {
                $plugin->unenrol_user($instance, $user->id);
            }
            redirect($redirecturl);
        }
    }
    echo $OUTPUT->header();
    echo $OUTPUT->heading($op->get_title());
    if (isset($form)) {
        $form->display();
    } else {
        echo $OUTPUT->notification($ue ? get_string('enrolmentisapproved', 'enrol_approval', fullname($user)) :
                get_string('enrolmentnotfound', 'enrol_approval'));
        echo $OUTPUT->continue_button($redirecturl);
    }
    echo $OUTPUT->footer();
    exit;
}

if ($action === 'edit') {
    if (!$ue) {
        print_error('enrolmentnotfound', 'enrol_approval', $redirecturl);
    }
    require_capability('enrol/approval:manage', $PAGE->context);
    $form = new enrol_approval_edit_enrolment_form($url,
            array('user' => $user, 'course' => $course, 'ue' => $ue));
    if ($form->is_cancelled()) {
        redirect($redirecturl);
    } else if ($data = $form->get_data()) {
        $plugin->update_user_enrol($instance, $user->id, $data->status, $data->timestart, $data->timeend);
        redirect($redirecturl);
    }

    $fullname = fullname($user);
    $title = get_string('editenrolment', 'core_enrol');

    $PAGE->set_title($title);
    $PAGE->set_heading($title);
    $PAGE->navbar->add($title);
    $PAGE->navbar->add($fullname);

    echo $OUTPUT->header();
    echo $OUTPUT->heading($fullname);
    $form->display();
    echo $OUTPUT->footer();
    exit;
}
