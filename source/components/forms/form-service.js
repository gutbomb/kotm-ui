export default function ($http, appConfig) {
    function submitForm(obj) {
        return $http.post(`${appConfig.apiUrl}/form/${obj.url}`, obj)
        .then(function (response) {
            return response.data;
        });
    }

    function getForm(url) {
        return $http.get(`${appConfig.apiUrl}/form/${url}`)
        .then(function (response) {
            return response.data;
        });
    }

    function uploadFile(file) {
        var fd = new FormData();
        fd.append('file', file);
        return $http.post(`${appConfig.apiUrl}/upload/form`, fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .then(function (response) {
            return response.data;
        });
    }

    function updateForm(url, obj) {
        return $http.put(`${appConfig.apiUrl}/form/${url}`, obj)
        .then(function (response) {
            return response.data[0];
        });
    }

    function createForm(obj) {
        return $http.post(`${appConfig.apiUrl}/form`, obj)
        .then(function (response) {
            return response.data[0];
        });
    }

    return {
        submitForm: submitForm,
        getForm: getForm,
        uploadFile: uploadFile,
        updateForm: updateForm,
        createForm: createForm
    };
}