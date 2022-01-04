export default function ($scope, $rootScope, $timeout, heroImageService, adminService, $location) {
    let chc = this;

    chc.init = function () {
        $scope.Math = window.Math;
        chc.overlayClass = 'no-overlay';
        chc.activeHero = 0;
        chc.hero = {
            title: 'New Carousel',
            items: []
        };
        chc.newHeroId = 0;
        chc.heroChanged = false;
        chc.titleChanged = false;
        chc.titleOpen = false;
        chc.detailsOpen = false;
        chc.deleteFramePopup = false;
        chc.deleteFrameIndex = -1;
        $timeout(function () {
            if (!$rootScope.isLoggedIn) {
                $rootScope.prevPage = '/admin';
                $location.path('/login');
            } else {
                if(!$rootScope.user.role === 'admin') {
                    $location.path(`/home`);
                }
            }
        }, 5);
    }

    chc.getHeroIndex = (id) => {
        for (let i=0; i < (chc.hero.items.length); i++) {
            if(chc.hero.items[i].id === id) {
                return i;
            }
        }
    };

    chc.changeHero = (direction) => {
        if(chc.activeHero) {
            chc.hero.items[chc.activeHero].active = false;
        }
        if (direction === 'next') {
            if (chc.activeHero === (chc.hero.items.length - 1)) {
                chc.activeHero = 0;
            } else {
                chc.activeHero++;
            }
        } else if (direction === 'previous') {
            if (chc.activeHero === 0) {
                chc.activeHero = chc.hero.items.length - 1;
            } else {
                chc.activeHero--;
            }
        } else {
            chc.activeHero = chc.getHeroIndex(direction);
        }
        chc.hero.items[chc.activeHero].active = true;
        chc.overlayClass = chc.hero.items[chc.activeHero].position;
        
        return true;
    }

    chc.changeGalleryPage = function (direction) {
        if(direction === 'prev') {
            chc.galleryPage = chc.galleryPage - 30;
        } else {
            chc.galleryPage = chc.galleryPage + 30;
        }
    };

    chc.gallerySearch = function () {
        chc.galleryPage = 0;
    };

    chc.uploadImage = function (heroId) {
        chc.fileError = false;
        if (chc.uploadFile.type === 'image/png' ||
            chc.uploadFile.type === 'image/gif' ||
            chc.uploadFile.type === 'image/jpeg') {
            adminService.uploadImage(chc.uploadFile)
            .then((r) => {
                chc.selectImage(heroId, r.newFilename)
            }, (e) => {
                console.error(e);
                chc.fileError = 'Sorry, there was some sort of error uploading the image.';
            });
        } else {
            console.error('file type not allowed');
            chc.fileError = 'Sorry, only .jpg, .jpeg, .gif, or .png files are allowed.';
        }
    };

    chc.selectImage = function (heroId, newImage) {
        let heroIndex = chc.getHeroIndex(heroId);
        chc.hero.items[heroIndex].image = newImage;
        chc.hero.items[heroIndex].imageChanged = true;
        chc.hero.items[heroIndex].imageOpen = false;
        chc.heroChanged = true;
        chc.changeHero(heroId);
    }

    chc.openDetails = function (heroId) {
        let heroIndex = chc.getHeroIndex(heroId);
        chc.detailsOpen = true;
        chc.hero.items[heroIndex].detailsOpen = true;
        chc.changeHero(heroId);
    }

    chc.openGallery = function (heroId) {
        chc.galleryPage = 0;
        let heroIndex = chc.getHeroIndex(heroId);
        adminService.getImages()
        .then(function (data) {
            chc.images = data;
        }, function (e) {
            console.error(e);
        })
        .then(function () {
            chc.hero.items[heroIndex].imageOpen = true;
        });
    }

    chc.moveImage = function (direction, originalHeroOrder, id) {
        let swapHeroId = -1;
        chc.hero.items.forEach(function (hero) {
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
        for (let i=0; i < (chc.hero.items.length); i++) {
            if(direction === 'down') {
                if(chc.hero.items[i].id === id) {
                    chc.hero.items[i].heroOrder++;
                }
                if(chc.hero.items[i].id === swapHeroId) {
                    chc.hero.items[i].heroOrder--;
                }
            } else {
                if(chc.hero.items[i].id === id) {
                    chc.hero.items[i].heroOrder--;
                }
                if(chc.hero.items[i].id === swapHeroId) {
                    chc.hero.items[i].heroOrder++;
                }
            }
        }
        chc.heroChanged = true;
    };

    chc.saveEdit = (heroId = false) => {
        
        if(heroId !== false) {
            let heroIndex = chc.getHeroIndex(heroId);
            chc.hero.items[heroIndex].detailsChanged = true;
            chc.hero.items[heroIndex].detailsOpen = false;
            chc.heroChanged = true;
            chc.detailsOpen = false;
        } else {
            chc.titleChanged = true;
            chc.titleOpen = false;
            chc.titleError = null;
            chc.heroChanged = true;
        }
    };

    chc.cancelEdit = (heroId = false) => {
        if(heroId) {
            let heroIndex = chc.getHeroIndex(heroId);
            chc.hero.items[heroIndex].topline = '';
            chc.hero.items[heroIndex].headline = '';
            chc.hero.items[heroIndex].link = '';
            chc.hero.items[heroIndex].image = '';
            chc.hero.items[heroIndex].description = '';
            chc.hero.items[heroIndex].position = 'no-overlay';
            chc.hero.items[heroIndex].detailsChanged = false;
            chc.hero.items[heroIndex].detailsOpen = false;
            chc.hero.items[heroIndex].imageChanged = false;
            chc.hero.items[heroIndex].imageOpen = false;
        } else {
            chc.hero.title = '';
            chc.titleChanged = false;
            chc.titleOpen = false;
            chc.titleError = null;
        }
    };

    chc.submitEdit = () => {
        heroImageService.createHero(chc.hero)
        .then(function () {
            $location.path(`/admin`);
        }, function (e) {
            if(e.data.error) {
                chc[`${e.data.module}Error`] = e.data.status;
                chc.displayErrorPopup = true;
            }
            console.error(e);
        });
    };

    chc.addFrame = () => {
        let newHeroOrder = -1;
        if(chc.hero.items.length) {
            for (let i=0; i < (chc.hero.items.length); i++) {
                if(chc.hero.items[i].heroOrder > newHeroOrder) {
                    newHeroOrder = chc.hero.items[i].heroOrder;
                }
            }
        }
        newHeroOrder++;
        chc.newHeroId++;
        chc.hero.items.push({
            id: chc.newHeroId,
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
        chc.heroChanged = true;
    };

    chc.deleteFrameWarning = (heroId) => {
        chc.deleteFramePopup = true;
        chc.deleteFrameIndex = chc.getHeroIndex(heroId);
    };

    chc.deleteFrame = () => {
        chc.hero.items.splice(chc.deleteFrameIndex, 1);
        chc.heroChanged = true;
        chc.deleteFramePopup = false;
        chc.deleteFrameIndex = -1;
    };

    chc.cancelDeleteFrame = () => {
        chc.deleteFramePopup = false;
        chc.deleteFrameIndex = -1;
    };

    chc.init(); 
}