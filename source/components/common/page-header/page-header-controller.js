export default function (pageHeaderService, $rootScope, $location) {
    let phc = this;

    pageHeaderService.getMenu()
    .then((menu) => {
        $rootScope.menu = menu;
    });

    phc.search = function () {

        $location.url(`/search?s=${phc.searchString}`);
        phc.searchString = '';
    }
}