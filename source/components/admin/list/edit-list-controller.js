export default function (listService, $routeParams, pageService, $rootScope, heroImageService, $location, $timeout) {
    let elc = this;

    elc.init = function () {
        pageService.getPage(`/list/${$routeParams.listUrl}`).then( function(pageMeta) {
            $rootScope.meta = pageMeta;
        })
        listService.getList($routeParams.listUrl)
        .then(function (results) {
            elc.list = angular.copy(results);
            elc.edited = angular.copy(results);
            elc.edited.meta = angular.copy($rootScope.meta);
            elc.listChanged = false;
            elc.heroIdChanged = false;
            elc.heroIdOpen = false;
            elc.colorOpen = false;
            elc.colorChanged = false;
            elc.titleOpen = false;
            elc.titleChanged = false;
            elc.descriptionOpen = false;
            elc.descriptionChanged = false;
            elc.urlOpen = false;
            elc.urlChanged = false;
            elc.selectedAddPage = -1;
            elc.deletePageIndex = -1;
            elc.metaTitleChanged = false;
            elc.metaTitleOpen = false;
            elc.metaDescriptionChanged = false;
            elc.metaDescriptionOpen = false;
            elc.metaKeywordsChanged = false;
            elc.metaKeywordsOpen = false;
            elc.responseError = '';
            elc.responseErrorPopup = false;
            elc.ckeditorConfig = {
                bodyClass: 'section-text-container',
                contentsCss: '/css/styles.css',
                removePlugins: 'iframe,flash,smiley',
                removeButtons: 'Source,Styles',
                allowedContent: true,
                skin: 'moono',
                format_tags: 'p;h1;h2;h3',
                scayt_autoStartup: true,
                extraPlugins: 'youtube',
                removeDialogTabs: ''
            };
        });
        pageService.getPages()
        .then(function (results) {
            elc.pages = results;
        });
    };

    elc.openHeroGallery = function () {
        heroImageService.getHeroes()
        .then(function (data) {
            elc.heroes = data;
        }, function (e) {
            console.error('unable to fetch heroes');
            console.error(e);
        });
        elc.heroIdOpen = true;
    };

    elc.selectHero = function (heroId) {
        elc.edited.heroId = heroId;
        elc.heroIdChanged = true;
        elc.heroIdOpen = false;
    };

    elc.selectColor = function (color) {
        elc.edited.color = color;
        elc.colorChanged = true;
        elc.colorOpen = false;
    };

    elc.selectNewPage = function () {
        let newPageOrder = -1;
        for (let i=0; i < (elc.edited.items.length); i++) {
            if(elc.edited.items[i].pageOrder > newPageOrder) {
                newPageOrder = elc.edited.items[i].pageOrder;
            }
        }
        newPageOrder++;
        elc.edited.items.push(elc.pages[elc.selectedAddPage]);
        let pageIndex = elc.getPageIndex(elc.pages[elc.selectedAddPage].id);
        elc.edited.items[pageIndex].newPage = true;
        elc.edited.items[pageIndex].pageOrder = newPageOrder;
        elc.addPageOpen = false;
        elc.listChanged = true;
        elc.selectedAddPage = -1;
    };

    elc.getPageIndex = (id) => {
        for (let i=0; i < (elc.edited.items.length); i++) {
            if(elc.edited.items[i].id === id) {
                return i;
            }
        }
    };

    elc.deletePage = () => {
        if (elc.edited.items[elc.deletePageIndex].newPage) {
            elc.edited.items.splice(elc.deletePageIndex, 1);
        } else {
            elc.edited.items[elc.deletePageIndex].deleted = true;
        }
        elc.listChanged = true;
        elc.deletePageIndex = -1
        elc.deletePagePopup = false;
    };

    elc.cancelDeletePage = () => {
        elc.deletePagePopup = false;
        elc.deletePageIndex = -1;
    };

    elc.deletePageWarning = (id) => {
        elc.deletePagePopup = true;
        elc.deletePageIndex = elc.getPageIndex(id);
    };

    elc.revertEdit = (module) => {
        if(module === 'items') {
            elc.edited.items = angular.copy(elc.list.items);
            elc.listChanged = false;
        } else if (module === 'metaDescription') {
            elc.edited.meta.description = angular.copy($rootScope.meta.description);
        } else if (module === 'metaTitle') {
            elc.edited.meta.title = angular.copy($rootScope.meta.title);
        } else if (module === 'metaKeywords') {
            elc.edited.meta.keywords = angular.copy($rootScope.meta.keywords);
        } else {
            elc.edited[module] = angular.copy(elc.list[module]);
            elc[`${module}Changed`] = false;
            elc[`${module}Open`] = false;
            elc[`${module}Error`] = null;
        }
    }

    elc.saveEdit= (module) => {
        if (module === 'metaDescription') {
            elc.metaDescriptionOpen = false;
            elc.metaDescriptionChanged = true;
        } else if (module === 'metaTitle') {
            elc.metaTitleOpen = false;
            elc.metaTitleChanged = true;
        } else if (module === 'metaKeywords') {
            elc.metaKeywordsOpen = false;
            elc.metaKeywordsChanged = true;
        } else {
            elc[`${module}Changed`] = true;
            elc[`${module}Open`] = false;
            elc[`${module}Error`] = null;
        }
    };

    elc.cancelEdit = function (module) {
        if (module === 'metaDescription') {
            elc.metaDescriptionOpen = false;
            elc.metaDescriptionChanged = false;
            elc.edited.meta.description = angular.copy($rootScope.meta.description);
        } else if (module === 'metaTitle') {
            elc.metaTitleOpen = false;
            elc.metaTitleChanged = false;
            elc.edited.meta.title = angular.copy($rootScope.meta.title);
        } else if (module === 'metaKeywords') {
            elc.metaKeywordsOpen = false;
            elc.metaKeywordsChanged = false;
            elc.edited.meta.keywords = angular.copy($rootScope.meta.keywords);
        } else {
            elc.edited[module] = angular.copy(elc.list[module]);
            elc[`${module}Open`] = false;
        }
    };

    elc.moveItem = function (direction, originalPageOrder, pageId) {
        let swapPageId = -1;
        elc.edited.items.forEach(function (page) {
            if(direction === 'down') {
                if(page.pageOrder === (originalPageOrder + 1)) {
                    swapPageId = page.id;
                }
            } else {
                if(page.pageOrder === (originalPageOrder - 1)) {
                    swapPageId = page.id;
                }
            }
        });
        for (let i=0; i < (elc.edited.items.length); i++) {
            if(direction === 'down') {
                if(elc.edited.items[i].id === pageId) {
                    elc.edited.items[i].pageOrder++;
                }
                if(elc.edited.items[i].id === swapPageId) {
                    elc.edited.items[i].pageOrder--;
                }
            } else {
                if(elc.edited.items[i].id === pageId) {
                    elc.edited.items[i].pageOrder--;
                }
                if(elc.edited.items[i].id === swapPageId) {
                    elc.edited.items[i].pageOrder++;
                }
            }
        }
        elc.listChanged = true;
    };

    elc.submitEdit = () => {
        listService.updateList($routeParams.listUrl, elc.edited)
        .then(function () {
            $location.path(`/admin/edit-list/${elc.edited.url}`);
            $timeout(function () {
                elc.init();
            }, 100);
        }, function (e) {
            if(e.data.error) {
                elc[`${e.data.module}Error`] = e.data.status;
                elc.responseError = e.data.status;
                elc.responseErrorPopup = true;
            }
            console.error(e);
        });
    };

    elc.encodeUrl = function () {
        elc.edited.url = encodeURI(elc.edited.url.replace(/ /g, '-').toLowerCase());
    }

    elc.init();
};