const app = angular.module('kotm2020UiApp', ['ngRoute', 'ngLocationUpdate', 'ngSanitize', 'angularMoment', 'moment-picker', 'ngAnimate', 'angular-stripe', 'ng.ckeditor', 'ngclipboard', 'ui.calendar', 'kotm2020Ui.config']);

//--------------------------------------------------------/
// Declarations
//--------------------------------------------------------/

// About Us
import aboutUsController from '../components/about-us/about-us-controller';
app.controller('aboutUsController', aboutUsController);

import aboutUsService from '../components/about-us/about-us-service';
app.factory('aboutUsService', aboutUsService);

import editAboutUsController from '../components/admin/about-us/edit-about-us-controller';
app.controller('editAboutUsController', editAboutUsController);

// Admin
import adminController from '../components/admin/admin-controller';
app.controller('adminController', adminController);

import adminService from '../components/admin/admin-service';
app.factory('adminService', adminService);


// Article
import articleController from '../components/article/article-controller';
app.controller('articleController', articleController);

import articleService from '../components/article/article-service';
app.factory('articleService', articleService);

import createArticleController from '../components/admin/article/create-article-controller';
app.controller('createArticleController', createArticleController);

import editArticleController from '../components/admin/article/edit-article-controller';
app.controller('editArticleController', editArticleController);

// Board
import boardController from '../components/board/board-controller';
app.controller('boardController', boardController);

import editBoardController from '../components/admin/board/edit-board-controller';
app.controller('editBoardController', editBoardController);

import boardService from '../components/board/board-service';
app.factory('boardService', boardService);

// Contact
import contactController from '../components/contact/contact-controller';
app.controller('contactController', contactController);

import contactService from '../components/contact/contact-service';
app.factory('contactService', contactService);

// Donate
import donateController from '../components/donate/donate-controller';
app.controller('donateController', donateController);

import editDonateController from '../components/admin/donate/edit-donate-controller';
app.controller('editDonateController', editDonateController);

import donateService from '../components/donate/donate-service';
app.factory('donateService', donateService);

// Error
import errorController from '../components/error/error-controller';
app.controller('errorController', errorController);

// Events
import calendarController from '../components/calendar/calendar-controller';
app.controller('calendarController', calendarController);

import eventController from '../components/event/event-controller';
app.controller('eventController', eventController);

import eventService from '../components/event/event-service';
app.factory('eventService', eventService);

// Forms
import formController from '../components/forms/form-controller';
app.controller('formController', formController);

import editFormController from '../components/admin/form/edit-form-controller';
app.controller('editFormController', editFormController);

import createFormController from '../components/admin/form/create-form-controller';
app.controller('createFormController', createFormController);

import formService from '../components/forms/form-service';
app.factory('formService', formService);

import formUpload from '../components/forms/form-upload-directive';
app.directive('formUpload', formUpload);

// Hero Image
import heroImage from '../components/common/hero-image/hero-image-directive';
app.directive('heroImage', heroImage);

import heroImageController from '../components/common/hero-image/hero-image-controller';
app.controller('heroImageController', heroImageController);

import heroImageService from '../components/common/hero-image/hero-image-service';
app.factory('heroImageService', heroImageService);

import createHeroImageController from '../components/admin/hero-image/create-hero-image-controller';
app.controller('createHeroImageController', createHeroImageController);

import editHeroImageController from '../components/admin/hero-image/edit-hero-image-controller';
app.controller('editHeroImageController', editHeroImageController);

// Home
import homeController from '../components/home/home-controller';
app.controller('homeController', homeController);

import editHomeController from '../components/admin/home/edit-home-controller';
app.controller('editHomeController', editHomeController);

import homeService from '../components/home/home-service';
app.factory('homeService', homeService);

// Landing
import landingController from '../components/landing/landing-controller';
app.controller('landingController', landingController);

import landingService from '../components/landing/landing-service';
app.factory('landingService', landingService);

import editLandingController from '../components/admin/landing/edit-landing-controller';
app.controller('editLandingController', editLandingController);

// List
import listController from '../components/list/list-controller';
app.controller('listController', listController);

import listService from '../components/list/list-service';
app.factory('listService', listService);

import createListController from '../components/admin/list/create-list-controller';
app.controller('createListController', createListController);

import editListController from '../components/admin/list/edit-list-controller';
app.controller('editListController', editListController);

// Location
import locationController from '../components/location/location-controller';
app.controller('locationController', locationController);

import locationService from '../components/location/location-service';
app.factory('locationService', locationService);

// Login
import authenticationInterceptor from '../components/login/authentication-interceptor-service';
app.factory('authenticationInterceptor', authenticationInterceptor);

