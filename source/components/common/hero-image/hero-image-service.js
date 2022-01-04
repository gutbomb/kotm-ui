export default function ($http, appConfig) {
    function getHero(id) {
        return $http.get(`${appConfig.apiUrl}/hero/${id}`)
        .then(function (response) {
            return response.data;
        });
    }

    function getHeroes() {
        return $http.get(`${appConfig.apiUrl}/hero`)
        .then(function (response) {
            return response.data;
        });
    }

    function updateHero(id, obj) {
        return $http.put(`${appConfig.apiUrl}/hero/${id}`, obj)
        .then(function (response) {
            return response.data[0];
        });
    }

    function createHero(obj) {
        return $http.post(`${appConfig.apiUrl}/hero`, obj)
        .then(function (response) {
            return response.data[0];
        });
    }

    return {
        getHero: getHero,
        getHeroes: getHeroes,
        updateHero: updateHero,
        createHero: createHero
    };
}