export default function ($http, appConfig) {

    function search(searchString) {
        return $http.get(`${appConfig.apiUrl}/search/${searchString}`)
        .then(function (response) {
            return response.data;
        });
    }

    return {
        search: search
    };
}