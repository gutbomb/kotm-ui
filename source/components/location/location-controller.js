export default function ($location, $routeParams, $window, locationService) {
    let loc = this;
    $window.scrollTo(0, 0);

    loc.getLocation = function () {
        locationService.getLocation($routeParams.slug)
        .then(function (data){
            loc.location = data[0];
            loc.buildGoogleLink();
        }, function(e) {
            if (e.status === 404) {
                $location.path(`/error/location/${$routeParams.slug}`);
            };
        });
    };

    loc.init = function () {
        loc.getLocation();
    };

    loc.buildGoogleLink = function () {
        loc.googleMapsUrl = `https://www.google.com/maps/embed/v1/search?key=AIzaSyAGuipQmBa3ryAUwLsZRhg-7ZhTIXqcqzU&q=${loc.location.street1.replace(new RegExp(' ', 'g'), '+')}+${loc.location.city.replace(new RegExp(' ', 'g'), '+')}+${loc.location.state}+${loc.location.zip}&zoom=14`;
    };

    loc.init();
}