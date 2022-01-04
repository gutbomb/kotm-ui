export default function ($scope, $rootScope, $timeout, heroImageService, $routeParams, adminService, $location) {
    let ehc = this;

    ehc.init = function () {
        $scope.Math = window.Math;
        ehc.getHero();
        ehc.overlayClass = 'no-overlay';
        ehc.activeHero = 0;
        ehc.hero = [];
        ehc.edited = [];
        ehc.heroChanged = false;
        ehc.titleChanged = false;
        ehc.titleOpen = false;
        ehc.detailsOpen = false;
        ehc.newFrameId = -1;
        ehc.deleteFramePopup = false;
        ehc.deleteFrameIndex = -1;
        $timeout(function () {
            if (!$rootScope.isLoggedIn) {
                $rootScope.prevPage = '/admin';
                $location.path('/login');
            } else {
                if(!$rootScope.user.role === 'admin') {
                    $location.path(`/article/${$routeParams.url}`);
                }
            }
        }, 5);
    }

    ehc.changeHero = (direction) => {
        ehc.edited.items[ehc.activeHero].active = false;
        if (direction === 'next') {
            if (ehc.activeHero === (ehc.edited.items.length - 1)) {
                ehc.activeHero = 0;
            } else {
                ehc.activeHero++;
            }
        } else if (direction === 'previous') {
            if (ehc.activeHero === 0) {
                ehc.activeHero = ehc.edited.items.length - 1;
            } else {
                ehc.activeHero--;
            }
        } else {
            ehc.activeHero = ehc.getHeroIndex(direction);
        }
        ehc.edited.items[ehc.activeHero].active = true;
        ehc.overlayClass = ehc.edited.items[ehc.activeHero].position;
        
        return true;
    }

    ehc.getHero = function () {
        heroImageService.getHero($routeParams.heroId)
        .then(function (data){
            ehc.hero = angular.copy(data);
            ehc.edited = angular.copy(data);
        }, function (error) {
            console.error('failed to retrieve hero image(s):');
            console.error(error);
        })
        .finally(function () {
            ehc.edited.items[0].active = true;
            ehc.overlayClass = ehc.edited.items[0].position;
        });
    };

    ehc.getHeroIndex = (id) => {
        for (let i=0; i < (ehc.edited.items.length); i++) {
            if(ehc.edited.items[i].id === id) {
                return i;
            }
        }
    };

    ehc.getOriginalHeroIndex = (id) => {
        for (let i=0; i < (ehc.edited.items.length); i++) {
            if(ehc.edited.items[i].id === id) {
                return i;
            }
        }
    };

    ehc.changeGalleryPage = function (direction) {
        if(direction === 'prev') {
            ehc.galleryPage = ehc.galleryPage - 30;
        } else {
            ehc.galleryPage = ehc.galleryPage + 30;
        }
    };

    ehc.gallerySearch = function () {
        ehc.galleryPage = 0;
    };

    ehc.uploadImage = function (heroId) {
        ehc.fileError = false;
        if (ehc.uploadFile.type === 'image/png' ||
            ehc.uploadFile.type === 'image/gif' ||
            ehc.uploadFile.type === 'image/jpeg') {
            adminService.uploadImage(ehc.uploadFile)
            .then((r) => {
                ehc.selectImage(heroId, r.newFilename)
            }, (e) => {
                console.error(e);
                ehc.fileError = 'Sorry, there was some sort of error uploading the image.';
            });
        } else {
            console.error('file type not allowed');
            ehc.fileError = 'Sorry, only .jpg, .jpeg, .gif, or .png files are allowed.';
        }
    };

    ehc.selectImage = function (heroId, newImage) {
        let heroIndex = ehc.getHeroIndex(heroId);
        ehc.edited.items[heroIndex].image = newImage;
        ehc.edited.items[heroIndex].imageChanged = true;
        ehc.edited.items[heroIndex].imageOpen = false;
        ehc.heroChanged = true;
        ehc.changeHero(heroId);
    }

    ehc.openDetails = function (heroId) {
        ehc.detailsOpen = true;
        ehc.edited.items[ehc.getHeroIndex(heroId)].detailsOpen = true;
        ehc.changeHero(heroId);
    }

    ehc.openGallery = function (heroId) {
        ehc.galleryPage = 0;
        let heroIndex = ehc.getHeroIndex(heroId);
        adminService.getImages()
        .then(function (data) {
            ehc.images = data;
        }, function (e) {
            console.error(e);
        })
        .then(function () {
            ehc.edited.items[heroIndex].imageOpen = true;
        });
    }

    ehc.moveImage = function (direction, originalHeroOrder, id) {
        let swapHeroId = -1;
        ehc.edited.items.forEach(function (hero) {
            if(direction === 'down') {
                if(hero.heroOrder === (originalHeroOrder + 1)) {
                    swapHeroId = hero.id;
                }
            } else {
                if(hero.heroOrder === (originalHeroOrder - 1)) {
                    swapHeroId = hero.id;
                }
            }
        });
        for (let i=0; i < (ehc.edited.items.length); i++) {
            if(direction === 'down') {
                if(ehc.edited.items[i].id === id) {
                    ehc.edited.items[i].heroOrder++;
                }
                if(ehc.edited.items[i].id === swapHeroId) {
                    ehc.edited.items[i].heroOrder--;
                }
            } else {
                if(ehc.edited.items[i].id === id) {
                    ehc.edited.items[i].heroOrder--;
                }
                if(ehc.edited.items[i].id === swapHeroId) {
                    ehc.edited.items[i].heroOrder++;
                }
            }
        }
        ehc.heroChanged = true;
    };

    ehc.saveEdit = (module, heroId = false) => {
        if(heroId) {
            let heroIndex = ehc.getHeroIndex(heroId);
            ehc.edited.items[heroIndex].detailsChanged = true;
            ehc.edited.items[heroIndex].detailsOpen = false;
            ehc.heroChanged = true;
            ehc.detailsOpen = false;
        } else {
            ehc.titleChanged = true;
            ehc.titleOpen = false;
            ehc.titleError = null;
            ehc.heroChanged = true;
        }
    };

    ehc.cancelEdit = (module, heroId = false) => {
        if(heroId) {
            let heroIndex = ehc.getHeroIndex(heroId);
            let originalHeroIndex = ehc.getOriginalHeroIndex(heroId);
            ehc.edited.items[heroIndex].topline = angular.copy(ehc.hero.items[originalHeroIndex].topline);
            ehc.edited.items[heroIndex].headline = angular.copy(ehc.hero.items[originalHeroIndex].headline);
            ehc.edited.items[heroIndex].link = angular.copy(ehc.hero.items[originalHeroIndex].link);
            ehc.edited.items[heroIndex].description = angular.copy(ehc.hero.items[originalHeroIndex].description);
            ehc.edited.items[heroIndex].position = angular.copy(ehc.hero.items[originalHeroIndex].position);
            ehc.edited.items[heroIndex].image = angular.copy(ehc.hero.items[originalHeroIndex].image);
            ehc.edited.items[heroIndex].detailsChanged = false;
            ehc.edited.items[heroIndex].detailsOpen = false;
            ehc.edited.items[heroIndex].imageChanged = false;
            ehc.edited.items[heroIndex].imageOpen = false;
        } else {
            ehc.edited.title = angular.copy(ehc.hero.title);
            ehc.titleChanged = false;
            ehc.titleOpen = false;
            ehc.titleError = null;
        }
    };

    ehc.submitEdit = () => {
        heroImageService.updateHero(ehc.edited.id, ehc.edited)
        .then(function () {
            $location.path(`/admin/edit-hero/${ehc.edited.id}`);
            ehc.init();
        }, function (e) {
            if(e.data.error) {
                ehc[`${e.data.module}Error`] = e.data.status;
                ehc.displayErrorPopup = true;
            }
        });
    };

    ehc.addFrame = () => {
        ehc.newFrameID++;
        let newHeroOrder = 0;
        for (let i=0; i < (ehc.edited.items.length); i++) {
            if(ehc.edited.items[i].heroOrder > newHeroOrder) {
                newHeroOrder = ehc.edited.items[i].heroOrder;
            }
        }
        newHeroOrder++;
        ehc.edited.items.push({
            newItem: true,
            id: `n${ehc.newFrameID}`,
            topline: '',
            headline: '',
            heroOrder: newHeroOrder,
            image: '',
            link: '',
            description: '',
            position: 'no-overlay',
            detailsChanged: true,
            detailsOpen: false
        });
        ehc.heroChanged = true;
    };

    ehc.deleteFrameWarning = (id) => {
        ehc.deleteFramePopup = true;
        ehc.deleteFrameIndex = ehc.getHeroIndex(id);
    };

    ehc.deleteFrame = () => {
        if(ehc.edited.items[ehc.deleteFrameIndex].newItem) {
            ehc.edited.items.splice(ehc.deleteFrameIndex, 1);
        } else {
            ehc.edited.items[ehc.deleteFrameIndex].deleted = true;
        }
        ehc.heroChanged = true;
        ehc.deleteFramePopup = false;
        ehc.deleteFrameIndex = -1;
    };

    ehc.cancelDeleteFrame = () => {
        ehc.deleteFramePopup = false;
        ehc.deleteFrameIndex = -1;
    };

    ehc.init(); 
}