import changePasswordController from '../components/admin/change-password/change-password-controller';
app.controller('changePasswordController', changePasswordController);

import loginController from '../components/login/login-controller';
app.controller('loginController', loginController);

import loginService from '../components/login/login-service';
app.factory('loginService', loginService);

// Media
import mediaController from '../components/media/media-controller';
app.controller('mediaController', mediaController);

import mediaService from '../components/media/media-service';
app.factory('mediaService', mediaService);

import mediaUpload from '../components/media/media-upload-directive';
app.directive('mediaUpload', mediaUpload);

// My KOTM
// import myKotmController from '../components/my-kotm/my-kotm-controller';
// app.controller('myKotmController', myKotmController);

// Page
import pageService from '../components/common/page/page-service';
app.factory('pageService', pageService);

// Page Footer
import pageFooter from '../components/common/page-footer/page-footer-directive';
app.directive('pageFooter', pageFooter);

import pageFooterController from '../components/common/page-footer/page-footer-controller';
app.controller('pageFooterController', pageFooterController);

import pageFooterService from '../components/common/page-footer/page-footer-service';
app.factory('pageFooterService', pageFooterService);

// Page Header
import pageHeader from '../components/common/page-header/page-header-directive';
app.directive('pageHeader', pageHeader);

import pageHeaderController from '../components/common/page-header/page-header-controller';
app.controller('pageHeaderController', pageHeaderController);

import pageHeaderService from '../components/common/page-header/page-header-service';
app.factory('pageHeaderService', pageHeaderService);

// Programs
import programController from '../components/program/program-controller';
app.controller('programController', programController);

import programService from '../components/program/program-service';
app.factory('programService', programService);

import editProgramController from '../components/admin/program/edit-program-controller';
app.controller('editProgramController', editProgramController);

// Reset Password
import resetPasswordController from '../components/reset-password/reset-password-controller';
app.controller('resetPasswordController', resetPasswordController);

// Search
import searchController from '../components/search/search-controller';
app.controller('searchController', searchController);

import searchService from '../components/search/search-service';
app.factory('searchService', searchService);

// Volunteer Portal
import volunteerController from '../components/volunteer/volunteer-controller';
app.controller('volunteerController', volunteerController);

//--------------------------------------------------------/
// Routes
//--------------------------------------------------------/

