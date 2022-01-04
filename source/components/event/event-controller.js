export default function ($location, $routeParams, $window, eventService) {
    let ec = this;
    $window.scrollTo(0, 0);

    ec.getEvent = function () {
        eventService.getEvent($routeParams.eventUrl)
        .then(function (data){
            ec.event = data;
            if (ec.event.rsvp && $routeParams.rsvpStatus === 'rsvp') {
                ec.rsvpEnabled = true;
            } else {
                ec.rsvpEnabled = false;
            }
            if(ec.event.eventLocationId) {
                ec.buildGoogleLink();
            }
        }, function(e) {
            if (e.status === 404) {
                $location.path(`/error/event/${$routeParams.eventUrl}`);
            };
        });
    };

    ec.sendRsvp = function (status) {
        if (status === 0) {
            ec.rsvp.attendingNum = 0;
        }
        eventService.sendRsvp({
            rsvpData: ec.rsvp,
            rsvpId: ec.event.rsvpId,
            attending: status,
            eventId: ec.event.id,
            eventTitle: ec.event.title,
            eventDate: ec.event.start,
            rsvpEmail: ec.event.rsvpEmail
        })
        .then(function (data) {
            ec.rsvpComplete = true;
            if (status) {
                ec.attending = true;    
            }
        }, function (e) {
            console.error(e)
        });
    };

    ec.init = function () {
        ec.getEvent();
        ec.rsvp = {
            name: '',
            email: '',
            notes: '',
            attendingNum: "1"
        };
    };

    ec.buildGoogleLink = function () {
        ec.googleMapsUrl = `https://www.google.com/maps/embed/v1/search?key=AIzaSyAGuipQmBa3ryAUwLsZRhg-7ZhTIXqcqzU&q=${ec.event.street1.replace(new RegExp(' ', 'g'), '+')}+${ec.event.city.replace(new RegExp(' ', 'g'), '+')}+${ec.event.state}+${ec.event.zip}&zoom=14`;
    };

    ec.init();
}