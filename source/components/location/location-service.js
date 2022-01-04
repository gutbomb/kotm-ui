export default function ($http, appConfig) {
    function getLocation(slug) {
        return $http.get(`${appConfig.apiUrl}/location/${slug}`)
        .then(function (response) {
            return response.data;
        });
    }

    function getLocations() {
        return $http.get(`${appConfig.apiUrl}/location`)
        .then(function (response) {
            return response.data;
        });
    }

    return {
        getLocation: getLocation,
        getLocations: getLocations
    };
}