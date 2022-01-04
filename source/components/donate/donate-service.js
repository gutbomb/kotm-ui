export default function ($http, appConfig) {

    function getDonateContent() {
        return $http.get(`${appConfig.apiUrl}/donate`)
        .then(function (response) {
            return response.data;
        });
    }

    function updateDonateContent(obj) {
        return $http.put(`${appConfig.apiUrl}/donate`, obj)
        .then(function (response) {
            return response.data;
        });
    }

    return {
        getDonateContent: getDonateContent,
        updateDonateContent: updateDonateContent
    };
}