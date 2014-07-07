# Improved Appointment Booking Calendar

## Disclaimer

This is code is provided as-is, use-at-your-own-risk. I have not had a chance to
go through all of it for security, and when I received it, (see "Origins of this
project" below), there were plenty of places that I didn't feel were secure.

## Origins of this project

In the spring of 2013 I was working on a project that used CodePeople.net's 
Appointment Booking Calendar, ( http://wordpress.dwbooster.com/calendars/appointment-booking-calendar ). When I got into the code I found a lot of places
where the code should be improved, so I started improving it.

At the time reached out to the original authors of the plugin to see if they 
wanted my improvements. They didn't, so I'm open-sourcing my work.

## Future Expections

I'm not working on this improved appointment booking plugin at the moment. If I
do start working on it again I will post updates here. I will also review &
accept, (or reject), pull requests whenever I have a chance.

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