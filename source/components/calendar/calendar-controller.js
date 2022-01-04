export default function ($window, $document, $scope, $timeout, eventService) {
    let cc = this;
    $window.scrollTo(0, 0);
    cc.activeStyle = 'month';
    cc.selectedCalendar = 'all';
    cc.eventSources = [];
    cc.displayEvent = false;
    cc.displayedEvent = {};
    cc.activeView = 'list';
    cc.noEvents = true;
    cc.noSearch = true;
    cc.activeFilter = false;
    cc.searchActive = false;
    cc.activeSearchString = '';

    const $calendar = $('[ui-calendar]');

    cc.changeActiveView = function (view) {
        cc.activeView = view;
        if(view = 'calendar') {
            cc.clearSearch();
        }
    }

    cc.getSearch = function () {
        cc.noSearch = true;
        cc.searchActive = true;
        cc.activeSearchString = cc.searchString;
        eventService.getSearch(cc.searchString)
        .then(function (data){
            cc.searchList = data;
            if(data.length) {
                cc.noSearch = false;
            }
        });
    }

    cc.clearSearch = function () {
        cc.searchActive = false;
        cc.activeSearchString = '';
        cc.searchString = '';
        cc.noSearch = true;
    }

    cc.changeView = function(view) {
        $calendar.fullCalendar('changeView', view);
        cc.activeStyle = view;
        if(view === 'agendaWeek') {
            cc.changeDayFormatting();
        }
    }

    cc.changeDayFormatting = function () {
        $('.fc-day-header span').each(function() {
            let oldString = $(this).html();
            let newString = oldString.replace('#', '</span>');
            newString = newString.replace('$', '<span class="bigday">');
            $(this).html(newString);
        });
    }

    cc.changeRange = function(range) {
        let prevRange = cc.activeRange;
        $calendar.fullCalendar(range);
        if (prevRange !== moment($calendar.fullCalendar('getDate')).format('MMMM Y')) {
            cc.getEvents(moment($calendar.fullCalendar('getDate')).format('Y'), moment($calendar.fullCalendar('getDate')).format('M'));
        }
        cc.activeRange = moment($calendar.fullCalendar('getDate')).format('MMMM Y');
        if(cc.activeStyle === 'agendaWeek') {
            cc.changeDayFormatting();
        }
    }

    cc.showEvent = function (date, event) {
        cc.displayEvent = true;
        cc.displayedEvent = date;
        let offset = event.offset();
        $timeout(function() {
            $('#selected-event').css({
                left: offset.left - ($('#selected-event').outerWidth()/3),
                top: (offset.top - $('#selected-event').outerHeight()) -20
            });
            $(document).bind('click', function(event){
                var isClickedElementChildOfPopup = $('#selected-event')
                    .find(event.target)
                    .length > 0;

                if (!isClickedElementChildOfPopup) {
                    $timeout(function() {
                        cc.hideEvent();
                    }, 5);
                }
            });
        }, 5)
    };

    cc.hideEvent = function () {
        if(cc.displayEvent) {
            $(document).unbind('click');
            cc.displayEvent = false;
            cc.displayedEvent = {};
        }
    };

    cc.uiConfig = {
        calendar:{
            timeFormat: 'h:mm a',
            height: 'auto',
            editable: false,
            defaultView: 'month',
            header: false,
            views: {
                month: {
                    columnHeaderFormat: 'dddd'
                },
                week: {
                    columnHeaderFormat: '[$]D[#] ddd[.]'
                },
                day: {
                    columnHeaderFormat: ' dddd MMMM D'
                }
            },
            eventClick: function(date, jsEvent, view) {
                cc.showEvent(date, $(this));
            }
        }
    };
    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

    cc.randomInt = (max) => {
        return Math.floor(Math.random() * Math.floor(max));
    }

    cc.activeRange = moment($calendar.fullCalendar('getDate')).format('MMMM Y');

    cc.getEvents = function (year, month) {
        eventService.getEvents(year,month)
        .then(function (data){
            if(data) {
                cc.events = data.events;
                cc.eventsList = data.eventsList;
                for (var i = 0; i < cc.eventsList.length; i++) {
                    if (!cc.eventsList[i].programId) {
                        cc.eventsList[i].programId = 99;
                        cc.eventsList[i].programName = 'General';
                        cc.eventsList[i].color = 'gray';
                    }
                }
                cc.noEvents = false;
            } else {
                cc.events = [];
                cc.eventsList = [];
                cc.noEvents = true;
            }
        }, function () {
            cc.events = [];
            cc.eventsList = [];
            cc.noEvents = true;
        })
        .then(function () {
            if(cc.events) {
                $calendar.fullCalendar('removeEvents');
                cc.displayAllCalendars();
            }
        });
    };

    cc.getEvents(moment().format('Y'), moment().format('M'));
    
    cc.displayAllCalendars = function () {
        cc.events.forEach((item) => {
            cc.eventSources.push(item);
        });
    };

    cc.changeCalendar = function () {
        cc.eventSources.splice(0, cc.eventSources.length);
        if(cc.selectedCalendar === 'all') {
            cc.activeFilter = false;
            cc.displayAllCalendars();
        } else {
            cc.activeFilter = true;
            cc.eventSources.push(cc.events[cc.selectedCalendar]);
        }
    };
}