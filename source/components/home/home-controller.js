export default function ($interval, $scope, articleService, programService, homeService) {
    let hc = this;

    hc.activeNews = 0;
    hc.mouseOnNews = false;

    hc.getPrograms = function () {
        programService.getPrograms()
        .then(function (data){
            hc.programs = data;
        });
    };
    
    hc.getPrograms();
    
    hc.getArticles = function () {
        articleService.getNews()
        .then(function (data){
            hc.news = data;
        })
        .finally(function () {
            hc.news[0].active = true;
        });
    };

    hc.getArticles();

    hc.getHomeContent = function () {
        homeService.getHome()
        .then(function (data){
            hc.homeContent = data;
        });
    };

    hc.getHomeContent();

    hc.changeNews = (direction) => {
        hc.news[hc.activeNews].active = false;
        if (direction === 'next') {
            if (hc.activeNews === (hc.news.length - 1)) {
                hc.activeNews = 0;
            } else {
                hc.activeNews++;
            }
        } else if (direction === 'previous') {
            if (hc.activeNews === 0) {
                hc.activeNews = hc.news.length - 1;
            } else {
                hc.activeNews--;
            }
        } else {
            hc.activeNews = direction;
        }
        hc.news[hc.activeNews].active = true;
        return true;
    }

    $interval(() => {
        if (!hc.mouseOnNews) {
            hc.changeNews('next');
        }
    }, 10000);
}