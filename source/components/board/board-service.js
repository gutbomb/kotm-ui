export default function ($http, appConfig) {
    function getBoard(link) {
        return $http.get(`${appConfig.apiUrl}/board`)
        .then(function (response) {
            return response.data;
        });
    }

    function updateBoard(obj) {
        return $http.put(`${appConfig.apiUrl}/board`, obj)
        .then(function (response) {
            return response.data[0];
        });
    }

    return {
        getBoard: getBoard,
        updateBoard: updateBoard
    };
}