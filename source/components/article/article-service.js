export default function ($http, appConfig) {
    function getArticles() {
        return $http.get(`${appConfig.apiUrl}/articles`)
        .then(function (response) {
            return response.data;
        }, function (e) {
            return e.data;
        });
    }

    function getNews() {
        return $http.get(`${appConfig.apiUrl}/news`)
        .then(function (response) {
            return response.data;
        });
    }

    function getArticle(url) {
        return $http.get(`${appConfig.apiUrl}/article/${url}`)
        .then(function (response) {
            return response.data[0];
        });
    }

    function updateArticle(id, obj) {
        return $http.put(`${appConfig.apiUrl}/article/${id}`, obj)
        .then(function (response) {
            return response.data[0];
        });
    }

    function createArticle(obj) {
        return $http.post(`${appConfig.apiUrl}/article`, obj)
        .then(function (response) {
            return response.data[0];
        });
    }

    function deleteArticle(url, id) {
        return $http.delete(`${appConfig.apiUrl}/article/${url}/${id}`)
        .then(function (response) {
            return response.data[0];
        });
    }

    return {
        getArticles: getArticles,
        getNews: getNews,
        getArticle: getArticle,
        createArticle: createArticle,
        updateArticle: updateArticle,
        deleteArticle: deleteArticle
    };
}