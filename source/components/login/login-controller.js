export default function (loginService, $rootScope, $location) {
    let lc = this;
    if ($rootScope.isLoggedIn) {
        $location.path('/admin');
    }

    lc.inputEmail = '';
    lc.inputPassword = '';

    lc.login = function () {
        loginService.loginUser(lc.inputUsername, lc.inputPassword).then(function (data) {
            if (data) {
                $rootScope.isLoggedIn = true;
                if($rootScope.prevPage) {
                    $location.path($rootScope.prevPage);
                    $rootScope.prevPage = null;
                } else {
                    $location.path('/admin');
                }
            } else {
                alert('login failed');
            }
        })
        .catch(function (error) {
            alert('An error occurred.\n' + error);
        });
    };
};