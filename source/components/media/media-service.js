export default function ($http, appConfig) {

    function getMedia() {
        return $http.get(`${appConfig.apiUrl}/media`)
        .then(function (response) {
            return response.data;
        });
    }

    function uploadMediaFile(file) {
        var fd = new FormData();
        fd.append('file', file);
        return $http.post(`${appConfig.apiUrl}/upload/media`, fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .then(function (response) {
            return response.data;
        });
    }

    function updateMedia(obj) {
        return $http.put(`${appConfig.apiUrl}/media`, obj)
        .then(function (response) {
            return response.data;
        });
    }

    return {
        getMedia: getMedia,
        uploadMediaFile: uploadMediaFile,
        updateMedia: updateMedia
    };
}