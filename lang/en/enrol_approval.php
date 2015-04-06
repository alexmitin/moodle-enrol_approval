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

$string['approval:approve'] = 'Approve and decline enrolment requests';
$string['approval:config'] = 'Configure instances of enrolment by approval';
$string['approval:manage'] = 'Manage enrolled users';
$string['approval:notify'] = 'Be notified about new enrolment requests';
$string['approval:unenrol'] = 'Unenrol users from course';
$string['approval:unenrolself'] = 'Unenrol self from the course';
$string['approve'] = 'Approve enrolment';
$string['approveusers'] = 'Approve enrolments';
$string['approveselectedusers'] = 'Approve selected users enrolments';
$string['canntenrol'] = 'Enrolment is disabled or inactive';
$string['cohortnonmemberinfo'] = 'Only members of cohort \'{$a}\' can request enrolment.';
$string['cohortonly'] = 'Only cohort members';
$string['cohortonly_help'] = 'Self enrolment may be restricted to members of a specified cohort only. Note that changing this setting has no effect on existing enrolments.';
$string['confirmbulkapproveenrolment'] = 'Are you sure you want to approve these users enrolments?';
$string['confirmbulkdeclineenrolment'] = 'Are you sure you want to decline these users enrolments?';
$string['confirmbulkdeleteenrolment'] = 'Are you sure you want to delete these users enrolments?';
$string['customapplicationreceivedbody'] = 'Message body when application received';
$string['customapplicationreceivedsubject'] = 'Message subject when application received';
$string['customapplicationreceivedsubject_help'] = 'This message is sent to all users who have capability \'enrol/approval:notify\' in the course';
$string['customapprovalbody'] = 'Message body on approval';
$string['customapprovalsubject'] = 'Message subject on approval';
$string['customdeclinebody'] = 'Message body on decline';
$string['customdeclinesubject'] = 'Message subject on decline';
$string['custommessageexplained'] = '<p>Messages may be added as plain text or Moodle-auto format, including HTML tags and multi-lang tags.</p>
<p>The following placeholders may be included in the messages and subjects:</p>
<ul>
<li>Course name {$a->coursename}</li>
<li>Link to user\'s profile page {$a->profileurl}</li>
<li>User full name {$a->username}</li>
<li>Link to manage enrollments page {$a->manageurl}</li>
</ul>';
$string['customyouhaveappliedbody'] = 'Message body on application';
$string['customyouhaveappliedsubject'] = 'Message subject on application';
$string['decline'] = 'Decline enrolment';
$string['declineselectedusers'] = 'Decline selected users enrolments';
$string['declineusers'] = 'Decline enrolments';
$string['defaultrole'] = 'Default role assignment';
$string['defaultrole_desc'] = 'Select role which should be assigned to users during the enrolment';
$string['deleteselectedusers'] = 'Delete selected user enrolments';
$string['editselectedusers'] = 'Edit selected user enrolments';
$string['enrolenddate'] = 'End date';
$string['enrolenddate_help'] = 'If enabled, users can enrol themselves until this date only.';
$string['enrolenddaterror'] = 'Enrolment end date cannot be earlier than start date';
$string['enrolme'] = 'Enrol me';
$string['enrolmentisapproved'] = 'The enrolment of {$a} is already approved';
$string['enrolmentnotfound'] = 'Enrolment not found';
$string['enrolperiod'] = 'Enrolment duration';
$string['enrolperiod_desc'] = 'Default length of time that the enrolment is valid. If set to zero, the enrolment duration will be unlimited by default.';
$string['enrolperiod_help'] = 'Length of time that the enrolment is valid, starting with the moment the user requests enrolment. If disabled, the enrolment duration will be unlimited.';
$string['enrolstartdate'] = 'Start date';
$string['enrolstartdate_help'] = 'If enabled, users can enrol themselves from this date onward only.';
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
$string['maxenrolledreached'] = 'This course is full.';
$string['messageprovider:expiry_notification'] = 'Self enrolment with approval expiry notifications';
$string['messagestemplates'] = 'Messages templates';
$string['newenrols'] = 'Allow new enrolment requests';
$string['newenrols_help'] = 'This setting determines whether a user can enrol into this course.';
$string['noapplicableusers'] = 'Neither of selected users has the status "{$a}" required for this action';
$string['nousers'] = 'There are no user enrolments pending approval';
$string['partialapplicableusers'] = 'One or more of selected users do not have the status "{$a}" required for this action';
$string['participationactive'] = 'Approved';
$string['participationsuspended'] = 'Pending approval';
$string['pluginname'] = 'Self enrolment with approval';
$string['pluginname_desc'] = 'The self enrolment by approval plugin allows users to choose which courses they want to participate in. Approval of teacher/manager is requried.';
$string['requestenrolment'] = 'Request enrolment in this course.';
$string['role'] = 'Default assigned role';
$string['sendmessageonapplication'] = 'Send message on application';
$string['sendmessageonapplication_help'] = 'If enabled, users receive a message via email when they apply for enrolment in a course.';
$string['status'] = 'Enable existing enrolments';
$string['status_desc'] = 'Enable self enrolment by approval method in new courses.';
$string['status_help'] = 'If disabled all existing enrolments under this method are suspended and new users can not enrol.';
$string['templateapplicationreceivedbody'] = 'User {$a->username} has applied for enrollment in "{$a->coursename}"

You can approve or decline enrollment applications at:
{$a->manageurl}';
$string['templateapplicationreceivedsubject'] = 'User {$a->username} applied to {$a->coursename}';
$string['templateyouareapprovedbody'] = 'Congratulations, you are now enrolled in "{$a->coursename}"!

If you have not done so already, you should edit your profile page so that we can learn more about you:

  {$a->profileurl}';
$string['templateyouareapprovedsubject'] = 'Welcome to {$a->coursename}';
$string['templateyouaredeclinedbody'] = 'We are sorry to inform you that your application to enrol in "{$a->coursename}" was declined.';
$string['templateyouaredeclinedsubject'] = 'Your enrollment application was declined';
$string['templateyouhaveappliedbody'] = 'Thank you for applying to "{$a->coursename}"!

Your application will be considered and you will be notified about the enrollment shortly.';
$string['templateyouhaveappliedsubject'] = 'You have applied to {$a->coursename}';
$string['unenrol'] = 'Unenrol user';
$string['unenrolselfconfirm'] = 'Do you really want to unenrol yourself from course "{$a}"?';
$string['unenroluser'] = 'Do you really want to unenrol "{$a->user}" from course "{$a->course}"?';
$string['unenrolusers'] = 'Unenrol users';
$string['usedefault'] = 'Use default';
$string['viewallenrolled'] = 'View all enrolled users';