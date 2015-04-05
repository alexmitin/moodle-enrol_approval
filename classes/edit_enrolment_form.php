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
 * Contains class enrol_approval_edit_enrolment_form
 *
 * @package   enrol_approval
 * @copyright 2015 Alex Mitin
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require($CFG->dirroot . '/enrol/editenrolment_form.php');

/**
 * Form for editing of single user enrolment
 *
 * @package   enrol_approval
 * @copyright 2015 Alex Mitin
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_approval_edit_enrolment_form extends enrol_user_enrolment_form {
    /**
     * Executed after set_data
     */
    public function definition_after_data() {
        parent::definition_after_data();
        $mform = $this->_form;
        if ($el = $mform->getElement('status')) {
            $el->_options[ENROL_USER_SUSPENDED]['text'] = get_string('participationsuspended', 'enrol_approval');
            $el->_options[ENROL_USER_ACTIVE]['text'] = get_string('participationactive', 'enrol_approval');
            $ue = $this->_customdata['ue'];
            if ($ue->status == ENROL_USER_ACTIVE) {
                // It is not allowed to suspend the enrolment in this method.
                $mform->freeze('status');
            }
        }
    }

    /**
     * Returns form data
     * @return array
     */
    public function get_data() {
        $data = parent::get_data();
        if ($data && !isset($data->status)) {
            $data->status = $this->_customdata['ue']->status;
        }
        return $data;
    }
}