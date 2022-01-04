export default function ($routeParams, $window, articleService, $rootScope, $timeout) {
    let ac = this;
    $window.scrollTo(0, 0);

    articleService.getArticle($routeParams.url)
    .then(function (data) {
        ac.article = data;
    });

    ac.init = function () {
        ac.editButton = false;
        $timeout(function () {
            if ($rootScope.isLoggedIn) {
                if($rootScope.user.role === 'admin') {
                    ac.editButton = true;
                }
            }
        }, 5);
    };

    ac.init();
}