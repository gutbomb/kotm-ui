export default function ($routeParams, $window, $location) {
    let ec = this;
    
    var dataLayer = $window.dataLayer = $window.dataLayer || [];
            
    if($routeParams.type && $routeParams.page) {
        dataLayer.push({
            event: 'fake404',
            attributes: {
                errorUrl: `/${$routeParams.type}/${$routeParams.page}`
            }
        });
    } else {
        dataLayer.push({
            event: 'fake404',
            attributes: {
                errorUrl: $location.path()
            }
        });
    }
};