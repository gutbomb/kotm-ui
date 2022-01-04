export default function ($scope, $routeParams, $window, articleService, $rootScope, $location, $timeout, heroImageService, adminService, pageService) {
    let eac = this;
    $window.scrollTo(0, 0);

    eac.init = function () {
        $timeout(function () {
            if (!$rootScope.isLoggedIn) {
                $rootScope.prevPage = '/admin';
                $location.path('/login');
            } else {
                if($rootScope.user.role === 'admin' || $rootScope.user.role === 'editor') {
                    $scope.Math = window.Math;
                    eac.getArticle();
                    eac.selectedRevision = 0;
                    eac.revisionDescription = false;
                    eac.revisionDescriptionOpen = false;
                    eac.newRevisionDescriptionOpen = false;
                    eac.stagedId = false;
                    eac.staged = false;
                    eac.stagedApproved = false;
                    eac.stagedFirstName = false;
                    eac.stagedLastName = false;
                    eac.descriptionOpen = false;
                    eac.descriptionChanged = false;
                    eac.urlOpen = false;
                    eac.urlChanged = false;
                    eac.urlError = null;
                    eac.textOpen = false;
                    eac.textChanged = false;
                    eac.imageOpen = false;
                    eac.imageChanged = false;
                    eac.titleOpen = false;
                    eac.titleChanged = false;
                    eac.newsChanged = false;
                    eac.displayGallery = false;
                    eac.displayErrorPopup = false;
                    eac.metaDescriptionOpen = false;
                    eac.metaDescriptionChanged = false;
                    eac.metaTitleOpen = false;
                    eac.metaTitleChanged = false;
                    eac.metaKeywordsOpen = false;
                    eac.metaKeywordsChanged = false;
                    eac.layoutChanged = false;
                    eac.layoutOpen = false;
                    eac.colorChanged = false;
                    eac.colorOpen = false;
                    eac.heroIdChanged = false;
                    eac.heroIdOpen = false;
                    eac.ckeditorConfig = {
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
                    $location.path(`/article/${$routeParams.url}`);
                }
            }
        }, 5);
    }

    eac.getArticle = function () {
        pageService.getPage(`/article/${$routeParams.url}`).then( function(pageMeta) {
            $rootScope.meta = pageMeta;
        })
        .then(() => {
            articleService.getArticle($routeParams.url)
            .then(function (data) {
                heroImageService.getHeroes()
                .then(function (data) {
                    eac.heroes = data;
                }, function (e) {
                    console.error('unable to fetch heroes');
                    console.error(e);
                });
                eac.article = angular.copy(data);
                if($rootScope.user.role === 'admin') {
                    eac.getRevisions();
                }
                adminService.getStaged('article', eac.article.id)
                .then(function (stagedData) {
                    eac.edited = angular.copy(stagedData.object);
                    eac.staged = true;
                    eac.stagedDate = stagedData.created;
                    eac.stagedId = stagedData.id;
                    eac.edited.meta.oldUrl = `/article/${$routeParams.url}`;
                    if (eac.edited.layout === 'page') {
                        eac.ckeditorConfig.bodyClass = `accent-${eac.edited.color} article-wrapper-content-text`;
                    } else {
                        eac.ckeditorConfig.bodyClass = `accent-${eac.edited.color} article-wrapper-content`;
                    }
                }, function () {
                    eac.edited = angular.copy(data);
                    eac.edited.meta = angular.copy($rootScope.meta);
                    eac.edited.meta.oldUrl = `/article/${$routeParams.url}`;
                    if (eac.edited.layout === 'page') {
                        eac.ckeditorConfig.bodyClass = `accent-${eac.edited.color} article-wrapper-content-text`;
                    } else {
                        eac.ckeditorConfig.bodyClass = `accent-${eac.edited.color} article-wrapper-content`;
                    }
                });
                
            }, function (error) {
                console.error(error);
            });
        });
    };

    eac.getRevisions = function () {
        adminService.getRevisions('article', eac.article.id)
        .then(function (data) {
            eac.revisions = data;
        })
        .then(function () {
            if ($routeParams.revision) {
                eac.selectedRevision = parseInt($routeParams.revision);
                eac.changeRevision();
            }
        });
    };

    eac.changeRevision = function () {
        if(eac.selectedRevision === 0) {
            eac.init();
        } else {
            adminService.getRevision(eac.selectedRevision)
            .then(function (data) {
                eac.staged = true;
                eac.edited = angular.copy(data.object);
                eac.stagedDate = data.created;
                eac.stagedId = data.id;
                eac.stagedApproved = data.approved;
                eac.stagedFirstName = data.firstname;
                eac.stagedLastName = data.lastname;
                eac.edited.meta.oldUrl = `/article/${$routeParams.url}`;
                eac.revisionDescription = data.description;
                if (eac.edited.layout === 'page') {
                    eac.ckeditorConfig.bodyClass = `accent-${eac.edited.color} article-wrapper-content-text`;
                } else {
                    eac.ckeditorConfig.bodyClass = `accent-${eac.edited.color} article-wrapper-content`;
                }
            });
        }
    }

    eac.cancelEdit = function (module) {
        if (module === 'metaDescription') {
            eac.edited.meta.description = angular.copy($rootScope.meta.description);
            eac.metaDescriptionOpen = false;
            eac.metaDescriptionChanged = false;
        } else if (module === 'metaTitle') {
            eac.edited.meta.title = angular.copy($rootScope.meta.title);
            eac.metaTitleOpen = false;
            eac.metaTitleChanged = false;
        } else if (module === 'metaKeywords') {
            eac.edited.meta.keywords = angular.copy($rootScope.meta.keywords);
            eac.metaKeywordsOpen = false;
            eac.metaKeywordsChanged = false;
        } else {
            eac.edited[module] = angular.copy(eac.article[module]);
            eac[`${module}Open`] = false;
        }
    };

    eac.saveEdit = function (module) {
        if(module === 'title') {
            eac.titleChanged = true;
            eac.titleOpen = false;
            eac.titleError = false;
            if(eac.edited.url === '') {

            }
        } else if (module === 'metaDescription') {
            eac.metaDescriptionOpen = false;
            eac.metaDescriptionChanged = true;
        } else if (module === 'metaTitle') {
            eac.metaTitleOpen = false;
            eac.metaTitleChanged = true;
        } else if (module === 'metaKeywords') {
            eac.metaKeywordsOpen = false;
            eac.metaKeywordsChanged = true;
        } else {
            eac[`${module}Changed`] = true;
            eac[`${module}Open`] = false;
            eac[`${module}Error`] = null;
        }
        
    };

    eac.revertEdit = function (module) {
        if (module === 'metaDescription') {
            eac.edited.meta.description = angular.copy($rootScope.meta.description);
            eac.metaDescriptionOpen = false;
            eac.metaDescriptionChanged = false;
        } else if (module === 'metaTitle') {
            eac.edited.meta.title = angular.copy($rootScope.meta.title);
            eac.metaTitleOpen = false;
            eac.metaTitleChanged = false;
        } else if (module === 'metaKeywords') {
            eac.edited.meta.keywords = angular.copy($rootScope.meta.keywords);
            eac.metaKeywordsOpen = false;
            eac.metaKeywordsChanged = false;
        } else {
            eac.edited[module] = angular.copy(eac.article[module]);
            eac[`${module}Changed`] = false;
            eac[`${module}Open`] = false;
            eac[`${module}Error`] = null;
        }
    };

    eac.submitEdit = function () {
        eac.newRevisionDescriptionOpen = false;
        if(eac.edited.layout === 'page') {
            if(eac.edited.heroId) {
                eac.edited.image = eac.heroes[eac.getHeroIndex(eac.edited.heroId)].items[0].image;
            } else {
                eac.edited.image = 'logo.png';
            }
        } else {
            if(!eac.edited.image) {
                eac.edited.image = 'logo.png';
            }
        }
        articleService.updateArticle(eac.edited.id, eac.edited)
        .then(function () {
            $location.path(`/admin/edit-article/${eac.edited.url}`);
            eac.init();
        }, function (e) {
            if(e.data.error) {
                eac[`${e.data.module}Error`] = e.data.status;
                eac.displayErrorPopup = true;
            }
            console.error(e);
        });
    };

    eac.removeStaged = function () {
        adminService.removeStaged(eac.stagedId)
        .then(function () {
            eac.init();
        });
    };

    eac.rejectRevision = function () {
        adminService.rejectRevision(eac.stagedId)
        .then(function () {
            eac.init();
        });
    };

    eac.approveRevision = function () {
        adminService.approveRevision(eac.stagedId)
        .then(function () {
            eac.submitEdit();
        });
    };

    eac.removeImage = function () {
        eac.edited.image = null;
        eac.imageChanged = true;
    };

    eac.selectImage = function (newImage) {
        eac.edited.image = newImage;
        eac.imageChanged = true;
        eac.imageOpen = false;
    };

    eac.openGallery = function () {
        eac.galleryPage = 0;
        adminService.getImages()
        .then(function (data) {
            eac.images = data;
        }, function (e) {
            console.error(e);
        })
        .then(function () {
            eac.imageOpen = true;
        });
    };

    eac.changeGalleryPage = function (direction) {
        if(direction === 'prev') {
            eac.galleryPage = eac.galleryPage - 30;
        } else {
            eac.galleryPage = eac.galleryPage + 30;
        }
    };

    eac.gallerySearch = function () {
        eac.galleryPage = 0;
    };

    eac.uploadImage = function (tabId, sectionId) {
        eac.fileError = false;
        if (eac.uploadFile.type === 'image/png' ||
            eac.uploadFile.type === 'image/gif' ||
            eac.uploadFile.type === 'image/jpeg') {
            adminService.uploadImage(eac.uploadFile)
            .then((r) => {
                eac.selectImage(r.newFilename)
            }, (e) => {
                console.error(e);
                eac.fileError = 'Sorry, there was some sort of error uploading the image.';
            });
        } else {
            console.error('file type not allowed');
            eac.fileError = 'Sorry, only .jpg, .jpeg, .gif, or .png files are allowed.';
        }
    };

    eac.encodeUrl = function () {
        eac.edited.url = encodeURI(eac.edited.url.replace(/ /g, '-').toLowerCase());
    };

    eac.selectColor = function (color) {
        eac.edited.color = color;
        eac.colorChanged = true;
        eac.colorOpen = false;
        if (eac.edited.layout === 'page') {
            eac.ckeditorConfig.bodyClass = `accent-${eac.edited.color} article-wrapper-content-text`;
        } else {
            eac.ckeditorConfig.bodyClass = `accent-${eac.edited.color} article-wrapper-content`;
        }
    };

    eac.selectLayout = function (layout) {
        eac.edited.layout = layout;
        eac.layoutChanged = true;
        eac.layoutOpen = false;
        if (eac.edited.layout === 'page') {
            eac.ckeditorConfig.bodyClass = `accent-${eac.edited.color} article-wrapper-content-text`;
        } else {
            eac.ckeditorConfig.bodyClass = `accent-${eac.edited.color} article-wrapper-content`;
        }
    };

    eac.selectHero = function (heroId) {
        eac.edited.heroId = heroId;
        eac.heroIdChanged = true;
        eac.heroIdOpen = false;
    };

    eac.getHeroIndex = (id) => {
        for (let i=0; i < (eac.heroes.length); i++) {
            if(eac.heroes[i].id === id) {
                return i;
            }
        }
    };

    eac.init();
}