export default function ($routeParams, $window, articleService, $location) {
    let ac = this;
    $window.scrollTo(0, 0);

    ac.init = function () {
        articleService.getArticle($routeParams.url)
        .then(function (data) {
            ac.article = data;
        }, function(e) {
            if (e.status === 404) {
                $location.path(`/error/article/${$routeParams.url}`);
            };
        });
    };

    ac.init();
}