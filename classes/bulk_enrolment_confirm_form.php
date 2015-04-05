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
 * Contains class enrol_approval_bulk_enrolment_confirm_form
 *
 * @package   enrol_approval
 * @copyright 2015 Alex Mitin
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Base confirmation form for the bulk actions in enrol_approval
 *
 * @package   enrol_approval
 * @copyright 2015 Alex Mitin
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class enrol_approval_bulk_enrolment_confirm_form extends enrol_approval_bulk_enrolment_change_form {

    /** @var int|null required enrolment status of users in this bulk action */
    protected $requiredstatus = null;

    /**
     * Defines the standard structure of the form
     */
    protected function definition() {
        global $OUTPUT;

        $form = $this->_form;
        $users = $this->_customdata['users'];
        $title = $this->_customdata['title'];
        $message = $this->_customdata['message'];
        $button = $this->_customdata['button'];

        $filteredusers = $this->filter_users($users);
        if (!count($filteredusers)) {
            $options = $this->get_status_options();
            $form->addElement('html', $OUTPUT->box(get_string('noapplicableusers', 'enrol_approval',
                    $options[$this->requiredstatus]), 'generalbox notifyproblem'));
            $form->addElement('cancel', 'cancelbutton');
            $form->closeHeaderBefore('cancelbutton');
        } else {
            if (count($filteredusers) != count($users)) {
                $options = $this->get_status_options();
                $form->addElement('html', $OUTPUT->box(get_string('partialapplicableusers', 'enrol_approval',
                        $options[$this->requiredstatus]), 'generalbox notifyproblem'));
            }
            $table = $this->get_users_table($filteredusers, $this->get_status_options());
            $form->addElement('html', $table);
            $form->addElement('header', 'ebecf_header', $title);
            $form->addElement('html', html_writer::tag('p', $message));
            $this->add_action_buttons(true, $button);
        }
    }

    /**
     * Filters the list of user by the required status
     *
     * @param array $users
     * @return array
     */
    protected function filter_users($users) {
        if ($this->requiredstatus === null) {
            return $users;
        }
        $filteredusers = array();
        $extrausers = '';
        foreach ($users as $id => $user) {
            $hasstatus = false;
            foreach ($user->enrolments as $enrolment) {
                $hasstatus = $hasstatus || ($enrolment->status == $this->requiredstatus);
            }
            if ($hasstatus) {
                $filteredusers[$id] = $user;
            } else {
                $extrausers .= html_writer::empty_tag('input',
                        array('type' => 'hidden', 'name' => 'bulkuser[]', 'value' => $user->id));
            }
        }
        $this->_form->addElement('html', $extrausers);
        return $filteredusers;
    }
}
