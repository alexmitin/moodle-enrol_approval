@enrol @enrol_approval
Feature: User is able to apply and teacher with limited capabilities is able to approve enrolments
  In order to participate in courses
  As a user
  I need to enrol myself in a course and get approval

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "activities" exist:
      | activity   | name                   | intro                         | course | idnumber    |
      | label      | Test label name        | Test course contents          | C1     | label1      |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | teacher |
    And I log in as "admin"
    And I navigate to "Manage enrol plugins" node in "Site administration > Plugins > Enrolments"
    And I click on "Enable" "link" in the "Self enrolment with approval" "table_row"
    And I am on homepage
    And I follow "Course 1"
    And I add "Self enrolment with approval" enrolment method with:
      | Custom instance name | Test student enrolment |
    And I log out

  @javascript
  Scenario: Teacher with limited capabilities approves an enrolment of a student
    When I log in as "student1"
    And I follow "Course 1"
    And I press "Enrol me"
    Then I should not see "Test course contents"
    And I am on homepage
    And I follow "Course 1"
    And I should not see "Test course contents"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Approve enrolments" node in "Course administration > Users"
    And I click on "Approve" "link" in the "Student 1" "table_row"
    And I press "Approve enrolments"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I should see "Test course contents"

  @javascript
  Scenario: Teacher with limited capabilities declines an enrolment of a student
    When I log in as "student1"
    And I follow "Course 1"
    And I press "Enrol me"
    Then I should not see "Test course contents"
    And I am on homepage
    And I follow "Course 1"
    And I should not see "Test course contents"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Approve enrolments" node in "Course administration > Users"
    And I click on "Decline" "link" in the "Student 1" "table_row"
    And I press "Decline enrolments"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I should see "Enrol me"
    And I should not see "Test course contents"
