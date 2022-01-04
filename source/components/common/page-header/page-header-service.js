export default function ($http, appConfig) {
    function getMenu() {
        return $http.get(`${appConfig.apiUrl}/menu`)
        .then(function (response) {
            return response.data;
        });
    }

    function updateMenu(menu) {
        return $http.put(`${appConfig.apiUrl}/menu`, {menu})
        .then(function (response) {
            return response.data;
        });
    }

    return {
        getMenu: getMenu,
        updateMenu: updateMenu
    };
}