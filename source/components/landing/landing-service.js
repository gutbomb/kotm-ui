export default function ($http, appConfig) {
    function getLanding(link) {
        return $http.get(`${appConfig.apiUrl}/landing/${link}`)
        .then(function (response) {
            return response.data;
        });
    }

    function getFaqs(id) {
        return $http.get(`${appConfig.apiUrl}/faq/landing/${id}`)
        .then(function (response) {
            return response.data;
        });
    }

    function updateLanding(id, obj) {
        return $http.put(`${appConfig.apiUrl}/landing/${id}`, obj)
        .then(function (response) {
            return response.data[0];
        });
    }

    return {
        getLanding: getLanding,
        updateLanding: updateLanding,
        getFaqs: getFaqs
    };
}