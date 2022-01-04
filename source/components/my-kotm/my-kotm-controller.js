export default function ($rootScope, $location) {
    let mkc = this;
    if (!$rootScope.isLoggedIn) {
        $rootScope.prevPage = '/my-kotm';
        $location.path('/login');
    } else {
        mkc.user = $rootScope.user;
    }
    mkc.notifications = [
        {
            name: 'inbox',
            number: 3
        },{
            name: 'billing',
            number: 1
        },{
            name: 'schedule',
            number: 4
        },{
            name: 'account',
            number: 7
        }
    ];
    mkc.totalNotifications = 0; 
    mkc.notifications.forEach(function (notificationCount) {
        mkc.totalNotifications = mkc.totalNotifications + notificationCount.number;
    });
};