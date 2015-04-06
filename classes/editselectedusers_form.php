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
 * Contains class enrol_approval_editselectedusers_form
 *
 * @package   enrol_approval
 * @copyright 2015 Alex Mitin
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Form for bulk editing users enrolments
 *
 * @package   enrol_approval
 * @copyright 2015 Alex Mitin
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_approval_editselectedusers_form extends enrol_approval_bulk_enrolment_change_form {
    /**
     * Form definition
     */
    public function definition() {
        $form = $this->_form;
        $users = $this->_customdata['users'];

        $statusoptions = $this->get_status_options();
        $form->addElement('html', $this->get_users_table($users, $statusoptions));
        // In this method the enrolment status can not be changed from "active" to "suspended".
        unset($statusoptions[ENROL_USER_SUSPENDED]);
        $form->addElement('select', 'status', get_string('alterstatus', 'enrol_manual'), $statusoptions, array('optional' => true));
        $form->addElement('date_time_selector', 'timestart', get_string('altertimestart', 'enrol_manual'),
                array('optional' => true));
        $form->addElement('date_time_selector', 'timeend', get_string('altertimeend', 'enrol_manual'), array('optional' => true));

        $this->add_action_buttons();
    }
}