export default function ($http, appConfig) {
    function getPage(link) {
        return $http.post(`${appConfig.apiUrl}/page`, {pageUrl: link})
        .then(function (response) {
            return response.data;
        });
    }

    function getPages() {
        return $http.get(`${appConfig.apiUrl}/page`)
        .then(function (response) {
            return response.data;
        });
    }

    return {
        getPage: getPage,
        getPages: getPages
    };
}