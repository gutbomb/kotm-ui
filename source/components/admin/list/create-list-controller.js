export default function (listService, $routeParams, pageService, $rootScope, heroImageService, $location, $timeout) {
    let clc = this;

    clc.init = function () {
        pageService.getPages()
        .then(function (results) {
            clc.pages = results;
        });
        clc.list = {
            heroId: false,
            title: '',
            description: '',
            color: 'green',
            items: [],
            url: '',
            meta: {
                title: '',
                keywords: '',
                description: '',
            }
        };
        clc.listChanged = false;
        clc.heroIdChanged = false;
        clc.heroIdOpen = false;
        clc.colorOpen = false;
        clc.colorChanged = false;
        clc.titleOpen = false;
        clc.titleChanged = false;
        clc.descriptionOpen = false;
        clc.descriptionChanged = false;
        clc.urlOpen = false;
        clc.urlChanged = false;
        clc.selectedAddPage = -1;
        clc.deletePageIndex = -1;
        clc.metaTitleChanged = false;
        clc.metaTitleOpen = false;
        clc.metaDescriptionChanged = false;
        clc.metaDescriptionOpen = false;
        clc.metaKeywordsChanged = false;
        clc.metaKeywordsOpen = false;
        clc.ckeditorConfig = {
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
    };

    clc.openHeroGallery = function () {
        heroImageService.getHeroes()
        .then(function (data) {
            clc.heroes = data;
        }, function (e) {
            console.error('unable to fetch heroes');
            console.error(e);
        });
        clc.heroIdOpen = true;
};

    clc.selectHero = function (heroId) {
        clc.list.heroId = heroId;
        clc.heroIdChanged = true;
        clc.heroIdOpen = false;
    };

    clc.selectColor = function (color) {
        clc.list.color = color;
        clc.colorChanged = true;
        clc.colorOpen = false;
    };

    clc.selectNewPage = function () {
        let newPageOrder = -1;
        for (let i=0; i < (clc.list.items.length); i++) {
            if(clc.list.items[i].pageOrder > newPageOrder) {
                newPageOrder = clc.list.items[i].pageOrder;
            }
        }
        newPageOrder++;
        clc.list.items.push(clc.pages[clc.selectedAddPage]);
        let pageIndex = clc.getPageIndex(clc.pages[clc.selectedAddPage].id);
        clc.list.items[pageIndex].pageOrder = newPageOrder;
        clc.addPageOpen = false;
        clc.listChanged = true;
        clc.selectedAddPage = -1;
    };

    clc.getPageIndex = (id) => {
        for (let i=0; i < (clc.list.items.length); i++) {
            if(clc.list.items[i].id === id) {
                return i;
            }
        }
    };

    clc.deletePage = () => {
        clc.list.items.splice(clc.deletePageIndex, 1);
        clc.listChanged = true;
        clc.deletePageIndex = -1
        clc.deletePagePopup = false;
    };

    clc.cancelDeletePage = () => {
        clc.deletePagePopup = false;
        clc.deletePageIndex = -1;
    };

    clc.deletePageWarning = (id) => {
        clc.deletePagePopup = true;
        clc.deletePageIndex = clc.getPageIndex(id);
    };

    clc.revertEdit = (module) => {
        if(module === 'items') {
            clc.list.items = [];
            clc.listChanged = false;
        } else if (module === 'metaDescription') {
            clc.list.meta.description = '';
        } else if (module === 'metaTitle') {
            clc.list.meta.title = '';
        } else if (module === 'metaKeywords') {
            clc.list.meta.keywords = '';
        } else {
            clc.list[module] = '';
            clc[`${module}Changed`] = false;
            clc[`${module}Open`] = false;
            clc[`${module}Error`] = null;
        }
    }

    clc.saveEdit= (module) => {
        if (module === 'metaDescription') {
            clc.metaDescriptionOpen = false;
            clc.metaDescriptionChanged = true;
        } else if (module === 'metaTitle') {
            clc.metaTitleOpen = false;
            clc.metaTitleChanged = true;
        } else if (module === 'metaKeywords') {
            clc.metaKeywordsOpen = false;
            clc.metaKeywordsChanged = true;
        } else {
            clc[`${module}Changed`] = true;
            clc[`${module}Open`] = false;
            clc[`${module}Error`] = null;
        }
    };

    clc.cancelEdit = function (module) {
        if (module === 'metaDescription') {
            clc.metaDescriptionOpen = false;
            clc.metaDescriptionChanged = false;
            clc.list.meta.description = '';
        } else if (module === 'metaTitle') {
            clc.metaTitleOpen = false;
            clc.metaTitleChanged = false;
            clc.list.meta.title = '';
        } else if (module === 'metaKeywords') {
            clc.metaKeywordsOpen = false;
            clc.metaKeywordsChanged = false;
            clc.list.meta.keywords = '';
        } else {
            clc.list[module] = angular.copy(clc.list[module]);
            clc[`${module}Open`] = false;
        }
    };

    clc.moveItem = function (direction, originalPageOrder, pageId) {
        let swapPageId = -1;
        clc.list.items.forEach(function (page) {
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
        for (let i=0; i < (clc.list.items.length); i++) {
            if(direction === 'down') {
                if(clc.list.items[i].id === pageId) {
                    clc.list.items[i].pageOrder++;
                }
                if(clc.list.items[i].id === swapPageId) {
                    clc.list.items[i].pageOrder--;
                }
            } else {
                if(clc.list.items[i].id === pageId) {
                    clc.list.items[i].pageOrder--;
                }
                if(clc.list.items[i].id === swapPageId) {
                    clc.list.items[i].pageOrder++;
                }
            }
        }
        clc.listChanged = true;
    };

    clc.submitEdit = () => {
        listService.createList(clc.list)
        .then(function () {
            $timeout(function () {
                $location.path(`/admin/edit-list/${clc.list.url}`);
            }, 100);
        }, function (e) {
            if(e.data.error) {
                clc[`${e.data.module}Error`] = e.data.status;
                clc.responseError = e.data.status;
                clc.responseErrorPopup = true;
            }
            console.error(e);
        });
    };

    clc.encodeUrl = function () {
        clc.list.url = encodeURI(clc.list.url.replace(/ /g, '-').toLowerCase());
    }

    clc.changeTitle = function () {
        clc.list.meta.title = clc.list.title;
        clc.list.url = encodeURI(clc.list.title.replace(/ /g, '-').toLowerCase());
    };

    clc.changeDescription = function () {
        clc.list.meta.description = clc.list.description.replace(/(<([^>]+)>)/gi, '');
    }

    clc.init();
};