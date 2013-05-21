# Improved Appointment Booking Calendar

## Requirements

* WordPress, (something recent)
* PHP 5.2 (for some DateTime functions)



# Current To-Do list
### (This is by no means complete, just what I've noticed and don't want to forget)


* Test that the widget, (CPABC_App_Widget), still works
* Add some basic styling to the customer-facing appointment booking form.
* Update Help link to no longer point at dwbooster.com, (only needed once plugin is sufficiently different from dwbooster.com's version)
* Properly close form elements in cpabc_scheduler
* Improve JS form validation in cpabc_scheduler, (match E-mail better, for one)
* Add some login or password or something to the iCal feed


# Future Improvements

* Show a warning when an admin blocks off/restricts a date that already has appointments booked.
* Stop relying on a CP_CALENDAR_ID constant so that we can do operations on multiple calendars in the future.