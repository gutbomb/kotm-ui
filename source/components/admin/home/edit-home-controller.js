import homeService from "../../home/home-service";

export default function ($interval, $scope, homeService, articleService, programService, pageService, adminService, $rootScope, $location) {
    let ehpc = this;

    ehpc.activeNews = 0;
    ehpc.mouseOnNews = false;
    ehpc.edited = {};

    ehpc.getHome = function () {
        pageService.getPage(`/home`).then( function(pageMeta) {
            $rootScope.meta = angular.copy(pageMeta);
            ehpc.edited.meta = {
                title: angular.copy(pageMeta.title),
                description: angular.copy(pageMeta.description),
                keywords: angular.copy(pageMeta.keywords)
            }
        });
        homeService.getHome().then( function(homeData) {
            ehpc.edited.emergencyHeadline = angular.copy(homeData.emergencyHeadline);
            ehpc.edited.emergencyText = angular.copy(homeData.emergencyText);
            ehpc.homeContent = angular.copy(homeData);
        });
    };

    ehpc.getPrograms = function () {
        programService.getPrograms()
        .then(function (data){
            ehpc.programs = data;
        });
    };
    
    ehpc.getArticles = function () {
        articleService.getNews()
        .then(function (data){
            ehpc.news = data;
        })
        .finally(function () {
            ehpc.news[0].active = true;
        });
    };

    ehpc.changeNews = (direction) => {
        ehpc.news[ehpc.activeNews].active = false;
        if (direction === 'next') {
            if (ehpc.activeNews === (ehpc.news.length - 1)) {
                ehpc.activeNews = 0;
            } else {
                ehpc.activeNews++;
            }
        } else if (direction === 'previous') {
            if (ehpc.activeNews === 0) {
                ehpc.activeNews = ehpc.news.length - 1;
            } else {
                ehpc.activeNews--;
            }
        } else {
            ehpc.activeNews = direction;
        }
        ehpc.news[ehpc.activeNews].active = true;
        return true;
    }

    $interval(() => {
        if (!ehpc.mouseOnNews) {
            ehpc.changeNews('next');
        }
    }, 10000);

    ehpc.saveEdit= (module) => {
        if (module === 'metaDescription') {
            ehpc.metaDescriptionOpen = false;
            ehpc.metaDescriptionChanged = true;
        } else if (module === 'metaTitle') {
            ehpc.metaTitleOpen = false;
            ehpc.metaTitleChanged = true;
        } else if (module === 'metaKeywords') {
            ehpc.metaKeywordsOpen = false;
            ehpc.metaKeywordsChanged = true;
        } else {
            ehpc[`${module}Open`] = false;
            ehpc[`${module}Changed`] = true;
        }
    };

    ehpc.cancelEdit = function (module) {
        if (module === 'metaDescription') {
            ehpc.edited.meta.description = angular.copy($rootScope.meta.description);
            ehpc.metaDescriptionOpen = false;
            ehpc.metaDescriptionChanged = false;
        } else if (module === 'metaTitle') {
            ehpc.edited.meta.title = angular.copy($rootScope.meta.title);
            ehpc.metaTitleOpen = false;
            ehpc.metaTitleChanged = false;
        } else if (module === 'metaKeywords') {
            ehpc.edited.meta.keywords = angular.copy($rootScope.meta.keywords);
            ehpc.metaKeywordsOpen = false;
            ehpc.metaKeywordsChanged = false;
        } else {
            ehpc.edited[module] = angular.copy(ehpc.homeContent[module]);
            ehpc[`${module}Open`] = false;
            ehpc[`${module}Changed`] = false;
        }
    };

    ehpc.init = function () {
        ehpc.getHome();
        ehpc.getArticles();
        ehpc.getPrograms();
        ehpc.metaDescriptionChanged = false;
        ehpc.metaTitleChanged = false;
        ehpc.metaKeywordsChanged = false;
        ehpc.metaDescriptionOpen = false;
        ehpc.metaTitleOpen = false;
        ehpc.metaKeywordsOpen = false;
        ehpc.emergencyHeadlineChanged = false;
        ehpc.emergencyHeadlineOpen = false;
        ehpc.emergencyTextChanged = false;
        ehpc.emergencyTextOpen = false;
        ehpc.ckeditorConfig = {
            bodyClass: 'section-text-container',
            contentsCss: '/css/styles.css',
            removePlugins: 'iframe,flash,smiley',
            removeButtons: 'Source,Styles',
            skin: 'moono',
            format_tags: 'p;h1;h2;h3',
            scayt_autoStartup: true,
            extraPlugins: 'youtube',
            removeDialogTabs: ''
        };
    };

    ehpc.submitEdit = () => {
        adminService.updateHome(ehpc.edited)
        .then(function () {
            ehpc.init();
            $location.path('/admin/edit-home');
        }, function (e) {
            if(e.data.error) {
                ehpc[`${e.data.module}Error`] = e.data.status;
                ehpc.displayErrorPopup = true;
            }
            console.error(e);
        });
    };

    ehpc.init();
}