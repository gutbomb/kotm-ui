export default function ($q, $http, $window, $rootScope, $location, appConfig) {
    function changePassword (oldPassword, newPassword) {
        return $http.put(
            appConfig.apiUrl + '/change-password',
            {
                newPassword: newPassword,
                password: oldPassword
            }
        )
        .then(function (response) {
            return response.data;
        }, function (error) {
            return $q.reject(error);
        });
    }

    function clearToken () {
        $window.localStorage.clear();
    }

    function getActiveUser () {
        return this.activeUser;
    }

    function getToken () {
        return $window.localStorage[appConfig.tokenName];
    }

    function getUser (userId) {
        return $http.get(appConfig.apiUrl + '/user/' + userId).then(function (response) {
            return response.data;
        });
    }

    function getUserId () {
        return parseToken(getToken()).id;
    }

    function isAdmin (userId) {
        return this.getUser(userId).then(function (user) {
            return user.role === 'admin';
        });
    }

    function isAuthenticated () {
        var token = this.getToken();

        return !!token;
    }

    function loginUser (username, password) {
        var defer = $q.defer();
        var self = this;
        $http({
            data: {username: username, password: password},
            headers: {'Content-Type': 'application/json'},
            method: 'POST',
            url: appConfig.apiUrl + '/auth'
        }).then(function (resp) {
            if (resp.data && resp.data.jwt) {
                self.activeUser = self.parseToken(resp.data.jwt).id;
            }
            defer.resolve(true);
        }, function (response) {
            if (response.status === 405) {
                defer.resolve(false);
            } else if (response.status === 304) {
                defer.resolve(true);
            } else {
                defer.resolve(false);
            }
        })
        .catch(function (error) {
            defer.reject(error);
        });

        return defer.promise;
    }

    function logoutUser () {
        clearToken();
        $rootScope.isLoggedIn = false;
        $location.path('/');
    }

    function parseToken (token) {
        var base64Url = token.split('.')[1];
        var base64 = base64Url.replace('-', '+').replace('_', '/');
        return JSON.parse($window.atob(base64));
    }

    function resetPassword (email) {
        return $http.put(`${appConfig.apiUrl}/reset-password/${email}`)
        .then(function (response) {
            return response.data[0];
        });
    }

    function saveToken (token) {
        $window.localStorage[appConfig.tokenName] = token;
    }

    return {
        changePassword: changePassword,
        clearToken: clearToken,
        getActiveUser: getActiveUser,
        getToken: getToken,
        getUser: getUser,
        getUserId: getUserId,
        isAdmin: isAdmin,
        isAuthenticated: isAuthenticated,
        loginUser: loginUser,
        logoutUser: logoutUser,
        parseToken: parseToken,
        resetPassword: resetPassword,
        saveToken: saveToken
    };
};