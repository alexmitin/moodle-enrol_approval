@enrol @enrol_approval
Feature: Users are able to apply and teacher is able to bulk approve enrolments
  In order to participate in courses
  As a user
  I need to enrol myself in a course and get approval

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
      | student2 | Student | 2 | student2@asd.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "activities" exist:
      | activity   | name                   | intro                         | course | idnumber    |
      | label      | Test label name        | Test course contents          | C1     | label1      |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "admin"
    And I navigate to "Manage enrol plugins" node in "Site administration > Plugins > Enrolments"
    And I click on "Enable" "link" in the "Self enrolment with approval" "table_row"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I add "Self enrolment with approval" enrolment method with:
      | Custom instance name | Test student enrolment |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I press "Enrol me"
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I press "Enrol me"
    And I log out

  @javascript
  Scenario: Teacher approves several enrolments
    When I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Enrolment methods" node in "Course administration > Users"
    And I click on "Approve enrolments" "link" in the "Test student enrolment" "table_row"
    And I click on "input[type=checkbox]" "css_element" in the "Student 1" "table_row"
    And I click on "input[type=checkbox]" "css_element" in the "Student 2" "table_row"
    And I set the field "bulkuserop" to "Approve selected users enrolments"
    And I press "Go"
    And I press "Approve enrolments"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    Then I should see "Test course contents"
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I should see "Test course contents"

  @javascript
  Scenario: Teacher declines several enrolments
    When I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Enrolment methods" node in "Course administration > Users"
    And I click on "Approve enrolments" "link" in the "Test student enrolment" "table_row"
    And I click on "input[type=checkbox]" "css_element" in the "Student 1" "table_row"
    And I click on "input[type=checkbox]" "css_element" in the "Student 2" "table_row"
    And I set the field "bulkuserop" to "Decline selected users enrolments"
    And I press "Go"
    And I press "Decline enrolments"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    Then I should see "Enrol me"
    And I should not see "Test course contents"
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I should see "Enrol me"
    And I should not see "Test course contents"
