export default function (searchService, $location) {
    let sc = this;
    
    

    sc.init = function () {
        if ($location.search().s) {
            sc.searchString = $location.search().s
            sc.displayedSearchString = $location.search().s
            sc.search();
        } else {
            sc.searchString = '';
        }
    };
    
    sc.search = function () {
        searchService.search(sc.searchString)
        .then(function (results) {
            sc.searchResults = results;
        });
    };

    sc.doSearch = function () {
        $location.url(`/search?s=${sc.searchString}`);
    };

    sc.closeSearch = function () {
        sc.searchString = '';
        sc.searchResults = false;
    };

    sc.init();
};