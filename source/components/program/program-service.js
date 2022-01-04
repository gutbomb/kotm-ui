export default function ($http, appConfig) {
    function getProgram(link) {
        return $http.get(`${appConfig.apiUrl}/program/${link}`)
        .then(function (response) {
            return response.data;
        });
    }

    function getLocations(programId) {
        return $http.get(`${appConfig.apiUrl}/program-locations/${programId}`)
        .then(function (response) {
            return response.data;
        });
    }

    function getFaqs(id) {
        return $http.get(`${appConfig.apiUrl}/faq/program/${id}`)
        .then(function (response) {
            return response.data;
        });
    }

    function getPrograms() {
        return $http.get(`${appConfig.apiUrl}/program`)
        .then(function (response) {
            return response.data;
        });
    }

    function updateProgram(id, obj) {
        return $http.put(`${appConfig.apiUrl}/program/${id}`, obj)
        .then(function (response) {
            return response.data[0];
        });
    }

    return {
        getProgram: getProgram,
        getPrograms: getPrograms,
        getLocations: getLocations,
        getFaqs: getFaqs,
        updateProgram: updateProgram
    };
}