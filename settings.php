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
 * Self enrolment plugin settings and presets.
 *
 * @package    enrol_approval
 * @copyright  2015 Alex Mitin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    // General settings.
    $settings->add(new admin_setting_heading('enrol_approval_settings', '', get_string('pluginname_desc', 'enrol_approval')));

    // Note: let's reuse the ext sync constants and strings here, internally it is very similar,
    //       it describes what should happend when users are not supposed to be enerolled any more.
    $options = array(
        ENROL_EXT_REMOVED_KEEP           => get_string('extremovedkeep', 'enrol'),
        ENROL_EXT_REMOVED_SUSPENDNOROLES => get_string('extremovedsuspendnoroles', 'enrol'),
        ENROL_EXT_REMOVED_UNENROL        => get_string('extremovedunenrol', 'enrol'),
    );
    $settings->add(new admin_setting_configselect('enrol_approval/expiredaction',
            get_string('expiredaction', 'enrol_approval'), get_string('expiredaction_help', 'enrol_approval'),
            ENROL_EXT_REMOVED_KEEP, $options));

    $options = array();
    for ($i = 0; $i < 24; $i++) {
        $options[$i] = $i;
    }
    $settings->add(new admin_setting_configselect('enrol_approval/expirynotifyhour',
            get_string('expirynotifyhour', 'core_enrol'), '', 6, $options));

    // Rnrol instance defaults.
    $settings->add(new admin_setting_heading('enrol_approval_defaults',
        get_string('enrolinstancedefaults', 'admin'), get_string('enrolinstancedefaults_desc', 'admin')));

    $settings->add(new admin_setting_configcheckbox('enrol_approval/defaultenrol',
        get_string('defaultenrol', 'enrol'), get_string('defaultenrol_desc', 'enrol'), 1));

    $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                     ENROL_INSTANCE_DISABLED => get_string('no'));
    $settings->add(new admin_setting_configselect('enrol_approval/status',
        get_string('status', 'enrol_approval'), get_string('status_desc', 'enrol_approval'), ENROL_INSTANCE_DISABLED, $options));

    $options = array(1  => get_string('yes'), 0 => get_string('no'));
    $settings->add(new admin_setting_configselect('enrol_approval/newenrols',
        get_string('newenrols', 'enrol_approval'), get_string('newenrols_desc', 'enrol_approval'), 1, $options));

    if (!during_initial_install()) {
        $options = get_default_enrol_roles(context_system::instance());
        $student = get_archetype_roles('student');
        $student = reset($student);
        $settings->add(new admin_setting_configselect('enrol_approval/roleid',
            get_string('defaultrole', 'enrol_approval'), get_string('defaultrole_desc', 'enrol_approval'), $student->id, $options));
    }

    $settings->add(new admin_setting_configduration('enrol_approval/enrolperiod',
        get_string('enrolperiod', 'enrol_approval'), get_string('enrolperiod_desc', 'enrol_approval'), 0));

    $options = array(
        0 => get_string('no'),
        1 => get_string('expirynotifyenroller', 'core_enrol'),
        2 => get_string('expirynotifyall', 'core_enrol')
    );
    $settings->add(new admin_setting_configselect('enrol_approval/expirynotify',
        get_string('expirynotify', 'core_enrol'), get_string('expirynotify_help', 'core_enrol'), 0, $options));

    $settings->add(new admin_setting_configduration('enrol_approval/expirythreshold',
        get_string('expirythreshold', 'core_enrol'), get_string('expirythreshold_help', 'core_enrol'), 86400, 86400));

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
    $settings->add(new admin_setting_configselect('enrol_approval/longtimenosee',
        get_string('longtimenosee', 'enrol_approval'), get_string('longtimenosee_help', 'enrol_approval'), 0, $options));

    $settings->add(new admin_setting_configtext('enrol_approval/maxenrolled',
        get_string('maxenrolled', 'enrol_approval'), get_string('maxenrolled_help', 'enrol_approval'), 0, PARAM_INT));

    $settings->add(new admin_setting_configcheckbox('enrol_approval/sendcoursewelcomemessage',
        get_string('sendcoursewelcomemessage', 'enrol_approval'),
            get_string('sendcoursewelcomemessage_help', 'enrol_approval'), 1));
}
