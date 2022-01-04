import { each } from "jquery";

export default function ($routeParams, $window, articleService, $rootScope, $location, $timeout, adminService, heroImageService) {
    let cac = this;
    $window.scrollTo(0, 0);

    cac.init = function () {
        $timeout(function () {
            if (!$rootScope.isLoggedIn) {
                $rootScope.prevPage = '/admin';
                $location.path('/login');
            } else {
                if($rootScope.user.role === 'admin') {
                    heroImageService.getHeroes()
                    .then(function (data) {
                        cac.heroes = data;
                    }, function (e) {
                        console.error('unable to fetch heroes');
                        console.error(e);
                    });
                    cac.article = {
                        title: '',
                        description: '',
                        text: '',
                        url: '',
                        image: null,
                        posted: new Date,
                        news: 0,
                        layout: 'article',
                        color: 'green',
                        heroId: null,
                        meta: {
                            title: '',
                            description: '',
                            keywords:''
                        }
                    };
                    cac.descriptionOpen = false;
                    cac.descriptionChanged = false;
                    cac.urlOpen = false;
                    cac.urlChanged = false;
                    cac.textOpen = false;
                    cac.textChanged = false;
                    cac.imageOpen = false;
                    cac.imageChanged = false;
                    cac.titleOpen = false;
                    cac.titleChanged = false;
                    cac.newsChanged = false;
                    cac.displayGallery = false;
                    cac.urlError = null;
                    cac.displayErrorPopup = false;
                    cac.metaDescriptionOpen = false;
                    cac.metaDescriptionChanged = false;
                    cac.metaTitleOpen = false;
                    cac.metaTitleChanged = false;
                    cac.metaKeywordsOpen = false;
                    cac.metaKeywordsChanged = false;
                    cac.ckeditorConfig = {
                        bodyClass: 'article-wrapper-content',
                        contentsCss: '/css/styles.css',
                        removePlugins: 'iframe,flash,smiley',
                        removeButtons: 'Source,Styles',
                        skin: 'moono',
                        format_tags: 'p;h1;h2;h3',
                        scayt_autoStartup: true,
                        extraPlugins: 'youtube',
                        removeDialogTabs: ''
                    };
                } else {
                    $location.path(`/home`);
                }
            }
        }, 5);
    }

    cac.cancelEdit = function (module) {
        if (module === 'metaDescription') {
            cac.article.meta.description = '';
            cac.metaDescriptionOpen = false;
            cac.metaDescriptionChanged = false;
        } else if (module === 'metaTitle') {
            cac.article.meta.title = '';
            cac.metaTitleOpen = false;
            cac.metaTitleChanged = false;
        } else if (module === 'metaKeywords') {
            cac.article.meta.keywords = '';
            cac.metaKeywordsOpen = false;
            cac.metaKeywordsChanged = false;
        } else {
            cac[`${module}Open`] = false;
        }
    };

    cac.saveEdit = function (module) {
        if (module === 'metaDescription') {
            cac.metaDescriptionOpen = false;
            cac.metaDescriptionChanged = true;
        } else if (module === 'metaTitle') {
            cac.metaTitleOpen = false;
            cac.metaTitleChanged = true;
        } else if (module === 'metaKeywords') {
            cac.metaKeywordsOpen = false;
            cac.metaKeywordsChanged = true;
        } else {
            cac[`${module}Changed`] = true;
            cac[`${module}Open`] = false;
            cac[`${module}Error`] = null;
        }
    };

    cac.submitArticle = function () {
        if(cac.article.layout === 'page') {
            if(cac.article.heroId) {
                cac.article.image = cac.heroes[cac.getHeroIndex(cac.article.heroId)].items[0].image;
            } else {
                cac.article.image = 'logo.png';
            }
        } else {
            if(!cac.article.image) {
                cac.article.image = 'logo.png';
            }
        }
        articleService.createArticle(cac.article)
        .then(function () {
            $location.path(`/admin/edit-article/${cac.article.url}`);
        }, function (e) {
            if(e.data.error) {
                cac[`${e.data.module}Error`] = e.data.status;
                cac.displayErrorPopup = true;
            }
            console.error(e);
        });
    }

    cac.removeImage = function () {
        cac.article.image = null;
        cac.imageChanged = true;
    };

    cac.selectImage = function (newImage) {
        cac.article.image = newImage;
        cac.imageChanged = true;
        cac.imageOpen = false;
    }

    cac.openGallery = function () {
        adminService.getImages()
        .then(function (data) {
            cac.images = data;
        }, function (e) {
            console.error(e);
        })
        .then(function () {
            cac.imageOpen = true;
        });
    }

    cac.updateUrl = function () {
        cac.article.url = encodeURI(cac.article.title.replace(/ /g, '-').toLowerCase());
        cac.article.meta.title = cac.article.title;
        cac.urlChanged = true;
        cac.metaTitleChanged = true;
    }

    cac.encodeUrl = function () {
        cac.article.url = encodeURI(cac.article.url.replace(/ /g, '-').toLowerCase());
    }

    cac.selectColor = function (color) {
        cac.article.color = color;
        cac.colorChanged = true;
        cac.colorOpen = false;
        if (cac.article.layout === 'page') {
            cac.ckeditorConfig.bodyClass = `accent-${cac.article.color} article-wrapper-content-text`;
        } else {
            cac.ckeditorConfig.bodyClass = `accent-${cac.article.color} article-wrapper-content`;
        }
    };

    cac.selectLayout = function (layout) {
        cac.article.layout = layout;
        cac.layoutChanged = true;
        cac.layoutOpen = false;
        if (cac.article.layout === 'page') {
            cac.ckeditorConfig.bodyClass = `accent-${cac.article.color} article-wrapper-content-text`;
        } else {
            cac.ckeditorConfig.bodyClass = `accent-${cac.article.color} article-wrapper-content`;
        }
    };

    cac.selectHero = function (heroId) {
        cac.article.heroId = heroId;
        cac.heroIdChanged = true;
        cac.heroIdOpen = false;
    };

    cac.getHeroIndex = (id) => {
        for (let i=0; i < (cac.heroes.length); i++) {
            if(cac.heroes[i].id === id) {
                return i;
            }
        }
    };

    cac.init();
}