app.config(function ($routeProvider, $locationProvider) {
    $locationProvider.html5Mode(true);
    $routeProvider
        .when('/about', {
            templateUrl: 'components/about-us/about-us-template.html',
            controller: 'aboutUsController',
            controllerAs: 'auc',
            activeTab: 'about'
        })
        .when('/about/board', {
            templateUrl: 'components/board/board-template.html',
            controller: 'boardController',
            controllerAs: 'bc',
            activeTab: 'about'
        })
        .when('/admin', {
            templateUrl: 'components/admin/admin-template.html',
            controller: 'adminController',
            controllerAs: 'ac',
            activeTab: 'admin'
        })
        .when('/admin/edit-about', {
            templateUrl: 'components/admin/about-us/edit-about-us-template.html',
            controller: 'editAboutUsController',
            controllerAs: 'eauc',
            activeTab: 'admin'
        })
        .when('/admin/change-password', {
            templateUrl: 'components/admin/change-password/change-password-template.html',
            controller: 'changePasswordController',
            controllerAs: 'cpc',
            activeTab: 'admin'
        })
        .when('/admin/create-article', {
            templateUrl: 'components/admin/article/create-article-template.html',
            controller: 'createArticleController',
            controllerAs: 'cac',
            activeTab: 'admin'
        })
        .when('/admin/create-carousel', {
            templateUrl: 'components/admin/hero-image/create-hero-image-template.html',
            controller: 'createHeroImageController',
            controllerAs: 'chc',
            activeTab: 'admin'
        })
        .when('/admin/create-form', {
            templateUrl: 'components/admin/form/create-form-template.html',
            controller: 'createFormController',
            controllerAs: 'cfc',
            activeTab: 'admin'
        })
        .when('/admin/create-list', {
            templateUrl: 'components/admin/list/create-list-template.html',
            controller: 'createListController',
            controllerAs: 'clc',
            activeTab: 'admin'
        })
        .when('/admin/edit-article/:url', {
            templateUrl: 'components/admin/article/edit-article-template.html',
            controller: 'editArticleController',
            controllerAs: 'eac',
            activeTab: 'admin'
        })
        .when('/admin/edit-article/:url/:revision', {
            templateUrl: 'components/admin/article/edit-article-template.html',
            controller: 'editArticleController',
            controllerAs: 'eac',
            activeTab: 'admin'
        })
        .when('/admin/edit-board', {
            templateUrl: 'components/admin/board/edit-board-template.html',
            controller: 'editBoardController',
            controllerAs: 'ebc',
            activeTab: 'admin'
        })
        .when('/admin/edit-donate', {
            templateUrl: 'components/admin/donate/edit-donate-template.html',
            controller: 'editDonateController',
            controllerAs: 'edc',
            activeTab: 'admin'
        })
        .when('/admin/edit-form/:formUrl', {
            templateUrl: 'components/admin/form/edit-form-template.html',
            controller: 'editFormController',
            controllerAs: 'efc',
            activeTab: 'admin'
        })
        .when('/admin/edit-hero/:heroId', {
            templateUrl: 'components/admin/hero-image/edit-hero-image-template.html',
            controller: 'editHeroImageController',
            controllerAs: 'ehc',
            activeTab: 'admin'
        })
        .when('/admin/edit-home', {
            templateUrl: 'components/admin/home/edit-home-template.html',
            controller: 'editHomeController',
            controllerAs: 'ehpc',
            activeTab: 'admin'
        })
        .when('/admin/edit-landing/:link', {
            templateUrl: 'components/admin/landing/edit-landing-template.html',
            controller: 'editLandingController',
            controllerAs: 'elc',
            activeTab: 'admin'
        })
        .when('/admin/edit-list/:listUrl', {
            templateUrl: 'components/admin/list/edit-list-template.html',
            controller: 'editListController',
            controllerAs: 'elc',
            activeTab: 'admin'
        })
        .when('/admin/edit-program/:program', {
            templateUrl: 'components/admin/program/edit-program-template.html',
            controller: 'editProgramController',
            controllerAs: 'epc',
            activeTab: 'admin'
        })
        .when('/admin/edit-program/:program/:revision', {
            templateUrl: 'components/admin/program/edit-program-template.html',
            controller: 'editProgramController',
            controllerAs: 'epc',
            activeTab: 'admin'
        })
        .when('/article/:url', {
            templateUrl: 'components/article/article-template.html',
            controller: 'articleController',
            controllerAs: 'ac',
            activeTab: 'none'
        })
        .when('/contact', {
            templateUrl: 'components/contact/contact-template.html',
            controller: 'contactController',
            controllerAs: 'cc',
            activeTab: 'about'
        })
        .when('/donate', {
            templateUrl: 'components/donate/donate-template.html',
            controller: 'donateController',
            controllerAs: 'dc',
            activeTab: 'give'
        })
        .when('/event/:eventUrl', {
            templateUrl: 'components/event/event-template.html',
            controller: 'eventController',
            controllerAs: 'ec',
            activeTab: 'events'
        })
        .when('/event/:eventUrl/:rsvpStatus', {
            templateUrl: 'components/event/event-template.html',
            controller: 'eventController',
            controllerAs: 'ec',
            activeTab: 'events'
        })
        .when('/events', {
            templateUrl: 'components/calendar/calendar-template.html',
            controller: 'calendarController',
            controllerAs: 'cc',
            activeTab: 'events'
        })
        .when('/form/:formUrl', {
            templateUrl: 'components/forms/form-template.html',
            controller: 'formController',
            controllerAs: 'fc',
            activeTab: 'programs'
        })
        .when('/give', {
            templateUrl: 'components/landing/landing-template.html',
            controller: 'landingController',
            controllerAs: 'lc',
            activeTab: 'give'
        })
        .when('/home', {
            templateUrl: 'components/home/home-template.html',
            controller: 'homeController',
            controllerAs: 'hc',
            activeTab: 'home'
        })
        .when('/list/:listUrl', {
            templateUrl: 'components/list/list-template.html',
            controller: 'listController',
            controllerAs: 'lic',
            activeTab: 'home'
        })
        .when('/location/:slug', {
            templateUrl: 'components/location/location-template.html',
            controller: 'locationController',
            controllerAs: 'loc',
            activeTab: 'events'
        })
        .when('/login', {
            templateUrl: 'components/login/login-template.html',
            controller: 'loginController',
            controllerAs: 'lc',
            activeTab: 'none'
        })
        .when('/media', {
            templateUrl: 'components/media/media-template.html',
            controller: 'mediaController',
            controllerAs: 'mc',
            activeTab: 'none'
        })
        // .when('/my-kotm', {
        //     templateUrl: 'components/my-kotm/my-kotm-template.html',
        //     controller: 'myKotmController',
        //     controllerAs: 'mkc',
        //     activeTab: 'none'
        // })
        .when('/programs', {
            redirectTo: '/home'
        })
        .when('/programs/:program', {
            templateUrl: 'components/program/program-template.html',
            controller: 'programController',
            controllerAs: 'pc',
            activeTab: 'programs'
        })
        .when('/programs/:program/:tabSlug', {
            templateUrl: 'components/program/program-template.html',
            controller: 'programController',
            controllerAs: 'pc',
            activeTab: 'programs'
        })
        .when('/reset-password', {
            templateUrl: 'components/reset-password/reset-password-template.html',
            controller: 'resetPasswordController',
            controllerAs: 'rpc',
            activeTab: 'admin'
        })
        .when('/search', {
            templateUrl: 'components/search/search-template.html',
            controller: 'searchController',
            controllerAs: 'sc',
            activeTab: 'home'
        })
        .when('/volunteer', {
            redirectTo: '/volunteer-portal'
        })
        .when('/volunteers', {
            redirectTo: '/volunteer-portal'
        })
        .when('/volunteer.portal', {
            redirectTo: '/volunteer-portal'
        })
        .when('/volunteer-portal', {
            templateUrl: 'components/volunteer/volunteer-template.html',
            controller: 'volunteerController',
            controllerAs: 'vc',
            activeTab: 'give'
        })
        .when('/error/:type/:page', {
            templateUrl: 'components/error/error-template.html',
            controller: 'errorController',
            controllerAs: 'ec',
            activeTab: 'home'
        })
        .when('/', {
            redirectTo: '/home'
        })
        .otherwise({
            templateUrl: 'components/error/error-template.html',
            controller: 'errorController',
            controllerAs: 'ec',
            activeTab: 'home'
        });
});

