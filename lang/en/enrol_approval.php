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
 * Strings for component 'enrol_approval', language 'en'.
 *
 * @package    enrol_approval
 * @copyright  2015 Alex Mitin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['approval:config'] = 'Configure instances of enrolment by approval';
$string['approval:manage'] = 'Manage enrolled users';
$string['approval:unenrol'] = 'Unenrol users from course';
$string['approval:unenrolself'] = 'Unenrol self from the course';
$string['approve'] = 'Approve enrolment';
$string['canntenrol'] = 'Enrolment is disabled or inactive';
$string['cohortnonmemberinfo'] = 'Only members of cohort \'{$a}\' can request enrolment.';
$string['cohortonly'] = 'Only cohort members';
$string['cohortonly_help'] = 'Self enrolment may be restricted to members of a specified cohort only. Note that changing this setting has no effect on existing enrolments.';
$string['customwelcomemessage'] = 'Custom welcome message';
$string['customwelcomemessage_help'] = 'A custom welcome message may be added as plain text or Moodle-auto format, including HTML tags and multi-lang tags.

The following placeholders may be included in the message:

* Course name {$a->coursename}
* Link to user\'s profile page {$a->profileurl}';
$string['decline'] = 'Decline enrolment';
$string['defaultrole'] = 'Default role assignment';
$string['defaultrole_desc'] = 'Select role which should be assigned to users during the enrolment';
$string['enrolenddate'] = 'End date';
$string['enrolenddate_help'] = 'If enabled, users can enrol themselves until this date only.';
$string['enrolenddaterror'] = 'Enrolment end date cannot be earlier than start date';
$string['enrolme'] = 'Enrol me';
$string['enrolperiod'] = 'Enrolment duration';
$string['enrolperiod_desc'] = 'Default length of time that the enrolment is valid. If set to zero, the enrolment duration will be unlimited by default.';
$string['enrolperiod_help'] = 'Length of time that the enrolment is valid, starting with the moment the user requests enrolment. If disabled, the enrolment duration will be unlimited.';
$string['enrolstartdate'] = 'Start date';
$string['enrolstartdate_help'] = 'If enabled, users can enrol themselves from this date onward only.';
$string['expiredaction'] = 'Enrolment expiration action';
$string['expiredaction_help'] = 'Select action to carry out when user enrolment expires. Please note that some user data and settings are purged from course during course unenrolment.';
$string['expirymessageenrollersubject'] = 'Self enrolment expiry notification';
$string['expirymessageenrollerbody'] = 'Self enrolment in the course \'{$a->course}\' will expire within the next {$a->threshold} for the following users:

{$a->users}

To extend their enrolment, go to {$a->extendurl}';
$string['expirymessageenrolledsubject'] = 'Self enrolment expiry notification';
$string['expirymessageenrolledbody'] = 'Dear {$a->user},

This is a notification that your enrolment in the course \'{$a->course}\' is due to expire on {$a->timeend}.

If you need help, please contact {$a->enroller}.';
$string['longtimenosee'] = 'Unenrol inactive after';
$string['longtimenosee_help'] = 'If users haven\'t accessed a course for a long time, then they are automatically unenrolled. This parameter specifies that time limit.';
$string['maxenrolled'] = 'Max enrolled users';
$string['maxenrolled_help'] = 'Specifies the maximum number of users that can self enrol. 0 means no limit.';
$string['maxenrolledreached'] = 'Maximum number of users allowed to self-enrol was already reached.';
$string['messageprovider:expiry_notification'] = 'Self enrolment expiry notifications';
$string['newenrols'] = 'Allow new enrolment requests';
$string['newenrols_desc'] = 'Allow users to self enrol into new courses by default.';
$string['newenrols_help'] = 'This setting determines whether a user can enrol into this course.';
$string['pluginname'] = 'Self enrolment with approval';
$string['pluginname_desc'] = 'The self enrolment by approval plugin allows users to choose which courses they want to participate in. Approval of teacher/manager is requried.';
$string['requestenrolment'] = 'Request enrolment in this course.';
$string['role'] = 'Default assigned role';
$string['sendcoursewelcomemessage'] = 'Send course welcome message';
$string['sendcoursewelcomemessage_help'] = 'If enabled, users receive a welcome message via email when they self-enrol in a course.';
$string['status'] = 'Enable existing enrolments';
$string['status_desc'] = 'Enable self enrolment by approval method in new courses.';
$string['status_help'] = 'If disabled all existing enrolments under this method are suspended and new users can not enrol.';
$string['unenrol'] = 'Unenrol user';
$string['unenrolselfconfirm'] = 'Do you really want to unenrol yourself from course "{$a}"?';
$string['unenroluser'] = 'Do you really want to unenrol "{$a->user}" from course "{$a->course}"?';
$string['welcometocourse'] = 'Welcome to {$a}';
$string['welcometocoursetext'] = 'Welcome to {$a->coursename}!

If you have not done so already, you should edit your profile page so that we can learn more about you:

  {$a->profileurl}';
