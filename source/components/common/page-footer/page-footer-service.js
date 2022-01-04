export default function ($http, appConfig) {
    function getFooter() {
        return $http.get(`${appConfig.apiUrl}/footer`)
        .then(function (response) {
            return response.data;
        });
    }

    function updateFooter(footer) {
        return $http.put(`${appConfig.apiUrl}/footer`, {footer})
        .then(function (response) {
            return response.data;
        });
    }

    return {
        getFooter: getFooter,
        updateFooter: updateFooter
    };
}