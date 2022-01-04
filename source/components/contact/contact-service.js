export default function ($http, appConfig) {

    function submitContact(obj) {
        return $http.post(`${appConfig.apiUrl}/contact`, obj)
        .then(function (response) {
            return response.data;
        });
    }

    return {
        submitContact: submitContact
    };
}