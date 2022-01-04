export default function ($location, listService, $routeParams) {
    let lic = this;

    lic.init = function () {
        listService.getList($routeParams.listUrl)
        .then(function (results) {
            lic.list = results;
        }, function(e) {
            if (e.status === 404) {
                $location.path(`/error/list/${$routeParams.listUrl}`);
            };
        });
    };

    lic.init();
};