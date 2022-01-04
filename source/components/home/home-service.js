export default function ($http, appConfig) {
    function getHome() {
        return $http.get(`${appConfig.apiUrl}/home`)
        .then(function (response) {
            return response.data;
        });
    }

    return {
        getHome: getHome,
    };
}