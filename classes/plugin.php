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
 * Contains class enrol_approval_plugin
 *
 * @package    enrol_approval
 * @copyright  2015 Alex Mitin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Self enrolment with approval plugin implementation.
 *
 * @package    enrol_approval
 * @copyright  2015 Alex Mitin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_approval_plugin extends enrol_plugin {

    /** @var stdClass user record */
    protected $lasternoller = null;
    /** @var int */
    protected $lasternollerinstanceid = 0;

    /**
     * Returns optional enrolment information icons.
     *
     * This is used in course list for quick overview of enrolment options.
     *
     * We are not using single instance parameter because sometimes
     * we might want to prevent icon repetition when multiple instances
     * of one type exist. One instance may also produce several icons.
     *
     * @param array $instances all enrol instances of this type in one course
     * @return array of pix_icon
     */
    public function get_info_icons(array $instances) {
        $canenrol = false;
        foreach ($instances as $instance) {
            if ($this->can_self_enrol($instance, false) !== true) {
                // User can not enrol himself.
                // Note that we do not check here if user is already enrolled for performance reasons -
                // such check would execute extra queries for each course in the list of courses and
                // would hide self-enrolment icons from guests.
                continue;
            }
            $canenrol = true;
        }
        $icons = array();
        if ($canenrol) {
            $icons[] = new pix_icon('approval', get_string('pluginname', 'enrol_approval'), 'enrol_approval');
        }
        return $icons;
    }

    /**
     * Returns localised name of enrol instance
     *
     * @param stdClass $instance (null is accepted too)
     * @return string
     */
    public function get_instance_name($instance) {
        global $DB;

        if (empty($instance->name)) {
            if (!empty($instance->roleid) and
                    $role = $DB->get_record('role', array('id' => $instance->roleid))) {
                $role = ' (' . role_get_name($role, context_course::instance($instance->courseid, IGNORE_MISSING)) . ')';
            } else {
                $role = '';
            }
            $enrol = $this->get_name();
            return get_string('pluginname', 'enrol_'.$enrol) . $role;
        } else {
            return format_string($instance->name);
        }
    }

    /**
     * roles_protected
     * @return bool
     */
    public function roles_protected() {
        // Users may tweak the roles later.
        return false;
    }

    /**
     * allow_unenrol
     *
     * @param stdClass $instance
     * @return bool
     */
    public function allow_unenrol(stdClass $instance) {
        // Users with unenrol cap may unenrol other users manually.
        return true;
    }

    /**
     * allow_manage
     *
     * @param stdClass $instance
     * @return bool
     */
    public function allow_manage(stdClass $instance) {
        // Disable the default edit instance functionality.
        return false;
    }

    /**
     * show_enrolme_link
     *
     * @param stdClass $instance
     * @return bool
     */
    public function show_enrolme_link(stdClass $instance) {

        if (true !== $this->can_self_enrol($instance, false)) {
            return false;
        }

        return true;
    }

    /**
     * Sets up navigation entries.
     *
     * @param stdClass $instancesnode
     * @param stdClass $instance
     * @return void
     */
    public function add_course_navigation($instancesnode, stdClass $instance) {
        if ($instance->enrol !== 'approval') {
             throw new coding_exception('Invalid enrol instance type!');
        }

        $context = context_course::instance($instance->courseid);
        if (has_capability('enrol/approval:config', $context)) {
            $managelink = new moodle_url('/enrol/approval/edit.php',
                    array('courseid' => $instance->courseid, 'id' => $instance->id));
            $instancesnode->add($this->get_instance_name($instance),
                    $managelink, navigation_node::TYPE_SETTING);
        }
        if (!has_capability('moodle/course:enrolreview', $context) && has_capability('enrol/approval:approve', $context)) {
            // Special link for users who have capability to approve enrolments
            // but do not have capability to review all enrolments.
            $approvelink = new moodle_url('/enrol/approval/workflow.php',
                    array('enrolid' => $instance->id, 'action' => 'overview'));
            $instancesnode->parent->add(get_string('approveusers', 'enrol_approval'),
                    $approvelink, navigation_node::TYPE_SETTING);
        }
    }

    /**
     * Returns edit icons for the page with list of instances
     *
     * @param stdClass $instance
     * @return array
     */
    public function get_action_icons(stdClass $instance) {
        global $OUTPUT;

        if ($instance->enrol !== 'approval') {
            throw new coding_exception('invalid enrol instance!');
        }
        $context = context_course::instance($instance->courseid);

        $icons = array();

        if (has_capability('enrol/approval:approve', $context)) {
            $managelink = new moodle_url("/enrol/approval/workflow.php", array('enrolid' => $instance->id));
            $icons[] = $OUTPUT->action_icon($managelink, new pix_icon('t/enrolusers',
                    get_string('approveusers', 'enrol_approval'), 'core', array('class' => 'iconsmall')));
        }
        if (has_capability('enrol/approval:config', $context)) {
            $editlink = new moodle_url("/enrol/approval/edit.php",
                    array('courseid' => $instance->courseid, 'id' => $instance->id));
            $icons[] = $OUTPUT->action_icon($editlink, new pix_icon('t/edit', get_string('edit'), 'core',
                array('class' => 'iconsmall')));
        }

        return $icons;
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course
     *
     * @param int $courseid
     * @return moodle_url page url
     */
    public function get_newinstance_link($courseid) {
        $context = context_course::instance($courseid, MUST_EXIST);

        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/approval:config', $context)) {
            return null;
        }
        // Multiple instances supported - different roles/cohorts.
        return new moodle_url('/enrol/approval/edit.php', array('courseid' => $courseid));
    }

    /**
     * Self enrol user to course
     *
     * @param stdClass $instance enrolment instance
     * @param stdClass $data data needed for enrolment.
     * @return bool|array true if enroled else eddor code and messege
     */
    public function enrol_approval(stdClass $instance, $data = null) {
        global $DB, $USER, $CFG;

        $timestart = time();
        if ($instance->enrolperiod) {
            $timeend = $timestart + $instance->enrolperiod;
        } else {
            $timeend = 0;
        }

        $this->enrol_user($instance, $USER->id, $instance->roleid, $timestart, $timeend, ENROL_USER_SUSPENDED);

        // Send welcome message.
        $this->send_application_message($instance);
    }

    /**
     * Creates course enrol form, checks if form submitted and enrols user if necessary. It can also redirect.
     *
     * @param stdClass $instance
     * @return string html text, usually a form in a text box
     */
    public function enrol_page_hook(stdClass $instance) {
        global $CFG, $OUTPUT, $USER;

        require_once("$CFG->dirroot/enrol/approval/locallib.php");

        $enrolstatus = $this->can_self_enrol($instance);

        // Don't show enrolment instance form, if user can't enrol using it.
        if (true === $enrolstatus) {
            $form = new enrol_approval_enrol_form(null, $instance);
            $instanceid = optional_param('instance', 0, PARAM_INT);
            if ($instance->id == $instanceid) {
                if ($data = $form->get_data()) {
                    $this->enrol_approval($instance, $data);
                }
            }

            ob_start();
            $form->display();
            $output = ob_get_clean();
            return $OUTPUT->box($output);
        } else {
            return $OUTPUT->box($enrolstatus);
        }
    }

    /**
     * Checks if user can self enrol.
     *
     * @param stdClass $instance enrolment instance
     * @param bool $checkuserenrolment if true will check if user enrolment is inactive.
     *             used by navigation to improve performance.
     * @return bool|string true if successful, else error message or false.
     */
    public function can_self_enrol(stdClass $instance, $checkuserenrolment = true) {
        global $DB, $USER, $CFG;

        if ($checkuserenrolment) {
            if (isguestuser()) {
                // Can not enrol guest.
                return get_string('noguestaccess', 'enrol');
            }
            // Check if user is already enroled.
            if ($DB->get_record('user_enrolments', array('userid' => $USER->id, 'enrolid' => $instance->id))) {
                // TODO change message to "enrolment is already requested".
                return get_string('canntenrol', 'enrol_approval');
            }
        }

        if ($instance->status != ENROL_INSTANCE_ENABLED) {
            return get_string('canntenrol', 'enrol_approval');
        }

        if ($instance->enrolstartdate != 0 and $instance->enrolstartdate > time()) {
            return get_string('canntenrol', 'enrol_approval');
        }

        if ($instance->enrolenddate != 0 and $instance->enrolenddate < time()) {
            return get_string('canntenrol', 'enrol_approval');
        }

        if (!$instance->customint6) {
            // New enrols not allowed.
            return get_string('canntenrol', 'enrol_approval');
        }

        if ($DB->record_exists('user_enrolments', array('userid' => $USER->id, 'enrolid' => $instance->id))) {
            return get_string('canntenrol', 'enrol_approval');
        }

        if ($instance->customint3 > 0) {
            // Max enrol limit specified.
            $count = $DB->count_records('user_enrolments', array('enrolid' => $instance->id, 'status' => ENROL_USER_ACTIVE));
            if ($count >= $instance->customint3) {
                // Bad luck, no more self enrolments here.
                return get_string('maxenrolledreached', 'enrol_approval');
            }
        }

        if ($instance->customint5) {
            require_once("$CFG->dirroot/cohort/lib.php");
            if (!cohort_is_member($instance->customint5, $USER->id)) {
                $cohort = $DB->get_record('cohort', array('id' => $instance->customint5));
                if (!$cohort) {
                    return null;
                }
                $a = format_string($cohort->name, true, array('context' => context::instance_by_id($cohort->contextid)));
                return markdown_to_html(get_string('cohortnonmemberinfo', 'enrol_approval', $a));
            }
        }

        return true;
    }

    /**
     * Return information for enrolment instance containing list of parameters required
     * for enrolment, name of enrolment plugin etc.
     *
     * @param stdClass $instance enrolment instance
     * @return stdClass instance info.
     */
    public function get_enrol_info(stdClass $instance) {

        $instanceinfo = new stdClass();
        $instanceinfo->id = $instance->id;
        $instanceinfo->courseid = $instance->courseid;
        $instanceinfo->type = $this->get_name();
        $instanceinfo->name = $this->get_instance_name($instance);
        $instanceinfo->status = $this->can_self_enrol($instance);
        return $instanceinfo;
    }

    /**
     * Add new instance of enrol plugin with default settings
     *
     * @param stdClass $course
     * @return int id of new instance
     */
    public function add_default_instance($course) {
        $fields = $this->get_instance_defaults();

        return $this->add_instance($course, $fields);
    }

    /**
     * Returns defaults for new instances
     *
     * @return array
     */
    public function get_instance_defaults() {
        $expirynotify = $this->get_config('expirynotify');
        if ($expirynotify == 2) {
            $expirynotify = 1;
            $notifyall = 1;
        } else {
            $notifyall = 0;
        }

        $fields = array();
        $fields['status']          = $this->get_config('status');
        $fields['roleid']          = $this->get_config('roleid');
        $fields['enrolperiod']     = $this->get_config('enrolperiod');
        $fields['expirynotify']    = $expirynotify;
        $fields['notifyall']       = $notifyall;
        $fields['expirythreshold'] = $this->get_config('expirythreshold');
        $fields['customint2']      = $this->get_config('longtimenosee');
        $fields['customint3']      = $this->get_config('maxenrolled');
        $fields['customint4']      = $this->get_config('sendmessageonapplication');
        $fields['customint5']      = 0;
        $fields['customint6']      = 1;

        return $fields;
    }

    /**
     * Send emails on application
     *
     * @param stdClass $instance
     * @return void
     */
    protected function send_application_message($instance) {
        global $USER;

        if ($instance->customint4) {
            // Send message to user.
            list($subject, $messagetext, $messagehtml) = $this->prepare_message($instance, $USER,
                    'templateyouhaveappliedbody', 'templateyouhaveappliedsubject');

            email_to_user($USER, core_user::get_support_user(), $subject, $messagetext, $messagehtml);
        }

        // Notify enrollers.
        list($subject, $messagetext, $messagehtml) = $this->prepare_message($instance, $USER,
                'templateapplicationreceivedbody', 'templateapplicationreceivedsubject');

        $enrollers = get_users_by_capability(context_course::instance($instance->courseid),
                'enrol/approval:notify');
        foreach ($enrollers as $user) {
            email_to_user($user, core_user::get_support_user(), $subject, $messagetext, $messagehtml);
        }

    }

    /**
     * Send message to user when his application was approved
     *
     * @param stdClass $instance
     * @param int $userid
     */
    protected function send_approval_message(stdClass $instance, $userid) {
        global $DB;
        $user = $DB->get_record('user', array('id' => $userid));
        list($subject, $messagetext, $messagehtml) = $this->prepare_message($instance, $user,
                'templateyouareapprovedbody', 'templateyouareapprovedsubject');

        email_to_user($user, core_user::get_support_user(), $subject, $messagetext, $messagehtml);
    }

    /**
     * Send message to user when his application was declined
     *
     * @param stdClass $instance
     * @param int $userid
     */
    protected function send_decline_message(stdClass $instance, $userid) {
        global $DB;
        $user = $DB->get_record('user', array('id' => $userid));
        list($subject, $messagetext, $messagehtml) = $this->prepare_message($instance, $user,
                'templateyouaredeclinedbody', 'templateyouaredeclinedsubject');

        email_to_user($user, core_user::get_support_user(), $subject, $messagetext, $messagehtml);
    }

    /**
     * Prepare the message for sending by processing templates
     *
     * @param stdClass $instance
     * @param stdClass $user
     * @param string $bodystring
     * @param string $subjectstring
     * @return array
     */
    protected function prepare_message($instance, $user, $bodystring, $subjectstring) {
        global $CFG;
        $course = get_course($instance->courseid);
        $context = context_course::instance($course->id);

        $templates = @json_decode($instance->customtext1, true);

        $a = new stdClass();
        $a->coursename = format_string($course->fullname, true, array('context' => $context));
        $a->profileurl = "$CFG->wwwroot/user/view.php?id=$user->id&course=$course->id";
        $a->manageurl = "$CFG->wwwroot/enrol/approval/workflow.php?enrolid={$instance->id}";
        $a->approveurl = "$CFG->wwwroot/enrol/approval/workflow.php?userid={$user->id}&enrolid={$instance->id}&action=approve";
        $a->declineurl = "$CFG->wwwroot/enrol/approval/workflow.php?userid={$user->id}&enrolid={$instance->id}&action=decline";
        $a->username = fullname($user);
        $keys = array('{$a->coursename}', '{$a->profileurl}', '{$a->manageurl}',
            '{$a->username}', '{$a->approveurl}', '{$a->declineurl}');
        $values = array($a->coursename, $a->profileurl, $a->manageurl,
            $a->username, $a->approveurl, $a->declineurl);

        if (!empty($templates[$bodystring])) {
            $message = str_replace($keys, $values, $templates[$bodystring]);
            if (strpos($message, '<') === false) {
                // Plain text only.
                $messagetext = $message;
                $messagehtml = text_to_html($messagetext, null, false, true);
            } else {
                // This is most probably the tag/newline soup known as FORMAT_MOODLE.
                $messagehtml = format_text($message, FORMAT_MOODLE,
                        array('context' => $context, 'para' => false, 'newlines' => true, 'filter' => true));
                $messagetext = html_to_text($messagehtml);
            }
        } else {
            $messagetext = get_string($bodystring, 'enrol_approval', $a);
            $messagehtml = text_to_html($messagetext, null, false, true);
        }

        if (!empty($templates[$subjectstring])) {
            $subject = str_replace($keys, $values, $templates[$subjectstring]);
        } else {
            $subject = get_string($subjectstring, 'enrol_approval', $a);
        }

        return array($subject, $messagetext, $messagehtml);
    }

    /**
     * Store user_enrolments changes and trigger event.
     *
     * @param stdClass $instance
     * @param int $userid
     * @param int $status
     * @param int $timestart
     * @param int $timeend
     * @return void
     */
    public function update_user_enrol(stdClass $instance, $userid, $status = null, $timestart = null, $timeend = null) {
        global $DB;
        if (!$ue = $DB->get_record('user_enrolments',
                array('enrolid' => $instance->id, 'userid' => $userid))) {
            return;
        }
        parent::update_user_enrol($instance, $userid, $status, $timestart, $timeend);
        if ($status == ENROL_USER_ACTIVE && $ue->status != ENROL_USER_ACTIVE) {
            $this->send_approval_message($instance, $userid);
        }
    }

    /**
     * Unenrol user from course,
     * the last unenrolment removes all remaining roles.
     *
     * @param stdClass $instance
     * @param int $userid
     * @return void
     */
    public function unenrol_user(stdClass $instance, $userid) {
        global $DB;
        if (!$ue = $DB->get_record('user_enrolments',
                array('enrolid' => $instance->id, 'userid' => $userid))) {
            return;
        }
        parent::unenrol_user($instance, $userid);
        if ($ue->status == ENROL_USER_SUSPENDED) {
            $this->send_decline_message($instance, $userid);
        }
    }

    /**
     * Enrol self cron support.
     * @return void
     */
    public function cron() {
        $trace = new text_progress_trace();
        $this->sync($trace, null);
        $this->send_expiry_notifications($trace);
    }

    /**
     * Sync all meta course links.
     *
     * @param progress_trace $trace
     * @param int $courseid one course, empty mean all
     * @return int 0 means ok, 1 means error, 2 means plugin disabled
     */
    public function sync(progress_trace $trace, $courseid = null) {
        global $DB;

        if (!enrol_is_enabled('approval')) {
            $trace->finished();
            return 2;
        }

        // Unfortunately this may take a long time, execution can be interrupted safely here.
        core_php_time_limit::raise();
        raise_memory_limit(MEMORY_HUGE);

        $trace->output('Verifying self-enrolments...');

        $params = array('now' => time(), 'useractive' => ENROL_USER_ACTIVE, 'courselevel' => CONTEXT_COURSE);
        $coursesql = "";
        if ($courseid) {
            $coursesql = "AND e.courseid = :courseid";
            $params['courseid'] = $courseid;
        }

        // Note: the logic of self enrolment guarantees that user logged in at least once (=== u.lastaccess set)
        //       and that user accessed course at least once too (=== user_lastaccess record exists).

        // First deal with users that did not log in for a really long time - they do not have user_lastaccess records.
        $sql = "SELECT e.*, ue.userid
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = 'approval' AND e.customint2 > 0)
                  JOIN {user} u ON u.id = ue.userid
                 WHERE :now - u.lastaccess > e.customint2
                       $coursesql";
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $instance) {
            $userid = $instance->userid;
            unset($instance->userid);
            $this->unenrol_user($instance, $userid);
            $days = $instance->customint2 / 60 * 60 * 24;
            $trace->output("unenrolling user $userid from course $instance->courseid ".
                    "as they have did not log in for at least $days days", 1);
        }
        $rs->close();

        // Now unenrol from course user did not visit for a long time.
        $sql = "SELECT e.*, ue.userid
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = 'approval' AND e.customint2 > 0)
                  JOIN {user_lastaccess} ul ON (ul.userid = ue.userid AND ul.courseid = e.courseid)
                 WHERE :now - ul.timeaccess > e.customint2
                       $coursesql";
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $instance) {
            $userid = $instance->userid;
            unset($instance->userid);
            $this->unenrol_user($instance, $userid);
                $days = $instance->customint2 / 60 * 60 * 24;
            $trace->output("unenrolling user $userid from course $instance->courseid ".
                    "as they have did not access course for at least $days days", 1);
        }
        $rs->close();

        $trace->output('...user self-enrolment updates finished.');
        $trace->finished();

        $this->process_expirations($trace, $courseid);

        return 0;
    }

    /**
     * Returns the user who is responsible for self enrolments in given instance.
     *
     * Usually it is the first editing teacher - the person with "highest authority"
     * as defined by sort_by_roleassignment_authority() having 'enrol/approval:manage'
     * capability.
     *
     * @param int $instanceid enrolment instance id
     * @return stdClass user record
     */
    protected function get_enroller($instanceid) {
        global $DB;

        if ($this->lasternollerinstanceid == $instanceid and $this->lasternoller) {
            return $this->lasternoller;
        }

        $instance = $DB->get_record('enrol', array('id' => $instanceid, 'enrol' => $this->get_name()), '*', MUST_EXIST);
        $context = context_course::instance($instance->courseid);

        if ($users = get_enrolled_users($context, 'enrol/approval:manage')) {
            $users = sort_by_roleassignment_authority($users, $context);
            $this->lasternoller = reset($users);
            unset($users);
        } else {
            $this->lasternoller = parent::get_enroller($instanceid);
        }

        $this->lasternollerinstanceid = $instanceid;

        return $this->lasternoller;
    }

    /**
     * Gets an array of the user enrolment actions.
     *
     * @param course_enrolment_manager $manager
     * @param stdClass $ue A user enrolment object
     * @return array An array of user_enrolment_actions
     */
    public function get_user_enrolment_actions(course_enrolment_manager $manager, $ue) {
        $context = $manager->get_context();
        $redirecturl = $manager->get_moodlepage()->url;
        return $this->user_enrolment_actions($context, $redirecturl, $ue);
    }

    /**
     * List of enrolment actions available to the current user
     *
     * @param context_course $context
     * @param moodle_url $redirecturl
     * @param stdClass $ue
     * @return user_enrolment_action[]
     */
    protected function user_enrolment_actions($context, $redirecturl, $ue) {
        $actions = array();
        $instance = $ue->enrolmentinstance;
        $params = $redirecturl->params();
        $params['ue'] = $ue->id;
        if ($ue->status == ENROL_USER_ACTIVE && $this->allow_unenrol($instance) &&
                has_capability('enrol/approval:unenrol', $context)) {
            $url = new moodle_url('/enrol/unenroluser.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/delete', ''),
                    get_string('unenrol', 'enrol'), $url, array('class' => 'unenrollink', 'rel' => $ue->id));
        }
        $url = new moodle_url('/enrol/approval/workflow.php',
                array('ue' => $ue->id,
                    'redirecturl' => $redirecturl));
        if (has_capability('enrol/approval:manage', $context)) {
            $actions[] = new user_enrolment_action(new pix_icon('t/edit', ''), get_string('edit'),
                    new moodle_url($url, array('action' => 'edit')),
                    array('class' => 'editenrollink', 'rel' => $ue->id));
        }
        if (has_capability('enrol/approval:approve', $context) && $ue->status == ENROL_USER_SUSPENDED) {
            $url->param('sesskey', sesskey());
            $actions[] = new user_enrolment_action(new pix_icon('i/invalid', ''),
                    get_string('decline', 'enrol_approval'),
                    new moodle_url($url, array('action' => 'decline')),
                    array('class' => 'declineenrollink', 'rel' => $ue->id));
            $actions[] = new user_enrolment_action(new pix_icon('i/valid', ''),
                    get_string('approve', 'enrol_approval'),
                    new moodle_url($url, array('action' => 'approve')),
                    array('class' => 'approveenrollink', 'rel' => $ue->id));
        }
        return $actions;
    }

    /**
     * Counts the number of users with suspended status
     *
     * @param stdClass $instance
     * @return int
     */
    public function count_inactive_users($instance) {
        global $DB;
        return $DB->count_records_sql('SELECT COUNT(u.id)
                FROM {user} u, {user_enrolments} ue
                WHERE u.id = ue.userid AND ue.enrolid = ? AND ue.status = ?',
                array($instance->id, ENROL_USER_SUSPENDED));
    }

    /**
     * Returns simple representation of the list of suspended users
     *
     * Is used in workflow.php for staff who is able to approve but not able
     * to review enrolments
     *
     * @param stdClass $instance
     * @param int|null $count
     * @return string
     */
    public function overview_inactive_users($instance, $count = null) {
        global $DB, $OUTPUT, $PAGE;

        if ($count === 0) {
            $users = array();
        } else {
            $namefields = get_all_user_name_fields(true, 'u');
            $users = $DB->get_records_sql('select u.id, '.$namefields.',
                    ue.timecreated AS applied, ue.id AS userenrolmentid
                    FROM {user} u, {user_enrolments} ue
                    WHERE u.id = ue.userid AND ue.enrolid = ? AND ue.status = ?
                    ORDER BY ue.timecreated',
                    array($instance->id, ENROL_USER_SUSPENDED));
        }

        if (!$users) {
            return $OUTPUT->notification(get_string('nousers', 'enrol_approval')).
                    (has_capability('moodle/course:enrolreview', $PAGE->context) ?
                    html_writer::link(new moodle_url('/enrol/users.php',
                            array('ifilter' => $instance->id, 'id' => $instance->courseid)),
                            get_string('viewallenrolled', 'enrol_approval')) : '');
        }

        $renderer = $PAGE->get_renderer('core_enrol');
        $table = new html_table();
        $table->head = array(
            get_string('name'),
            get_string('actions'),
        );
        $table->data = array();
        $userprofile = new moodle_url('/user/view.php', array('course' => $instance->courseid));
        $context = context_course::instance($instance->courseid);
        $redirecturl = new moodle_url('/enrol/approval/workflow.php', array('iid' => $instance->id));
        $ue = (object)array('status' => ENROL_USER_SUSPENDED, 'enrolmentinstance' => $instance->id);
        foreach ($users as $user) {
            $ue->id = $user->userenrolmentid;
            $actions = $this->user_enrolment_actions($context, $redirecturl, $ue);
            $txt = '';
            foreach (array_reverse($actions) as $action) {
                $txt .= $renderer->render($action);
            }
            $table->data[] = array(
                html_writer::link(new moodle_url($userprofile, array('id' => $user->id)), fullname($user)),
                $txt
            );
        }
        return html_writer::table($table);
    }

    /**
     * The enrol_approval plugin has several bulk operations that can be performed.
     *
     * @param course_enrolment_manager $manager
     * @return array
     */
    public function get_bulk_operations(course_enrolment_manager $manager) {
        global $CFG;
        $context = $manager->get_context();
        $bulkoperations = array();
        if (has_capability('enrol/approval:approve', $context)) {
            $bulkoperations['approveselectedusers'] = new enrol_approval_approveselectedusers_operation($manager, $this);
        }
        if (has_capability('enrol/approval:approve', $context)) {
            $bulkoperations['declineselectedusers'] = new enrol_approval_declineselectedusers_operation($manager, $this);
        }
        if (has_capability('enrol/approval:manage', $context)) {
            $bulkoperations['editselectedusers'] = new enrol_approval_editselectedusers_operation($manager, $this);
        }
        if (has_capability('enrol/approval:unenrol', $context)) {
            $bulkoperations['deleteselectedusers'] = new enrol_approval_deleteselectedusers_operation($manager, $this);
        }
        return $bulkoperations;
    }

    /**
     * Restore instance and map settings.
     *
     * @param restore_enrolments_structure_step $step
     * @param stdClass $data
     * @param stdClass $course
     * @param int $oldid
     */
    public function restore_instance(restore_enrolments_structure_step $step, stdClass $data, $course, $oldid) {
        global $DB;
        if ($step->get_task()->get_target() == backup::TARGET_NEW_COURSE) {
            $merge = false;
        } else {
            $merge = array(
                'courseid'   => $data->courseid,
                'enrol'      => $this->get_name(),
                'roleid'     => $data->roleid,
            );
        }
        if ($merge and $instances = $DB->get_records('enrol', $merge, 'id')) {
            $instance = reset($instances);
            $instanceid = $instance->id;
        } else {
            if (!empty($data->customint5)) {
                if (!$step->get_task()->is_samesite()) {
                    // Use some id that can not exist in order to prevent self enrolment,
                    // because we do not know what cohort it is in this site.
                    $data->customint5 = -1;
                }
            }
            $instanceid = $this->add_instance($course, (array)$data);
        }
        $step->set_mapping('enrol', $oldid, $instanceid);
    }

    /**
     * Restore user enrolment.
     *
     * @param restore_enrolments_structure_step $step
     * @param stdClass $data
     * @param stdClass $instance
     * @param int $userid
     * @param int $oldinstancestatus
     */
    public function restore_user_enrolment(restore_enrolments_structure_step $step, $data, $instance, $userid, $oldinstancestatus) {
        $this->enrol_user($instance, $userid, null, $data->timestart, $data->timeend, $data->status);
    }

    /**
     * Restore role assignment.
     *
     * @param stdClass $instance
     * @param int $roleid
     * @param int $userid
     * @param int $contextid
     */
    public function restore_role_assignment($instance, $roleid, $userid, $contextid) {
        // This is necessary only because we may migrate other types to this instance,
        // we do not use component in manual or self enrol.
        role_assign($roleid, $userid, $contextid, '', 0);
    }

    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_delete_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/approval:config', $context);
    }

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_hide_show_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/approval:config', $context);
    }
}
