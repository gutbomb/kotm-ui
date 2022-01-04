export default function ($http, appConfig) {

    function getHistory() {
        return $http.get(`${appConfig.apiUrl}/history`)
        .then(function (response) {
            return response.data;
        });
    }

    function updateHistory(obj) {
        return $http.put(`${appConfig.apiUrl}/history`, obj)
        .then(function (response) {
            return response.data[0];
        });
    }

    return {
        getHistory: getHistory,
        updateHistory: updateHistory
    };
}