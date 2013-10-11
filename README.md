Auto Group Assignments
======================

Introduction
------------

This CiviCRM extension was written during the Dalesbridge Sprint Code, after the CiviCON London 2013.

This extension wan't, at the beggining, intended to be published. It was created only with pedagogical purposes. But, as long as it works and can be useful to others -even if it's only as an "inspiration" to create other nice CiviCRM extensions-, we decided to give it a change.

Please, issue any bug or suggestion: https://github.com/amnesty/autogroupassignments/issues

More info about the event, can be found at: https://civicrm.org/civicrm/event/info?reset=1&id=322

What does this extension do?
----------------------------

Auto Group Assignments allows you to specify a default group for each contact created (by members of another specified group).

Lets say that you have, for instance, three groups in CiviCRM: administrators, teachers and students. The administrators, normally, use CiviCRM to create teacher records. The teachers use to create student records. With this extension, you'll be able to assign the "Default Group for New Contacts" when you create, or modify a group.

![Group Form](https://github.com/amnesty/autogroupassignments/raw/master/img/group-form-screenshot.png "Group Edition Form")

So, for Administrators, you'll choose Teachers and for Teachers you'll choose Students. From this time on, when a administrator creates a contact, by default, it will be assigned, by default, to he teachers group.

![New Contact](https://github.com/amnesty/autogroupassignments/raw/master/img/new-contact-screenshot.png "New Contact Form")

What else can it do?
--------------------

You can specify if the automated group assignments will be triggered when the contacts are created via API, or not. The logic is the same as the explained before:

* Assign the CiviCRM API User to a group (for instance, teachers)
* Set the Default Group for New Contacts to the desired one (for instance, students)
* Check the Affects API Calls

From now on, it your CiviCRM API User creates a contact, the will be automatically assigned to the students group.

How to install it?
------------------

In general, before trying to install manually any CiviCRM extension:

* Open your CiviCRM instance
* Go to Administer > System Settings > Manage Extensions
* Look there for your extension

If it's there, you only need to click Install. :-D

If it's not there:

* Download this extension from: https://github.com/amnesty/autogroupassignments/archive/master.zip
* Unzip it into your extensions folder
* Got to Administer > System Settings > Manage Extensions
* Install it and activate it

Where's my extensions folder?
-----------------------------

If you don't know that's your CiviCRM extensions folder:

* Go to Administer > System Settings > Directories
* Look at the value of CiviCRM Extensions Directory

If it's empty, ask your CiviCRM administrator, or go to the CiviCRM Docs.
