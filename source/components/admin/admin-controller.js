export default function ($filter, $scope, $location, $rootScope, loginService, articleService, listService, $timeout, heroImageService, programService, adminService, pageService, pageHeaderService, pageFooterService, mediaService) {
    let ac = this;
    
    // Admin Page Functions
    ac.changeTab = function (tab) {
        ac.activeTab = tab;
        switch(tab) {
            case 'articles':
                ac.getArticles();
                ac.deleteArticleTitle = '';
                ac.deleteArticleUrl = -1;
                ac.deleteArticleId = -1;
                ac.displayDeleteArticlePopup = false;
                ac.deleteListTitle = '';
                ac.deleteListUrl = -1;
                ac.deleteListId = -1;
                ac.deleteListPageId = -1;
                ac.displayDeleteListPopup = false;
                break;

            case 'specialpages':
                break;

            case 'images':
                ac.getImages();
                break;

            case 'carousels':
                ac.getHeroes();
                break;

            case 'programs':
                ac.getPrograms();
                break;

            case 'header':
                ac.getHeader();
                ac.getPages();
                break;

            case 'footer':
                ac.getFooter();
                ac.getPages();
                break;

            case 'forms':
                ac.getForms();
                ac.deleteFormPopup = false;
                ac.deletedForm = {
                    title: false,
                    url: false
                };
                break;

            case 'media':
                ac.getMedia();
                break;

            case 'rsvp':
                ac.getEvents();
                $scope.filter = $filter;
                ac.rsvpOpen = true;
                ac.today = moment().format('X');
                break;

            case 'locations':
                ac.getLocations();
                ac.ckeditorConfig = {
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
                break;

            case 'account':
                ac.getUsers();
                ac.getUser(ac.user.id);
                ac.newUserForm = false;
                ac.deleteUserName = '';
                ac.deleteUserId = -1;
                ac.displayDeleteUserPopup = false;
                ac.newUser = {
                    firstname: '',
                    lastName: '',
                    email: ''
                };

                break;
        }
    };

    ac.getPages = function () {
        pageService.getPages()
        .then((pages) => {
            ac.pages = pages;
        });
    };

    ac.getForms = function () {
        adminService.getForms()
        .then((forms) => {
            ac.forms = forms;
        });
    };

    ac.init = function () {
        $scope.Math = window.Math;
        $timeout(function () {
            if (!$rootScope.isLoggedIn) {
                $rootScope.prevPage = '/admin';
                $location.path('/login');
            } else {
                if($rootScope.user.role==='admin' || $rootScope.user.role==='editor') {
                    console.log($rootScope.user);
                    ac.user = $rootScope.user;
                    ac.changeTab('articles');
                } else {
                    $location.path('/home');
                }
            }
        }, 5);
    };

    ac.init();

    // Account
    ac.addUser = function () {
        adminService.addUser(ac.newUser)
        .then(function (data) {
            ac.changeTab('account');
        }, function (e) {
            console.error('unable to add user');
            console.error(e);
        });
    };

    ac.closeDeleteUserPopup = function () {
        ac.displayDeleteUserPopup = false;
        ac.deleteUserName = '';
        ac.deleteUserId = -1;
    };

    ac.deleteUser = function () {
        adminService.deleteUser(ac.deleteUserId)
        .then(function () {
            ac.changeTab('account');
        }, function (e) {
            console.error(e);
        })
    };

    ac.getUser = function (userId) {
        adminService.getUser(userId)
        .then(function (data) {
            ac.editUser = data[0];
        }, function (e) {
            console.error('unable to fetch user');
            console.error(e);
        });
    };

    ac.getUsers = function () {
        adminService.getUsers()
        .then(function (data) {
            ac.users = data;
        }, function (e) {
            console.error('unable to fetch users');
            console.error(e);
        });
    };

    ac.logout = function () {
        loginService.logoutUser();
    };

    ac.openDeleteUserPopup = function (userIndex) {
        ac.displayDeleteUserPopup = true;
        ac.deleteUserName = `${ac.users[userIndex].firstname} ${ac.users[userIndex].lastname}`;
        ac.deleteUserId = ac.users[userIndex].id;
    };

    ac.selectEditUser = function (userIndex) {
        ac.editUser=ac.users[userIndex];
    };

    ac.updateUser = () => {
        adminService.updateUser(ac.editUser)
        .then(function () {
            ac.getUsers();
            ac.editUser = {};
        }, function (e) {
            if(e.data.error) {
                ac[`${e.data.module}Error`] = e.data.status;
                ac.displayErrorPopup = true;
            }
            console.error(e);
        });
    };

    // Articles
    ac.closeDeleteArticlePopup = function () {
        ac.deleteArticleTitle = '';
        ac.deleteArticleUrl = -1;
        ac.deleteArticleId = -1;
        ac.displayDeleteArticlePopup = false;
    };

    ac.closeDeleteListPopup = function () {
        ac.deleteListTitle = '';
        ac.deleteListUrl = -1;
        ac.deleteListId = -1;
        ac.deleteListPageId = -1;
        ac.displayDeleteListPopup = false;
    };
    
    ac.deleteArticle = function (url, id) {
        articleService.deleteArticle(url, id)
        .then(function () {
            ac.changeTab('articles');
        }, function (e) {
            console.error(e);
        })
    };

    ac.deleteList = function (url, id, pageId) {
        listService.deleteList(url, id, pageId)
        .then(function () {
            ac.changeTab('articles');
        }, function (e) {
            console.error(e);
        });
    };

    ac.getArticles = function () {
        articleService.getArticles()
        .then(function (data) {
            ac.articles = data;
        }, function (e) {
            console.error('unable to fetch articles');
            console.error(e);
        });
        listService.getLists()
        .then(function (data) {
            ac.lists = data;
        }, function (e) {
            console.error('unable to fetch lists');
            console.error(e);
        });
    };

    ac.openDeleteArticlePopup = function (title, url, id) {
        ac.displayDeleteArticlePopup = true;
        ac.deleteArticleTitle = title;
        ac.deleteArticleUrl = url;
        ac.deleteArticleId = id;
    };

    ac.openDeleteListPopup = function (title, url, id, pageId) {
        ac.displayDeleteListPopup = true;
        ac.deleteListTitle = title;
        ac.deleteListUrl = url;
        ac.deleteListId = id;
        ac.deleteListPageId = pageId;
    };

    // Carousels
    ac.getHeroes = function () {
        heroImageService.getHeroes()
        .then(function (data) {
            ac.heroes = data;
        }, function (e) {
            console.error('unable to fetch heroes');
            console.error(e);
        });
    }

    // Events
    ac.getEvents = function () {
        adminService.getEvents()
        .then(function (data) {
            ac.events = data;
        }, function (e) {
            console.error('unable to fetch events');
            console.error(e);
        });
    };

    ac.getEventIndex = (eventId) => {
        for (let i=0; i < (ac.events.length); i++) {
            if(ac.events[i].id === eventId) {
                return i;
            }
        }
    };

    ac.removeRsvp = function (eventId) {
        adminService.removeRsvp(eventId)
        .then(function () {
            $timeout(() => {
                ac.getEvents();
            }, 1000);
        }, function (e) {
            if(e.data.error) {
                ac[`${e.data.module}Error`] = e.data.status;
                ac.displayErrorPopup = true;
            }
            console.error(e);
        });
    };

    ac.addRsvp = function (eventId) {
        let eventIndex = ac.getEventIndex(eventId);
        ac.events[eventIndex].rsvp = true;
        ac.events[eventIndex].newRsvp = true;
        ac.events[eventIndex].open = true;
        ac.events[eventIndex].rsvpEmail = $rootScope.user.email;

    }

    ac.viewRsvp = function (eventId) {
        let eventIndex = ac.getEventIndex(eventId);
        ac.events[eventIndex].view = true;
        adminService.viewRsvp(eventId)
        .then(function (data) {
            ac.events[eventIndex].rsvpData = data;
        }, function (e) {
            console.error('unable to fetch rsvp');
            console.error(e);
        });
    };

    ac.saveRsvp = function (eventObj) {
        if (!eventObj.attendees) {
            eventObj.attendees = 9999;
        }
        adminService.saveRsvp(eventObj)
        .then(function () {
            $timeout(() => {
                ac.getEvents();
            }, 1000);
        }, function (e) {
            if(e.data.error) {
                ac[`${e.data.module}Error`] = e.data.status;
                ac.displayErrorPopup = true;
            }
            console.error(e);
        });
    };


    // Footer
    ac.addFooterGroupItem = function (footerGroupId) {
        let footerGroupIndex = ac.getFooterGroupIndex(footerGroupId);
        ac.newFooterGroupItemId++
        let newItemOrder = 0;
        for (let i=0; i < (ac.editedFooter[footerGroupIndex].items.length); i++) {
            if(ac.editedFooter[footerGroupIndex].items[i].itemOrder > newItemOrder) {
                newItemOrder = ac.editedFooter[footerGroupIndex].items[i].itemOrder;
            }
        }
        newItemOrder++;
        ac.editedFooter[footerGroupIndex].items.push({
            newItem: true,
            title: 'New Item',
            itemOrder: newItemOrder,
            id: `n${ac.newFooterGroupItemID}`,
            url: ''
        });
        ac.footerChanged = true;
    };

    ac.cancelDeleteFooterGroupItem = function () {
        ac.deleteFooterGroupItemPopup = false;
        ac.deleteFooterGroupItem = {
            footerGroupIndex: -1,
            itemIndex: -1
        };
    };

    ac.cancelFooterGroupItem = function (footerGroupId, itemId) {
        let footerGroupIndex = ac.getFooterGroupIndex(footerGroupId);
        let itemIndex = ac.getFooterGroupItemIndex(footerGroupIndex, itemId);
        let originalFooterGroupIndex = ac.getFooterGroupIndex(footerGroupId, true);
        let originalItemIndex = ac.getFooterGroupItemIndex(originalFooterGroupIndex, itemId, true);
        if (ac.editedFooter[footerGroupIndex].items[itemIndex].newItem) {
            ac.editedFooter[footerGroupIndex].items[itemIndex].title = 'New Item';
            ac.editedFooter[footerGroupIndex].items[itemIndex].url = '';
        } else {
            ac.editedFooter[footerGroupIndex].items[itemIndex].title = angular.copy($rootScope.footer[originalFooterGroupIndex].items[originalItemIndex].title);
            ac.editedFooter[footerGroupIndex].items[itemIndex].url = angular.copy($rootScope.footer[originalFooterGroupIndex].items[originalItemIndex].url);
        }
        ac.editedFooter[footerGroupIndex].items[itemIndex].itemOpen = false;
        ac.footerGroupItemOpen = false;
        ac.selectedFooterGroupItemUrl = '';
    };

    ac.cancelFooterGroupTitle = function (footerGroupId) {
        ac.footerGroupItemOpen = false;
        let footerGroupIndex = ac.getFooterGroupIndex(footerGroupId);
        let originalFooterGroupIndex = ac.getFooterGroupIndex(footerGroupId, true);
        ac.editedFooter[footerGroupIndex].title = angular.copy($rootScope.footer[originalFooterGroupIndex].title);
        ac.editedFooter[footerGroupIndex].titleOpen = false;
    };

    ac.deleteFooterGroupItemWarning = function (footerGroupId, itemId) {
        ac.deleteFooterGroupItemPopup = true;
        let footerGroupIndex = ac.getFooterGroupIndex(footerGroupId);
        ac.deleteFooterGroupItem = {
            footerGroupIndex: footerGroupIndex,
            itemIndex: ac.getFooterGroupItemIndex(footerGroupIndex, itemId)
        };
    };

    ac.doDeleteFooterGroupItem = function () {
        if(ac.editedFooter[ac.deleteFooterGroupItem.footerGroupIndex].items[ac.deleteFooterGroupItem.itemIndex].newItem) {
            ac.editedFooter[ac.deleteFooterGroupItem.footerGroupIndex].items.splice(ac.deleteFooterGroupItem.itemIndex, 1);
        } else {
            ac.editedFooter[ac.deleteFooterGroupItem.footerGroupIndex].items[ac.deleteFooterGroupItem.itemIndex].deleted = true;
        }
        ac.footerChanged = true;
        ac.deleteFooterGroupItemPopup = false;
        ac.deleteFooterGroupItem = {
            footerGroupIndex: -1,
            itemIndex: -1
        };
    };

    ac.getFooter = function () {
        pageFooterService.getFooter()
        .then((menu) => {
            $rootScope.footer = angular.copy(menu);
            ac.editedFooter = angular.copy(menu);
            ac.footerChanged = false;
            ac.newFooterGroupItemId = 0;
            ac.deleteFooterGroupItem = {
                groupIndex: -1,
                itemIndex: -1
            };
            ac.footerGroupItemOpen = false;
        });
    };

    ac.getFooterGroupIndex = (footerGroupId, original = false) => {
        if (original) {
            for (let i=0; i < ($rootScope.footer.length); i++) {
                if($rootScope.footer[i].id === footerGroupId) {
                    return i;
                }
            }
        } else {
            for (let i=0; i < (ac.editedFooter.length); i++) {
                if(ac.editedFooter[i].id === footerGroupId) {
                    return i;
                }
            }
        }
        
    };

    ac.getFooterGroupItemIndex = (footerGroupIndex, itemId, original = false) => {
        if (original) {
            for (let i=0; i < ($rootScope.footer[footerGroupIndex].items.length); i++) {
                if($rootScope.footer[footerGroupIndex].items[i].id === itemId) {
                    return i;
                }
            }
        } else {
            for (let i=0; i < (ac.editedFooter[footerGroupIndex].items.length); i++) {
                if(ac.editedFooter[footerGroupIndex].items[i].id === itemId) {
                    return i;
                }
            }
        }
    };

    ac.getPageIndex = (pageId) => {
        for (let i=0; i < (ac.pages.length); i++) {
            if(ac.pages[i].id === pageId) {
                return i;
            }
        }
    };

    ac.moveFooterGroupItem = function (direction, originalItemOrder, footerGroupId, itemId) {
        let footerGroupIndex = ac.getFooterGroupIndex(footerGroupId);
        let swapItemId = -1;
        ac.editedFooter[footerGroupIndex].items.forEach(function (item) {
            if(direction === 'down') {
                if(item.itemOrder === (originalItemOrder + 1)) {
                    swapItemId = item.id;
                }
            } else {
                if(item.itemOrder === (originalItemOrder - 1)) {
                    swapItemId = item.id;
                }
            }
        });
        for (let i=0; i < (ac.editedFooter[footerGroupIndex].items.length); i++) {
            if(direction === 'down') {
                if(ac.editedFooter[footerGroupIndex].items[i].id === itemId) {
                    ac.editedFooter[footerGroupIndex].items[i].itemOrder++;
                }
                if(ac.editedFooter[footerGroupIndex].items[i].id === swapItemId) {
                    ac.editedFooter[footerGroupIndex].items[i].itemOrder--;
                }
            } else {
                if(ac.editedFooter[footerGroupIndex].items[i].id === itemId) {
                    ac.editedFooter[footerGroupIndex].items[i].itemOrder--;
                }
                if(ac.editedFooter[footerGroupIndex].items[i].id === swapItemId) {
                    ac.editedFooter[footerGroupIndex].items[i].itemOrder++;
                }
            }
        }
        ac.footerChanged = true;
    };

    ac.openFooterGroupItem = function (footerGroupId, itemId) {
        ac.footerGroupItemOpen = true;
        let footerGroupIndex = ac.getFooterGroupIndex(footerGroupId);
        let itemIndex = ac.getFooterGroupItemIndex(footerGroupIndex, itemId);
        ac.editedFooter[footerGroupIndex].items[itemIndex].itemOpen = true;
    };

    ac.openFooterGroupTitle = function (footerGroupId) {
        ac.footerGroupItemOpen = true;
        let footerGroupIndex = ac.getFooterGroupIndex(footerGroupId);
        ac.editedFooter[footerGroupIndex].titleOpen = true;
    };

    ac.saveFooterGroupItem = function (footerGroupId, itemId) {
        let footerGroupIndex = ac.getFooterGroupIndex(footerGroupId);
        let itemIndex = ac.getFooterGroupItemIndex(footerGroupIndex, itemId);
        ac.editedFooter[footerGroupIndex].items[itemIndex].itemOpen = false;
        ac.footerGroupItemOpen = false;
        ac.selectedFooterGroupItemUrl = '';
        ac.footerChanged = true;
    };

    ac.saveFooterGroupTitle = function (footerGroupId) {
        ac.footerGroupItemOpen = false;
        let footerGroupIndex = ac.getFooterGroupIndex(footerGroupId);
        ac.editedFooter[footerGroupIndex].titleOpen = false;
        ac.footerChanged = true;
    };

    ac.selectFooterPage = function (footerGroupId, itemId) {
        let footerGroupIndex = ac.getFooterGroupIndex(footerGroupId);
        let itemIndex = ac.getFooterGroupItemIndex(footerGroupIndex, itemId);
        let pageIndex = ac.getPageIndex(ac.selectedFooterGroupPageId);
        ac.editedFooter[footerGroupIndex].items[itemIndex].title = ac.pages[pageIndex].title;
        ac.editedFooter[footerGroupIndex].items[itemIndex].url = ac.pages[pageIndex].url;
    };

    ac.submitFooterEdit = () => {
        pageFooterService.updateFooter(ac.editedFooter)
        .then(function () {
            $timeout(() => {
                ac.getFooter();
            }, 1000);
        }, function (e) {
            if(e.data.error) {
                ac[`${e.data.module}Error`] = e.data.status;
                ac.displayErrorPopup = true;
            }
            console.error(e);
        });
    };

    // Header
    ac.addMenuItem = function (menuId) {
        let menuIndex = ac.getMenuIndex(menuId);
        ac.newMenuItemId++
        let newItemOrder = 0;
        for (let i=0; i < (ac.editedHeader[menuIndex].items.length); i++) {
            if(ac.editedHeader[menuIndex].items[i].itemOrder > newItemOrder) {
                newItemOrder = ac.editedHeader[menuIndex].items[i].itemOrder;
            }
        }
        newItemOrder++;
        ac.editedHeader[menuIndex].items.push({
            newItem: true,
            title: 'New Item',
            itemOrder: newItemOrder,
            id: `n${ac.newMenuItemID}`,
            url: ''
        });
        ac.menuChanged = true;
    };

    ac.cancelDeleteMenuItem = function () {
        ac.deleteMenuItemPopup = false;
        ac.deleteMenuItem = {
            menuIndex: -1,
            itemIndex: -1
        };
    };

    ac.cancelMenuItem = function (menuId, itemId) {
        let menuIndex = ac.getMenuIndex(menuId);
        let itemIndex = ac.getMenuItemIndex(menuIndex, itemId);
        let originalMenuIndex = ac.getMenuIndex(menuId, true);
        let originalItemIndex = ac.getMenuItemIndex(originalMenuIndex, itemId, true);
        if (ac.editedHeader[menuIndex].items[itemIndex].newItem) {
            ac.editedHeader[menuIndex].items[itemIndex].title = 'New Item';
            ac.editedHeader[menuIndex].items[itemIndex].url = '';
        } else {
            ac.editedHeader[menuIndex].items[itemIndex].title = angular.copy($rootScope.menu[originalMenuIndex].items[originalItemIndex].title);
            ac.editedHeader[menuIndex].items[itemIndex].url = angular.copy($rootScope.menu[originalMenuIndex].items[originalItemIndex].url);
        }
        ac.editedHeader[menuIndex].items[itemIndex].itemOpen = false;
        ac.menuItemOpen = false;
        ac.selectedMenuItemUrl = '';
    };

    ac.cancelMenuTitle = function (menuId) {
        ac.menuItemOpen = false;
        let menuIndex = ac.getMenuIndex(menuId);
        let originalMenuIndex = ac.getMenuIndex(menuId, true);
        ac.editedHeader[menuIndex].title = angular.copy($rootScope.menu[originalMenuIndex].title);
        ac.editedHeader[menuIndex].titleOpen = false;
    };

    ac.deleteMenuItemWarning = function (menuId, itemId) {
        ac.deleteMenuItemPopup = true;
        let menuIndex = ac.getMenuIndex(menuId);
        ac.deleteMenuItem = {
            menuIndex: menuIndex,
            itemIndex: ac.getMenuItemIndex(menuIndex, itemId)
        };
    };

    ac.doDeleteMenuItem = function () {
        if(ac.editedHeader[ac.deleteMenuItem.menuIndex].items[ac.deleteMenuItem.itemIndex].newItem) {
            ac.editedHeader[ac.deleteMenuItem.menuIndex].items.splice(ac.deleteMenuItem.itemIndex, 1);
        } else {
            ac.editedHeader[ac.deleteMenuItem.menuIndex].items[ac.deleteMenuItem.itemIndex].deleted = true;
        }
        ac.menuChanged = true;
        ac.deleteMenuItemPopup = false;
        ac.deleteMenuItem = {
            menuIndex: -1,
            itemIndex: -1
        };
    };

    ac.getHeader = function () {
        pageHeaderService.getMenu()
        .then((menu) => {
            $rootScope.menu = angular.copy(menu);
            ac.editedHeader = angular.copy(menu);
            ac.menuChanged = false;
            ac.newMenuItemId = 0;
            ac.deleteMenuItem = {
                menuIndex: -1,
                itemIndex: -1
            };
            ac.menuItemOpen = false;
        });
    };

    ac.getMenuIndex = (menuId, original = false) => {
        if (original) {
            for (let i=0; i < ($rootScope.menu.length); i++) {
                if($rootScope.menu[i].id === menuId) {
                    return i;
                }
            }
        } else {
            for (let i=0; i < (ac.editedHeader.length); i++) {
                if(ac.editedHeader[i].id === menuId) {
                    return i;
                }
            }
        }
        
    };

    ac.getMenuItemIndex = (menuIndex, itemId, original = false) => {
        if (original) {
            for (let i=0; i < ($rootScope.menu[menuIndex].items.length); i++) {
                if($rootScope.menu[menuIndex].items[i].id === itemId) {
                    return i;
                }
            }
        } else {
            for (let i=0; i < (ac.editedHeader[menuIndex].items.length); i++) {
                if(ac.editedHeader[menuIndex].items[i].id === itemId) {
                    return i;
                }
            }
        }
    };

    ac.moveMenuItem = function (direction, originalItemOrder, menuId, itemId) {
        let menuIndex = ac.getMenuIndex(menuId);
        let swapItemId = -1;
        ac.editedHeader[menuIndex].items.forEach(function (item) {
            if(direction === 'down') {
                if(item.itemOrder === (originalItemOrder + 1)) {
                    swapItemId = item.id;
                }
            } else {
                if(item.itemOrder === (originalItemOrder - 1)) {
                    swapItemId = item.id;
                }
            }
        });
        for (let i=0; i < (ac.editedHeader[menuIndex].items.length); i++) {
            if(direction === 'down') {
                if(ac.editedHeader[menuIndex].items[i].id === itemId) {
                    ac.editedHeader[menuIndex].items[i].itemOrder++;
                }
                if(ac.editedHeader[menuIndex].items[i].id === swapItemId) {
                    ac.editedHeader[menuIndex].items[i].itemOrder--;
                }
            } else {
                if(ac.editedHeader[menuIndex].items[i].id === itemId) {
                    ac.editedHeader[menuIndex].items[i].itemOrder--;
                }
                if(ac.editedHeader[menuIndex].items[i].id === swapItemId) {
                    ac.editedHeader[menuIndex].items[i].itemOrder++;
                }
            }
        }
        ac.menuChanged = true;
    };

    ac.openMenuItem = function (menuId, itemId) {
        ac.menuItemOpen = true;
        let menuIndex = ac.getMenuIndex(menuId);
        let itemIndex = ac.getMenuItemIndex(menuIndex, itemId);
        ac.editedHeader[menuIndex].items[itemIndex].itemOpen = true;
    };

    ac.openMenuTitle = function (menuId) {
        ac.menuItemOpen = true;
        let menuIndex = ac.getMenuIndex(menuId);
        ac.editedHeader[menuIndex].titleOpen = true;
    };

    ac.saveMenuItem = function (menuId, itemId) {
        let menuIndex = ac.getMenuIndex(menuId);
        let itemIndex = ac.getMenuItemIndex(menuIndex, itemId);
        ac.editedHeader[menuIndex].items[itemIndex].itemOpen = false;
        ac.menuItemOpen = false;
        ac.selectedMenuItemUrl = '';
        ac.menuChanged = true;
    };

    ac.saveMenuTitle = function (menuId) {
        ac.menuItemOpen = false;
        let menuIndex = ac.getMenuIndex(menuId);
        ac.editedHeader[menuIndex].titleOpen = false;
        ac.menuChanged = true;
    };

    ac.submitMenuEdit = () => {
        pageHeaderService.updateMenu(ac.editedHeader)
        .then(function () {
            ac.getHeader();
        }, function (e) {
            if(e.data.error) {
                ac[`${e.data.module}Error`] = e.data.status;
                ac.displayErrorPopup = true;
            }
            console.error(e);
        });
    };

    // Images
    ac.getImages = function () {
        adminService.getImages()
        .then(function (data) {
            ac.images = data;
            ac.galleryPage = 0;
        }, function (e) {
            console.error(e);
        });
    };

    ac.clipboardNotify = function (imageId) {
        for (let i=0; i < (ac.images.length); i++) {
            if(ac.images[i].id === imageId) {
                ac.images[i].notify = 'URL copied to clipboard';
            } else {
                ac.images[i].notify = false;
            }
        }
    };

    ac.uploadMainGalleryImage = function (locationId) {
        ac.fileError = false;
        ac.uploading = true;
        if (ac.uploadFile.type === 'image/png' ||
            ac.uploadFile.type === 'image/gif' ||
            ac.uploadFile.type === 'image/jpeg') {
            adminService.uploadImage(ac.uploadFile)
            .then((r) => {
                ac.uploadFile = '';
                ac.uploading = false;
                ac.getImages();
            }, (e) => {
                console.error(e);
                ac.fileError = 'Sorry, there was some sort of error uploading the image.';
            });
        } else {
            console.error('file type not allowed');
            ac.fileError = 'Sorry, only .jpg, .jpeg, .gif, or .png files are allowed.';
        }
    };

    // Locations
    ac.getLocations = function () {
        ac.newLocationId = 0;
        ac.locationsChanged = false;
        adminService.getLocations()
        .then((locations) => {
            ac.locations = angular.copy(locations);
            ac.editedLocations = angular.copy(locations);
        });
    };

    ac.getLocationIndex = (locationId, original = false) => {
        if (original) {
            for (let i=0; i < (ac.locations.length); i++) {
                if(ac.locations[i].id === locationId) {
                    return i;
                }
            }
        } else {
            for (let i=0; i < (ac.editedLocations.length); i++) {
                if(ac.editedLocations[i].id === locationId) {
                    return i;
                }
            }
        }
    };

    ac.openLocationGallery = function (locationId) {
        ac.getImages();
        ac.editedLocations[ac.getLocationIndex(locationId)].imageOpen = true;
    };

    ac.newLocation = function () {
        ac.newLocationId++;
        ac.editedLocations.push({
            id: `n${ac.newLocationId}`,
            title: '',
            description: '',
            newLocation: true,
            image: '',
            street1: '',
            street2: '',
            city: '',
            state: '',
            zip: '',
            slug: ''
        });
        ac.locationsChanged = true;
    };

    ac.selectLocationImage = function (locationId, newImage) {
        let locationIndex = ac.getLocationIndex(locationId);
        ac.editedLocations[locationIndex].image = newImage;
        ac.locationsChanged = true;
        ac.editedLocations[locationIndex].imageOpen = false;
    };

    ac.uploadLocationImage = function (locationId) {
        ac.fileError = false;
        if (ac.uploadFile.type === 'image/png' ||
            ac.uploadFile.type === 'image/gif' ||
            ac.uploadFile.type === 'image/jpeg') {
            adminService.uploadImage(ac.uploadFile)
            .then((r) => {
                $timeout(() => {
                    ac.selectLocationImage(locationId, r.newFilename);
                }, 1000);
            }, (e) => {
                console.error(e);
                ac.fileError = 'Sorry, there was some sort of error uploading the image.';
            });
        } else {
            console.error('file type not allowed');
            ac.fileError = 'Sorry, only .jpg, .jpeg, .gif, or .png files are allowed.';
        }
    };

    ac.deleteLocationItemWarning = function (locationId) {
        ac.deleteLocationPopup = true;
        ac.deleteLocationIndex = ac.getLocationIndex(locationId)
    };

    ac.deleteLocation = function () {
        if(ac.editedLocations[ac.deleteLocationIndex].newLocation) {
            ac.editedLocations.splice(ac.deleteLocationIndex, 1);
        } else {
            ac.editedLocations[ac.deleteLocationIndex].deleted = true;
        }
        ac.locationsChanged = true;
        ac.deleteLocationPopup = false;
        ac.deleteLocationIndex = -1;
    };

    ac.cancelDeleteLocation = function () {
        ac.deleteLocationPopup = false;
        ac.deleteLocationIndex = -1;
    };

    ac.saveLocationEdit = (module, locationId) => {
        let locationIndex = ac.getLocationIndex(locationId);
        ac.editedLocations[locationIndex][`${module}Open`] = false;
        ac.editedLocations[locationIndex][`${module}Changed`] = true;
        ac.locationsChanged = true;
    };

    ac.cancelLocationEdit = (module, locationId) => {
        let locationIndex = ac.getLocationIndex(locationId);
        if (ac.editedLocations[locationIndex].newLocation) {
            if(module === 'address') {
                ac.editedLocations[locationIndex].street1 = '';
                ac.editedLocations[locationIndex].street2 = '';
                ac.editedLocations[locationIndex].city = '';
                ac.editedLocations[locationIndex].state = '';
                ac.editedLocations[locationIndex].zip = '';
            } else {
                ac.editedLocations[locationIndex][module] = '';
            }
            ac.editedLocations[locationIndex][`${module}Open`] = false;
        } else {
            let originalLocationIndex = ac.getLocationIndex(locationId, true);
            ac.editedLocations[locationIndex][`${module}Open`] = false;
            ac.editedLocations[locationIndex][`${module}Changed`] = false;
            if(module === 'address') {
                ac.editedLocations[locationIndex].street1 = angular.copy(ac.locations[originalLocationIndex].street1);
                ac.editedLocations[locationIndex].street2 = angular.copy(ac.locations[originalLocationIndex].street2);
                ac.editedLocations[locationIndex].city = angular.copy(ac.locations[originalLocationIndex].city);
                ac.editedLocations[locationIndex].state = angular.copy(ac.locations[originalLocationIndex].state);
                ac.editedLocations[locationIndex].zip = angular.copy(ac.locations[originalLocationIndex].zip);
            } else {
                ac.editedLocations[locationIndex][module] = angular.copy(ac.locations[originalLocationIndex][module]);
            }
        }
    };

    ac.removeLocationImage = (locationId) => {
        let locationIndex = ac.getLocationIndex(locationId);
        ac.editedLocations[locationIndex].imageOpen = false;
        ac.editedLocations[locationIndex].image = '';
        ac.locationsChanged = true;
    };

    ac.submitLocationsEdit = () => {
        adminService.updateLocations(ac.editedLocations)
        .then(function () {
            $timeout(() => {
                ac.getLocations();
            }, 1000);
        }, function (e) {
            if(e.data.error) {
                ac[`${e.data.module}Error`] = e.data.status;
                ac.displayErrorPopup = true;
                console.error(e);
            }
            console.error(e);
        });
    };

    // Media
    ac.getMedia = function () {
        ac.newMediaId = 0;
        mediaService.getMedia()
        .then((media) => {
            ac.media = angular.copy(media);
            ac.editedMedia = angular.copy(media);

        });
    };

    ac.newMediaItem = function (categoryIndex) {
        ac.newMediaId++;
        ac.editedMedia[categoryIndex].items.push({
            id: `n${ac.newMediaId}`,
            title: 'New Item',
            filename: '',
            date: moment().format('YYYY-MM-DD HH:MM:SS'),
            newItem: true,
            uploading: false
        });
    };

    ac.selectMediaImage = function (index, newImage) {
        ac.editedMedia[index].image = newImage;
        ac.editedMedia[index].categoryChanged = true;
        ac.editedMedia[index].imageOpen = false;
    };

    ac.openMediaGallery = function (index) {
        ac.getImages();
        ac.editedMedia[index].imageOpen = true;
    };

    ac.changeGalleryPage = function (direction) {
        if(direction === 'prev') {
            ac.galleryPage = ac.galleryPage - 30;
        } else {
            ac.galleryPage = ac.galleryPage + 30;
        }
    };

    ac.gallerySearch = function () {
        ac.galleryPage = 0;
    };

    ac.uploadImage = function (categoryOrder) {
        ac.fileError = false;
        if (ac.uploadFile.type === 'image/png' ||
            ac.uploadFile.type === 'image/gif' ||
            ac.uploadFile.type === 'image/jpeg') {
            adminService.uploadImage(ac.uploadFile)
            .then((r) => {
                $timeout(() => {
                    ac.selectMediaImage(categoryOrder, r.newFilename);
                }, 1000);
            }, (e) => {
                console.error(e);
                ac.fileError = 'Sorry, there was some sort of error uploading the image.';
            });
        } else {
            console.error('file type not allowed');
            ac.fileError = 'Sorry, only .jpg, .jpeg, .gif, or .png files are allowed.';
        }
    };

    ac.deleteMediaItemWarning = function (categoryIndex, itemId) {
        ac.deleteMediaItemPopup = true;
        ac.deleteMediaItem = {
            categoryIndex: categoryIndex,
            itemIndex: ac.getMediaItemIndex(categoryIndex, itemId)
        };
    };

    ac.doDeleteMediaItem = function () {
        if(ac.editedMedia[ac.deleteMediaItem.categoryIndex].items[ac.deleteMediaItem.itemIndex].newItem) {
            ac.editedMedia[ac.deleteMediaItem.categoryIndex].items.splice(ac.deleteMediaItem.itemIndex, 1);
        } else {
            ac.editedMedia[ac.deleteMediaItem.categoryIndex].items[ac.deleteMediaItem.itemIndex].deleted = true;
        }
        ac.editedMedia[ac.deleteMediaItem.categoryIndex].categoryChanged = true;
        ac.deleteMediaItemPopup = false;
        ac.deleteMediaItem = {
            categoryIndex: -1,
            itemIndex: -1
        };
    };

    ac.cancelDeleteMediaItem = function () {
        ac.deleteMediaItemPopup = false;
        ac.deleteMediaItem = {
            categoryIndex: -1,
            itemIndex: -1
        };
    };

    ac.getMediaItemIndex = (categoryIndex, itemId, original = false) => {
        if (original) {
            for (let i=0; i < (ac.media[categoryIndex].items.length); i++) {
                if(ac.media[categoryIndex].items[i].id === itemId) {
                    return i;
                }
            }
        } else {
            for (let i=0; i < (ac.editedMedia[categoryIndex].items.length); i++) {
                if(ac.editedMedia[categoryIndex].items[i].id === itemId) {
                    return i;
                }
            }
        }
    };
    ac.cancelMediaEdit = (module, categoryIndex, itemId = false) => {
        if (itemId) {
            let itemIndex = ac.getMediaItemIndex(categoryIndex, itemId);
            if (ac.editedMedia[categoryIndex].items[itemIndex].newItem) {
                ac.editedMedia[categoryIndex].items[itemIndex][module] = '';
            } else {
                let originalItemIndex = ac.getMediaItemIndex(categoryIndex, itemId, true);
                ac.editedMedia[categoryIndex].items[itemIndex][`${module}Open`] = false;
                ac.editedMedia[categoryIndex].items[itemIndex][`${module}Changed`] = false;
                ac.editedMedia[categoryIndex].items[itemIndex][module] = angular.copy(ac.media[categoryIndex].items[originalItemIndex][module]);
            }
        } else {
            ac.editedMedia[categoryIndex][`${module}Open`] = false;
            ac.editedMedia[categoryIndex][`${module}Changed`] = true;
        }
        ac.editedMedia[categoryIndex].categoryChanged = true;
    };

    ac.saveMediaEdit = (module, categoryIndex, itemId = false) => {
        if (itemId) {
            let itemIndex = ac.getMediaItemIndex(categoryIndex, itemId);
            ac.editedMedia[categoryIndex].items[itemIndex][`${module}Open`] = false;
            ac.editedMedia[categoryIndex].items[itemIndex][`${module}Changed`] = true;
        } else {
            ac.editedMedia[categoryIndex][`${module}Open`] = false;
            ac.editedMedia[categoryIndex][`${module}Changed`] = true;
        }
        ac.editedMedia[categoryIndex].categoryChanged = true;
    };

    ac.uploadMediaFile = (categoryIndex, itemId) => {
        let itemIndex = ac.getMediaItemIndex(categoryIndex, itemId);
        ac.editedMedia[categoryIndex].items[itemIndex].fileError = '';
        if (ac.editedMedia[categoryIndex].items[itemIndex].newFile.type === 'application/pdf' ||
            ac.editedMedia[categoryIndex].items[itemIndex].newFile.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
            ac.editedMedia[categoryIndex].items[itemIndex].newFile.type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
            ac.editedMedia[categoryIndex].items[itemIndex].newFile.type === 'application/vnd.openxmlformats-officedocument.presentationml.presentation') {
            ac.editedMedia[categoryIndex].items[itemIndex].uploading = true;
            mediaService.uploadMediaFile(ac.editedMedia[categoryIndex].items[itemIndex].newFile)
            .then((r) => {
                ac.editedMedia[categoryIndex].items[itemIndex].filename = `${r.prefix}${ac.editedMedia[categoryIndex].items[itemIndex].newFile.name}`;
                ac.editedMedia[categoryIndex].categoryChanged = true;
                ac.editedMedia[categoryIndex].items[itemIndex].uploading = false;
                ac.editedMedia[categoryIndex].items[itemIndex].newFile = false;
            }, (e) => {
                console.error(e);
                ac.editedMedia[categoryIndex].items[itemIndex].uploading = false;
            });
        } else {
            console.error('file type not allowed');
            ac.editedMedia[categoryIndex].items[itemIndex].newFile = false;
            ac.editedMedia[categoryIndex].items[itemIndex].fileError = 'Sorry, only .pdf, .docx, .ppt, or .xlsx, files are allowed.';
        }
    };

    ac.submitMediaEdit = () => {
        mediaService.updateMedia(ac.editedMedia)
        .then(function () {
            $timeout(() => {
                ac.getMedia();
            }, 1000);
        }, function (e) {
            if(e.data.error) {
                ac[`${e.data.module}Error`] = e.data.status;
                ac.displayErrorPopup = true;
                console.error(e);
            }
            console.error(e);
        });
    };

    // Programs
    ac.getPrograms = function () {
        programService.getPrograms()
        .then(function (data) {
            ac.programs = data;
        }, function (e) {
            console.error('unable to fetch programs');
            console.error(e);
        });
    };

    ac.cancelDeleteForm = function () {
        ac.deleteFormPopup = false;
        ac.deletedForm = {
            title: false,
            url: false
        };
    };
    
    ac.deleteFormWarning = function (title, url) {
        ac.deleteFormPopup = true;
        ac.deletedForm = {
            title: title,
            url: url
        };
    };

    ac.doDeleteForm = function () {
        adminService.deleteForm(ac.deletedForm.url)
        .then(function (data) {
            ac.deleteFormPopup = false;
            ac.changeTab('forms');
        });
    };
}

