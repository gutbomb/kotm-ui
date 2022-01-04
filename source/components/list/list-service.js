export default function ($http, appConfig) {

    function getList(list) {
        return $http.get(`${appConfig.apiUrl}/list/${list}`)
        .then(function (response) {
            return response.data;
        });
    }

    function updateList(listUrl, list) {
        return $http.put(`${appConfig.apiUrl}/list/${listUrl}`, list)
        .then(function (response) {
            return response.data;
        });
    }

    function createList(list) {
        return $http.post(`${appConfig.apiUrl}/list`, list)
        .then(function (response) {
            return response.data;
        });
    }

    function getLists() {
        return $http.get(`${appConfig.apiUrl}/list`)
        .then(function (response) {
            return response.data;
        });
    }

    function deleteList(url, id, pageId) {
        return $http.delete(`${appConfig.apiUrl}/list/${url}/${id}/${pageId}`)
        .then(function (response) {
            return response.data[0];
        });
    }

    return {
        getList: getList,
        updateList: updateList,
        createList: createList,
        deleteList: deleteList,
        getLists: getLists
    };
}