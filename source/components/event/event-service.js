export default function ($http, appConfig) {
    function getEvent(eventUrl) {
        return $http.get(`${appConfig.apiUrl}/event/${eventUrl}`)
        .then(function (response) {
            return response.data;
        });
    }

    function sendRsvp(rsvpObj) {
        return $http.post(`${appConfig.apiUrl}/rsvp/${rsvpObj.eventId}`, rsvpObj)
        .then(function (response) {
            let emptyResults = [];
            if(response.data.length) {
                return response.data;
            } else {
                return emptyResults;
            }
        });
    }

    function getEvents(year, month) {
        let calendar = [];
        let prevCategory = -1;
        return $http.get(`${appConfig.apiUrl}/events/month/${year}/${month}`)
        .then(function (response) {
            let newCategory = {};
            for (let i = 0; i < response.data.length; i++) {
                if(prevCategory !== response.data[i].programId) {
                    prevCategory = response.data[i].programId;
                    newCategory = {
                        name: response.data[i].programName,
                        programId: response.data[i].programId,
                        events: []
                    };
                    switch(response.data[i].color) {
                        case 'green':
                            newCategory.color = '#85c259';
                            break;

                        case 'teal':
                            newCategory.color = '#06aea8';
                            break;

                        case 'blue':
                            newCategory.color = '#9fd3f2';
                            break;

                        case 'orange':
                            newCategory.color = '#fcb040';
                            break;

                        case 'purple':
                            newCategory.color = '#aa9bbf';
                            break;

                        case 'red':
                            newCategory.color = '#d61c23';
                            break;

                        default:
                            newCategory.color = '#727272';
                            newCategory.name = 'General';
                            newCategory.programId = 99;
                    }

                }
                let newEvent = {
                    id: response.data[i].id,
                    title: response.data[i].title,
                    start: response.data[i].start,
                    bgClass: `bg-${response.data[i].color}`,
                    city: `${response.data[i].city}, ${response.data[i].state}`,
                    description: response.data[i].description,
                    linkUrl: response.data[i].url
                };
                if (!response.data[i].color) {
                    newEvent.bgClass = 'bg-gray';
                }
                if(response.data[i].allDay) {
                    newEvent.className = 'all-day-event';
                    newEvent.allDay = true;
                } else {
                    newEvent.className = 'timed-event';
                    newEvent.allDay = false;
                }
                newCategory.events.push(newEvent);
                if(i !== response.data.length - 1) {
                    if(response.data[i+1].programId !== prevCategory) {
                        calendar.push(newCategory);
                    }
                } else {
                    calendar.push(newCategory);
                }
            }
            return {
                events: calendar,
                eventsList: response.data
            };
        });
    }

    function getSearch(searchTerm) {
        return $http.get(`${appConfig.apiUrl}/events/search/${searchTerm}`)
        .then(function (response) {
            let emptyResults = [];
            if(response.data.length) {
                return response.data;
            } else {
                return emptyResults;
            }
        });
    }

    return {
        getEvent: getEvent,
        getEvents: getEvents,
        getSearch: getSearch,
        sendRsvp: sendRsvp
    };
}