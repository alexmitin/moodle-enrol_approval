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
 * Adds new instance of enrol_approval to specified course or edits current instance.
 *
 * @package    enrol_approval
 * @copyright  2015 Alex Mitin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/cohort/lib.php');

/**
 * Adds new instance of enrol_approval to specified course or edits current instance.
 *
 * @package    enrol_approval
 * @copyright  2015 Alex Mitin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_approval_instance_edit_form extends moodleform {

    /** @var array */
    protected $templatekeys = array('templateyouhaveappliedbody', 'templateyouhaveappliedsubject',
        'templateapplicationreceivedbody', 'templateapplicationreceivedsubject',
        'templateyouareapprovedbody', 'templateyouareapprovedsubject',
        'templateyouaredeclinedbody', 'templateyouaredeclinedsubject');

    /**
     * Form definition
     */
    public function definition() {
        global $DB;

        $mform = $this->_form;

        list($instance, $plugin, $context) = $this->_customdata;

        $mform->addElement('header', 'header', get_string('pluginname', 'enrol_approval'));

        $nameattribs = array('size' => '20', 'maxlength' => '255');
        $mform->addElement('text', 'name', get_string('custominstancename', 'enrol'), $nameattribs);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'server');

        $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                         ENROL_INSTANCE_DISABLED => get_string('no'));
        $mform->addElement('select', 'status', get_string('status', 'enrol_approval'), $options);
        $mform->addHelpButton('status', 'status', 'enrol_approval');

        $options = array(1 => get_string('yes'), 0 => get_string('no'));
        $mform->addElement('select', 'customint6', get_string('newenrols', 'enrol_approval'), $options);
        $mform->addHelpButton('customint6', 'newenrols', 'enrol_approval');
        $mform->disabledIf('customint6', 'status', 'eq', ENROL_INSTANCE_DISABLED);

        $roles = $this->extend_assignable_roles($context, $instance->roleid);
        $mform->addElement('select', 'roleid', get_string('role', 'enrol_approval'), $roles);

        $mform->addElement('duration', 'enrolperiod', get_string('enrolperiod', 'enrol_approval'),
                array('optional' => true, 'defaultunit' => 86400));
        $mform->addHelpButton('enrolperiod', 'enrolperiod', 'enrol_approval');

        $options = array(
            0 => get_string('no'),
            1 => get_string('expirynotifyenroller', 'core_enrol'),
            2 => get_string('expirynotifyall', 'core_enrol')
        );
        $mform->addElement('select', 'expirynotify', get_string('expirynotify', 'core_enrol'), $options);
        $mform->addHelpButton('expirynotify', 'expirynotify', 'core_enrol');

        $mform->addElement('duration', 'expirythreshold', get_string('expirythreshold', 'core_enrol'),
                array('optional' => false, 'defaultunit' => 86400));
        $mform->addHelpButton('expirythreshold', 'expirythreshold', 'core_enrol');
        $mform->disabledIf('expirythreshold', 'expirynotify', 'eq', 0);

        $mform->addElement('date_time_selector', 'enrolstartdate',
                get_string('enrolstartdate', 'enrol_approval'), array('optional' => true));
        $mform->setDefault('enrolstartdate', 0);
        $mform->addHelpButton('enrolstartdate', 'enrolstartdate', 'enrol_approval');

        $mform->addElement('date_time_selector', 'enrolenddate',
                get_string('enrolenddate', 'enrol_approval'), array('optional' => true));
        $mform->setDefault('enrolenddate', 0);
        $mform->addHelpButton('enrolenddate', 'enrolenddate', 'enrol_approval');

        $options = array(0 => get_string('never'),
                 1800 * 3600 * 24 => get_string('numdays', '', 1800),
                 1000 * 3600 * 24 => get_string('numdays', '', 1000),
                 365 * 3600 * 24 => get_string('numdays', '', 365),
                 180 * 3600 * 24 => get_string('numdays', '', 180),
                 150 * 3600 * 24 => get_string('numdays', '', 150),
                 120 * 3600 * 24 => get_string('numdays', '', 120),
                 90 * 3600 * 24 => get_string('numdays', '', 90),
                 60 * 3600 * 24 => get_string('numdays', '', 60),
                 30 * 3600 * 24 => get_string('numdays', '', 30),
                 21 * 3600 * 24 => get_string('numdays', '', 21),
                 14 * 3600 * 24 => get_string('numdays', '', 14),
                 7 * 3600 * 24 => get_string('numdays', '', 7));
        $mform->addElement('select', 'customint2', get_string('longtimenosee', 'enrol_approval'), $options);
        $mform->addHelpButton('customint2', 'longtimenosee', 'enrol_approval');

        $mform->addElement('text', 'customint3', get_string('maxenrolled', 'enrol_approval'));
        $mform->addHelpButton('customint3', 'maxenrolled', 'enrol_approval');
        $mform->setType('customint3', PARAM_INT);

        $cohorts = array(0 => get_string('no'));
        $allcohorts = cohort_get_available_cohorts($context, 0, 0, 0);
        if ($instance->customint5 && !isset($allcohorts[$instance->customint5]) &&
                ($c = $DB->get_record('cohort', array('id' => $instance->customint5),
                        'id, name, idnumber, contextid, visible', IGNORE_MISSING))) {
            // Current cohort was not found because current user can not see it. Still keep it.
            $allcohorts[$instance->customint5] = $c;
        }
        foreach ($allcohorts as $c) {
            $cohorts[$c->id] = format_string($c->name, true,
                    array('context' => context::instance_by_id($c->contextid)));
            if ($c->idnumber) {
                $cohorts[$c->id] .= ' ['.s($c->idnumber).']';
            }
        }
        if ($instance->customint5 && !isset($allcohorts[$instance->customint5])) {
            // Somebody deleted a cohort, better keep the wrong value so that random ppl can not enrol.
            $cohorts[$instance->customint5] = get_string('unknowncohort', 'cohort', $instance->customint5);
        }
        if (count($cohorts) > 1) {
            $mform->addElement('select', 'customint5', get_string('cohortonly', 'enrol_approval'), $cohorts);
            $mform->addHelpButton('customint5', 'cohortonly', 'enrol_approval');
        } else {
            $mform->addElement('hidden', 'customint5');
            $mform->setType('customint5', PARAM_INT);
            $mform->setConstant('customint5', 0);
        }

        if (enrol_accessing_via_instance($instance)) {
            $mform->addElement('static', 'selfwarn', get_string('instanceeditselfwarning', 'core_enrol'),
                    get_string('instanceeditselfwarningtext', 'core_enrol'));
        }

        $mform->addElement('header', 'notifications_hdr', get_string('messagestemplates', 'enrol_approval'));
        $mform->setExpanded('notifications_hdr', false);

        $mform->addElement('advcheckbox', 'customint4',
                get_string('sendmessageonapplication', 'enrol_approval'));
        $mform->addHelpButton('customint4', 'sendmessageonapplication', 'enrol_approval');

        $this->add_element_with_default('text', 'templateyouhaveappliedsubject', 'customyouhaveappliedsubject');
        $this->add_element_with_default('textarea', 'templateyouhaveappliedbody', 'customyouhaveappliedbody');

        $this->add_element_with_default('text', 'templateapplicationreceivedsubject',
                'customapplicationreceivedsubject', 'customapplicationreceivedsubject');
        $this->add_element_with_default('textarea', 'templateapplicationreceivedbody', 'customapplicationreceivedbody');

        $this->add_element_with_default('text', 'templateyouareapprovedsubject', 'customapprovalsubject');
        $this->add_element_with_default('textarea', 'templateyouareapprovedbody', 'customapprovalbody');

        $this->add_element_with_default('text', 'templateyouaredeclinedsubject', 'customdeclinesubject');
        $this->add_element_with_default('textarea', 'templateyouaredeclinedbody', 'customdeclinebody');

        $mform->addElement('static', '', '', get_string('custommessageexplained', 'enrol_approval'));

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons(true, ($instance->id ? null : get_string('addinstance', 'enrol')));

        $this->set_data($instance);
    }

    /**
     * Adds an element with "use default" checkbox
     *
     * @param string $eltype
     * @param string $key
     * @param string $titlestr
     * @param string $helpstr
     */
    protected function add_element_with_default($eltype, $key, $titlestr, $helpstr = null) {
        $mform = $this->_form;
        $buttonarray = array();
        $buttonarray[] = $mform->createElement('advcheckbox', $key . '_usedefault', '',
                get_string('usedefault', 'enrol_approval'));
        $buttonarray[] = $mform->createElement($eltype, $key, '',
                ($eltype === 'textarea') ? array('cols' => '60', 'rows' => '8') : array('size' => 60));
        $mform->setType($key, PARAM_RAW);
        $mform->disabledIf($key, $key.'_usedefault', 'checked', 1);
        $mform->addGroup($buttonarray, $key.'_grp', get_string($titlestr, 'enrol_approval'), array('<br/>'), false);
        if ($helpstr) {
            $mform->addHelpButton($key.'_grp', $helpstr, 'enrol_approval');
        }
    }

    /**
     * Set form data
     *
     * @param stdClass $data
     */
    public function set_data($data) {
        $data = fullclone($data);
        $templates = @json_decode($data->customtext1, true);
        foreach ($this->templatekeys as $key) {
            if (!empty($templates[$key])) {
                $data->$key = $templates[$key];
                $data->{$key.'_usedefault'} = 0;
            } else {
                $data->$key = get_string($key, 'enrol_approval');
                $data->{$key.'_usedefault'} = 1;
            }
        }
        unset($data->customtext1);
        parent::set_data($data);
    }

    /**
     * Form validation
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        global $DB, $CFG;
        $errors = parent::validation($data, $files);

        list($instance, $plugin, $context) = $this->_customdata;

        if ($data['status'] == ENROL_INSTANCE_ENABLED) {
            if (!empty($data['enrolenddate']) and $data['enrolenddate'] < $data['enrolstartdate']) {
                $errors['enrolenddate'] = get_string('enrolenddaterror', 'enrol_approval');
            }
        }

        if ($data['expirynotify'] > 0 and $data['expirythreshold'] < 86400) {
            $errors['expirythreshold'] = get_string('errorthresholdlow', 'core_enrol');
        }

        return $errors;
    }

    /**
     * Gets a list of roles that this user can assign for the course as the default for self-enrolment.
     *
     * @param context $context the context.
     * @param integer $defaultrole the id of the role that is set as the default for self-enrolment
     * @return array index is the role id, value is the role name
     */
    protected function extend_assignable_roles($context, $defaultrole) {
        global $DB;

        $roles = get_assignable_roles($context, ROLENAME_BOTH);
        if (!isset($roles[$defaultrole])) {
            if ($role = $DB->get_record('role', array('id' => $defaultrole))) {
                $roles[$defaultrole] = role_get_name($role, $context, ROLENAME_BOTH);
            }
        }
        return $roles;
    }

    /**
     * Gets the form data
     *
     * @return stdClass|null
     */
    public function get_data() {
        if (!$data = parent::get_data()) {
            return $data;
        }
        $templates = array();
        foreach ($this->templatekeys as $key) {
            if (isset($data->$key) && empty($data->{$key.'_usedefault'})) {
                $templates[$key] = $data->$key;
                unset($data->$key);
            }
        }
        $data->customtext1 = json_encode($templates);
        return $data;
    }
}
