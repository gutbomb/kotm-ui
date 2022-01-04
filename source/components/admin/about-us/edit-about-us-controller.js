import { each } from "jquery";

export default function ($location, $timeout, $scope, programService, aboutUsService, $rootScope, pageService, adminService) {
    let eauc = this;

    eauc.init = function () {
        $timeout(function () {
            if (!$rootScope.isLoggedIn) {
                $rootScope.prevPage = '/admin';
                $location.path('/login');
            } else {
                if($rootScope.user.role === 'admin') {
                    $scope.Math = window.Math;
                    eauc.getPrograms();
                    eauc.getHistory();
                    eauc.newItemId = 0;
                    eauc.historyChanged = false;
                    eauc.descriptionOpen = false;
                    eauc.descriptionChanged = false;
                    eauc.headlineOpen = false;
                    eauc.headlineChanged = false;
                    eauc.endDescriptionOpen = false;
                    eauc.endDescriptionChanged = false;
                    eauc.endHeadlineOpen = false;
                    eauc.endHeadlineChanged = false;
                    eauc.displayErrorPopup = false;
                    eauc.metaDescriptionOpen = false;
                    eauc.metaDescriptionChanged = false;
                    eauc.metaTitleOpen = false;
                    eauc.metaTitleChanged = false;
                    eauc.metaKeywordsOpen = false;
                    eauc.metaKeywordsChanged = false;
                    eauc.ckeditorConfig = {
                        bodyClass: 'results',
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

    eauc.getPrograms = function () {
        programService.getPrograms()
        .then(function (data){
            eauc.programs = data;
        }, function (error) {
            console.error('failed to retrieve programs:');
            console.error(error);
        })
    };

    eauc.getHistory = function () {
        pageService.getPage(`/about`).then( function(pageMeta) {
            $rootScope.meta = pageMeta;
        })
        .then(() => {
            aboutUsService.getHistory()
            .then(function (data){
                eauc.history = angular.copy(data);
                eauc.edited = angular.copy(data);
                eauc.edited.meta = angular.copy($rootScope.meta);
                eauc.edited.deleteYears = [];
            }, function (error) {
                console.error('failed to retrieve history:');
                console.error(error);
            });
        });
    };

    eauc.cancelEdit = function (module, itemId = false) {
        if (module === 'year' && itemId) {
            let itemIndex = eauc.getHistoryIndex(itemId);
            if (eauc.edited.years[itemIndex].newItem) {
                eauc.edited.years[itemIndex].year = '';
            } else {
                let originalItemIndex = eauc.getHistoryIndex(itemId, true);
                eauc.edited.years[itemIndex].year = angular.copy(eauc.history.years[originalItemIndex].year);
            }
            
            eauc.edited.years[itemIndex].yearOpen = false;
        } else if (module === 'image' && itemId) {
            let itemIndex = eauc.getHistoryIndex(itemId);
            if (eauc.edited.years[itemIndex].newItem) {
                eauc.edited.years[itemIndex].image = 'logo.png';
            } else {
                let originalItemIndex = eauc.getHistoryIndex(itemId, true);
                eauc.edited.years[itemIndex].image = angular.copy(eauc.history.years[originalItemIndex].image);
            }
            eauc.edited.years[itemIndex].imageOpen = false;
        } else if (module === 'description' && itemId) {
            let itemIndex = eauc.getHistoryIndex(itemId);
            if (eauc.edited.years[itemIndex].newItem) {
                eauc.edited.years[itemIndex].content = '';
            } else {
                let originalItemIndex = eauc.getHistoryIndex(itemId, true);
                eauc.edited.years[itemIndex].content = angular.copy(eauc.history.years[originalItemIndex].content);
            }
            eauc.edited.years[itemIndex].descriptionOpen = false;
        } else if (module === 'headline' && itemId) {
            let itemIndex = eauc.getHistoryIndex(itemId);
            if (eauc.edited.years[itemIndex].newItem) {
                eauc.edited.years[itemIndex].headline = '';
            } else {
                let originalItemIndex = eauc.getHistoryIndex(itemId, true);
                eauc.edited.years[itemIndex].headline = angular.copy(eauc.history.years[originalItemIndex].headline);
            }
            eauc.edited.years[itemIndex].headlineOpen = false;
        } else if (module === 'description' && itemId === false) {
            let contentIndex = eauc.getContentIndex('aboutDescription');
            let originalContentIndex = eauc.getContentIndex('aboutDescription', true);
            eauc.edited.content[contentIndex].content = angular.copy(eauc.history.content[originalContentIndex].content);
            eauc.descriptionChanged = false;
        } else if (module === 'headline' && itemId === false) {
            let contentIndex = eauc.getContentIndex('aboutHeadline');
            let originalContentIndex = eauc.getContentIndex('aboutHeadline', true);
            eauc.edited.content[contentIndex].content = angular.copy(eauc.history.content[originalContentIndex].content);
            eauc.headlineChanged = false;
        } else if (module === 'endHeadline' && itemId === false) {
            let contentIndex = eauc.getContentIndex('aboutHistoryEndHeadline');
            let originalContentIndex = eauc.getContentIndex('aboutHistoryEndHeadline', true);
            eauc.edited.content[contentIndex].content = angular.copy(eauc.history.content[originalContentIndex].content);
            eauc.endHeadlineChanged = false;
        } else if (module === 'endDescription' && itemId === false) {
            let contentIndex = eauc.getContentIndex('aboutHistoryEndText');
            let originalContentIndex = eauc.getContentIndex('aboutHistoryEndText', true);
            eauc.edited.content[contentIndex].content = angular.copy(eauc.history.content[originalContentIndex].content);
            eauc.endDescriptionChanged = false;
        } else if (module === 'metaDescription') {
            eauc.edited.meta.description = angular.copy($rootScope.meta.description);
            eauc.metaDescriptionOpen = false;
            eauc.metaDescriptionChanged = false;
        } else if (module === 'metaTitle') {
            eauc.edited.meta.title = angular.copy($rootScope.meta.title);
            eauc.metaTitleOpen = false;
            eauc.metaTitleChanged = false;
        } else if (module === 'metaKeywords') {
            eauc.edited.meta.keywords = angular.copy($rootScope.meta.keywords);
            eauc.metaKeywordsOpen = false;
            eauc.metaKeywordsChanged = false;
        } else {
            eauc.edited[module] = angular.copy(eauc.history[module]);
            eauc[`${module}Open`] = false;
        }
    };

    eauc.saveEdit = function (module, itemId = false) {
        if (module === 'year' && itemId) {
            let itemIndex = eauc.getHistoryIndex(itemId);
            eauc.edited.years[itemIndex].year = angular.copy(eauc.edited.years[itemIndex].yearEdit);
            eauc.edited.years[itemIndex].yearOpen = false;
            eauc.historyChanged = true;
        } else if (module === 'description' && itemId) {
            let itemIndex = eauc.getHistoryIndex(itemId);
            eauc.edited.years[itemIndex].descriptionOpen = false;
            eauc.historyChanged = true;
        } else if (module === 'headline' && itemId) {
            let itemIndex = eauc.getHistoryIndex(itemId);
            eauc.edited.years[itemIndex].headlineOpen = false;
            eauc.historyChanged = true;
        } else if (module === 'description' && itemId === false) {
            eauc.descriptionChanged = true;
            eauc.descriptionOpen = false;
        } else if (module === 'headline' && itemId === false) {
            eauc.headlineChanged = true;
            eauc.headlineOpen = false;
        } else if (module === 'endHeadline' && itemId === false) {
            eauc.endHeadlineChanged = true;
            eauc.endHeadlineOpen = false;
        } else if (module === 'endDescription' && itemId === false) {
            eauc.endDescriptionChanged = true;
            eauc.endDescriptionOpen = false;
        } else if (module === 'metaDescription') {
            eauc.metaDescriptionOpen = false;
            eauc.metaDescriptionChanged = true;
        } else if (module === 'metaTitle') {
            eauc.metaTitleOpen = false;
            eauc.metaTitleChanged = true;
        } else if (module === 'metaKeywords') {
            eauc.metaKeywordsOpen = false;
            eauc.metaKeywordsChanged = true;
        } else {
            eauc[`${module}Changed`] = true;
            eauc[`${module}Open`] = false;
            eauc[`${module}Error`] = null;
        }
    };

    eauc.revertEdit = function (module) {
        eauc.init();
    };

    eauc.submitEdit = function () {
        aboutUsService.updateHistory(eauc.edited)
        .then(function () {
            $location.path(`/admin/edit-about`);
            eauc.init();
        }, function (e) {
            if(e.data.error) {
                eauc[`${e.data.module}Error`] = e.data.status;
                eauc.displayErrorPopup = true;
            }
            console.error(e);
        });
    }

    eauc.selectImage = function (newImage, id) {
        let itemIndex = eauc.getHistoryIndex(id);
        eauc.edited.years[itemIndex].image = newImage;
        eauc.edited.years[itemIndex].imageChanged = true;
        eauc.edited.years[itemIndex].imageOpen = false;
        eauc.historyChanged = true;
    };

    eauc.openGallery = function (id) {
        eauc.galleryPage = 0;
        adminService.getImages()
        .then(function (data) {
            eauc.images = data;
        }, function (e) {
            console.error(e);
        })
        .then(function () {
            eauc.edited.years[eauc.getHistoryIndex(id)].imageOpen = true;
        });
    };

    eauc.changeGalleryPage = function (direction) {
        if(direction === 'prev') {
            eauc.galleryPage = eauc.galleryPage - 30;
        } else {
            eauc.galleryPage = eauc.galleryPage + 30;
        }
    };

    eauc.gallerySearch = function () {
        eauc.galleryPage = 0;
    };

    eauc.uploadImage = function (itemId) {
        eauc.fileError = false;
        if (eauc.uploadFile.type === 'image/png' ||
            eauc.uploadFile.type === 'image/gif' ||
            eauc.uploadFile.type === 'image/jpeg') {
            adminService.uploadImage(eauc.uploadFile)
            .then((r) => {
                eauc.selectImage(r.newFilename, itemId)
            }, (e) => {
                console.error(e);
                eauc.fileError = 'Sorry, there was some sort of error uploading the image.';
            });
        } else {
            console.error('file type not allowed');
            eauc.fileError = 'Sorry, only .jpg, .jpeg, .gif, or .png files are allowed.';
        }
    };

    eauc.getHistoryIndex = (itemId, original = false) => {
        if (original) {
            for (let i=0; i < (eauc.history.years.length); i++) {
                if(eauc.history.years[i].id === itemId) {
                    return i;
                }
            }
        } else {
            for (let i=0; i < (eauc.edited.years.length); i++) {
                if(eauc.edited.years[i].id === itemId) {
                    return i;
                }
            }
        }
    };

    eauc.getContentIndex = (name, original = false) => {
        if (original) {
            for (let i=0; i < (eauc.history.content.length); i++) {
                if(eauc.history.content[i].name === name) {
                    return i;
                }
            }
        } else {
            for (let i=0; i < (eauc.edited.content.length); i++) {
                if(eauc.edited.content[i].name === name) {
                    return i;
                }
            }
        }
    };

    eauc.openYear = (id) => {
        let itemIndex = eauc.getHistoryIndex(id);
        eauc.edited.years[itemIndex].yearEdit = angular.copy(eauc.edited.years[itemIndex].year);
        eauc.edited.years[itemIndex].yearOpen = true;
    };

    eauc.addYear = function () {
        eauc.historyChanged = true;
        eauc.newItemId++;
        eauc.edited.years.push({
            newItem: true,
            id: `n${eauc.newItemId}`,
            year: '',
            headline: '',
            content: '',
            image:'logo.png'
        });
    };

    eauc.cancelDeleteItem = () => {
        eauc.deleteItemPopup = false;
        eauc.deleteItemIndex = -1;
    };

    eauc.deleteItemWarning = (id) => {
        eauc.deleteItemPopup = true;
        eauc.deleteItemIndex = eauc.getHistoryIndex(id);
        eauc.deleteItemId = id;
    };

    eauc.deleteItem = () => {
        if(!eauc.edited.years[eauc.deleteItemIndex].newItem) {
            eauc.edited.deleteYears.push(eauc.deleteItemId);
        }
        eauc.edited.years.splice(eauc.deleteItemIndex, 1);
        eauc.historyChanged = true;
        eauc.deleteItemPopup = false;
        eauc.deleteItemIndex = -1;
        eauc.deleteItemId = -1;
    };

    eauc.init();
}