//--------------------------------------------------------
// Config
//--------------------------------------------------------

app.config(function($httpProvider) {
    $httpProvider.interceptors.push('authenticationInterceptor');
});

app.config(function($sceProvider) {
    $sceProvider.enabled(false);
});

app.config(function (stripeProvider, appConfig) {
    stripeProvider.setPublishableKey(appConfig.stripeApiKey);
});

app.config(function (momentPickerProvider) {
    momentPickerProvider.options({
        hoursFormat: 'h A'
    });
});

app.filter ('stripHTML', [function () {
    return function (stringWithHtml) {
        var strippedString = $('<div/>').html(stringWithHtml).text();
        return strippedString;
    };
}]);

app.run(function ($rootScope, $location, loginService, $route, pageService, $window, $timeout) {
    var dataLayer = $window.dataLayer = $window.dataLayer || [];
    $rootScope.$on('$routeChangeStart', function () {
        $rootScope.isLoggedIn = loginService.isAuthenticated();
        if ($rootScope.isLoggedIn === true) {
            $rootScope.userId = loginService.getUserId();
            loginService.getUser($rootScope.userId).then(function(user) {
                $rootScope.user=user[0];
                if(!$rootScope.user.validated) {
                    loginService.clearToken();
                    $rootScope.isLoggedIn = false;
                    $location.path('/admin/not-validated');
                }
                if($rootScope.user.passwordMustChange) {
                    $location.path('/admin/change-password');
                }
            });
        } else {
            if($location.path().includes('admin')) {
                if ($location.path().includes('sign-up') === false && $location.path().includes('reset-password') === false && $location.path().includes('validate') === false) {
                    $location.path('/login');
                }
            }
        }
    });
    $rootScope.$on('$routeChangeSuccess', function () {
        $rootScope.activeTab = $route.current.activeTab;
        $rootScope.displayMobileMenu = false;
        if ($rootScope.menu) {
            $rootScope.menu.forEach(function (menu) {
                menu.open = false;
            });
        }
    });
    $rootScope.$on('$locationChangeSuccess', function () {
        if(!$location.path().includes('admin')) {
            pageService.getPage($location.path())
                .then( function(pageMeta) {
                    $rootScope.meta = pageMeta;
                })
                .then(function () {
                    $timeout(function () {
                        if($location.path().includes('/programs/') && $rootScope.metaTab) {
                            dataLayer.push({
                                event: 'ngRouteChange',
                                attributes: {
                                    route: $location.path(),
                                    title: `Kids on the Move${$rootScope.metaTab.title ? ' - ' + $rootScope.metaTab.title : ''}`
                                }
                            });
                        } else {
                            dataLayer.push({
                                event: 'ngRouteChange',
                                attributes: {
                                    route: $location.path(),
                                    title: `Kids on the Move${$rootScope.meta.title ? ' - ' + $rootScope.meta.title : ''}`
                                }
                            });
                        }
                    }, 2000);
                });
        }
    });
});