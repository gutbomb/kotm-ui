export default function ($location, boardService, $rootScope, $timeout, adminService, pageService, $scope) {
    let ebc = this;

    ebc.getBoard = function () {
        pageService.getPage(`/about/board`).then( function(pageMeta) {
            $rootScope.meta = pageMeta;
        })
        .then(function () {
            boardService.getBoard()
            .then(function (data){
                ebc.board = angular.copy(data);
                ebc.edited = angular.copy(data);
                ebc.edited.meta = {
                    title: angular.copy($rootScope.meta.title),
                    description: angular.copy($rootScope.meta.description),
                    keywords: angular.copy($rootScope.meta.keywords)
                };
                ebc.ready = true;
            }, function (error) {
                console.error('failed to retrieve board page:');
                console.error(error);
            });
        });
    };

    ebc.init = function () {
        $scope.Math = window.Math;
        ebc.getBoard();
        ebc.headlineChanged = false;
        ebc.headlineOpen = false;
        ebc.descriptionChanged = false;
        ebc.descriptionOpen = false;
        ebc.membersChanged = false;
        ebc.newMemberId = 0;
        ebc.deleteMemberPopup = false;
        ebc.deleteMemberIndex = -1;
        ebc.metaDescriptionChanged = false;
        ebc.metaTitleChanged = false;
        ebc.metaKeywordsChanged = false;
        ebc.metaDescriptionOpen = false;
        ebc.metaTitleOpen = false;
        ebc.metaKeywordsOpen = false;
        ebc.ckeditorConfig = {
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
        $timeout(function () {
            if ($rootScope.isLoggedIn) {
                if(!$rootScope.user.role === 'admin') {
                    $location.path('/about/board');
                }
            } else {
                $location.path('/login');
            }
        }, 5);
    }

    ebc.getMemberIndex = (memberId, original) => {
        if (original) {
            for (let i=0; i < (ebc.board.members.length); i++) {
                if(ebc.board.members[i].id === memberId) {
                    return i;
                }
            }
        } else {
            for (let i=0; i < (ebc.edited.members.length); i++) {
                if(ebc.edited.members[i].id === memberId) {
                    return i;
                }
            }
        }
    };

    ebc.revertEdit = (module) => {
        if(module === 'members') {
            for(let original=0; original < ebc.board.members.length; original++) {
                for(let edited=0; edited < ebc.edited.members.length; edited++) {
                    if(!ebc.edited.members[edited].newMember) {
                        if(ebc.board.members[original].id === ebc.edited.members[edited].id) {
                            ebc.edited.members[edited].title = angular.copy(ebc.board.members[original].title);
                            ebc.edited.members[edited].boardOrder = angular.copy(ebc.board.members[original].boardOrder);
                            ebc.edited.members[edited].deleted = false;
                            
                        }
                    } else {
                        ebc.edited.members.splice(edited, 1);
                    }
                }
            }
            ebc.membersChanged = false;
        } else if (module === 'metaDescription') {
            ebc.edited.meta.description = angular.copy($rootScope.meta.description);
        } else if (module === 'metaTitle') {
            ebc.edited.meta.title = angular.copy($rootScope.meta.title);
        } else if (module === 'metaKeywords') {
            ebc.edited.meta.keywords = angular.copy($rootScope.meta.keywords);
        } else {
            ebc.edited[module] = angular.copy(ebc.board[module]);
            ebc[`${module}Changed`] = false;
            ebc[`${module}Open`] = false;
            ebc[`${module}Error`] = null;
        }
    }

    ebc.saveEdit= (module, memberId = false) => {
        if (module === 'description' && memberId) {
            let memberIndex = ebc.getMemberIndex(memberId);
            ebc.edited.members[memberIndex].descriptionOpen = false;
            ebc.edited.members[memberIndex].descriptionChanged = true;
            ebc.membersChanged = true;
        } else if (module === 'name' && memberId) {
            let memberIndex = ebc.getMemberIndex(memberId);
            ebc.edited.members[memberIndex].nameOpen = false;
            ebc.edited.members[memberIndex].nameChanged = true;
            ebc.membersChanged = true;
        } else if (module === 'title' && memberId) {
            let memberIndex = ebc.getMemberIndex(memberId);
            ebc.edited.members[memberIndex].titleOpen = false;
            ebc.edited.members[memberIndex].titleChanged = true;
            ebc.membersChanged = true;
        } else if (module === 'subtitle' && memberId) {
            let memberIndex = ebc.getMemberIndex(memberId);
            ebc.edited.members[memberIndex].subtitleOpen = false;
            ebc.edited.members[memberIndex].subtitleChanged = true;
            ebc.membersChanged = true;
        } else if (module === 'metaDescription') {
            ebc.metaDescriptionOpen = false;
            ebc.metaDescriptionChanged = true;
        } else if (module === 'metaTitle') {
            ebc.metaTitleOpen = false;
            ebc.metaTitleChanged = true;
        } else if (module === 'metaKeywords') {
            ebc.metaKeywordsOpen = false;
            ebc.metaKeywordsChanged = true;
        } else {
            ebc[`${module}Changed`] = true;
            ebc[`${module}Open`] = false;
            ebc[`${module}Error`] = null;
        }
    };

    ebc.cancelEdit = function (module, memberId = false) {
        if (module === 'description' && memberId) {
            let memberIndex = ebc.getMemberIndex(memberId);
            let originalMemberIndex = ebc.getMemberIndex(memberId, true);
            if(!ebc.edited.members[memberIndex].newMember) {
                ebc.edited.members[memberIndex].description = angular.copy(ebc.board.members[originalMemberIndex].description);
            } else {
                ebc.edited.members[memberIndex].description = '';
            }
            ebc.edited.members[memberIndex].descriptionOpen = false;
            ebc.edited.members[memberIndex].descriptionChanged = false;
            ebc.membersChanged = true;
            
        } else if (module === 'name' && memberId) {
            let memberIndex = ebc.getMemberIndex(memberId);
            let originalMemberIndex = ebc.getMemberIndex(memberId, true);
            if(!ebc.edited.members[memberIndex].newMember) {
                ebc.edited.members[memberIndex].name = angular.copy(ebc.board.members[originalMemberIndex].name);
            } else {
                ebc.edited.members[memberIndex].name = 'New Member';
            }
            ebc.edited.members[memberIndex].nameOpen = false;
            ebc.edited.members[memberIndex].nameChanged = false;
            ebc.membersChanged = true;
        } else if (module === 'title' && memberId) {
            let memberIndex = ebc.getMemberIndex(memberId);
            let originalMemberIndex = ebc.getMemberIndex(memberId, true);
            if(!ebc.edited.members[memberIndex].newMember) {
                ebc.edited.members[memberIndex].title = angular.copy(ebc.board.members[originalMemberIndex].title);
            } else {
                ebc.edited.members[memberIndex].title = '';
            }
            ebc.edited.members[memberIndex].titleOpen = false;
            ebc.edited.members[memberIndex].titleChanged = false;
            ebc.membersChanged = true;
        } else if (module === 'subtitle' && memberId) {
            let memberIndex = ebc.getMemberIndex(memberId);
            let originalMemberIndex = ebc.getMemberIndex(memberId, true);
            if(!ebc.edited.members[memberIndex].newMember) {
                ebc.edited.members[memberIndex].subtitle = angular.copy(ebc.board.members[originalMemberIndex].subtitle);
            } else {
                ebc.edited.members[memberIndex].subtitle = '';
            }
            ebc.edited.members[memberIndex].subtitleOpen = false;
            ebc.edited.members[memberIndex].subtitleChanged = false;
            ebc.membersChanged = true;
        } else if (module === 'metaDescription') {
            ebc.edited.meta.description = angular.copy($rootScope.meta.description);
            ebc.metaDescriptionOpen = false;
            ebc.metaDescriptionChanged = false;
        } else if (module === 'metaTitle') {
            ebc.edited.meta.title = angular.copy($rootScope.meta.title);
            ebc.metaTitleOpen = false;
            ebc.metaTitleChanged = false;
        } else if (module === 'metaKeywords') {
            ebc.edited.meta.keywords = angular.copy($rootScope.meta.keywords);
            ebc.metaKeywordsOpen = false;
            ebc.metaKeywordsChanged = false;
        } else {
            ebc.edited[module] = angular.copy(ebc.board[module]);
            ebc[`${module}Open`] = false;
            ebc[`${module}Changed`] = false;
        }
    };

    ebc.selectImage = function (memberId, newImage) {
        let memberIndex = ebc.getMemberIndex(memberId);
        ebc.edited.members[memberIndex].image = newImage;
        ebc.membersChanged = true;
        ebc.edited.members[memberIndex].imageOpen = false;
    };

    ebc.openGallery = function (memberId) {
        ebc.galleryPage = 0;
        let memberIndex = ebc.getMemberIndex(memberId);
        adminService.getImages()
        .then(function (data) {
            ebc.images = data;
        }, function (e) {
            console.error(e);
        })
        .then(function () {
            ebc.edited.members[memberIndex].imageOpen = true;
        });
    };

    ebc.changeGalleryPage = function (direction) {
        if(direction === 'prev') {
            ebc.galleryPage = ebc.galleryPage - 30;
        } else {
            ebc.galleryPage = ebc.galleryPage + 30;
        }
    };

    ebc.gallerySearch = function () {
        ebc.galleryPage = 0;
    };

    ebc.uploadImage = function (memberId) {
        ebc.fileError = false;
        if (ebc.uploadFile.type === 'image/png' ||
            ebc.uploadFile.type === 'image/gif' ||
            ebc.uploadFile.type === 'image/jpeg') {
            adminService.uploadImage(ebc.uploadFile)
            .then((r) => {
                ebc.selectImage(memberId, r.newFilename)
            }, (e) => {
                console.error(e);
                ebc.fileError = 'Sorry, there was some sort of error uploading the image.';
            });
        } else {
            console.error('file type not allowed');
            ebc.fileError = 'Sorry, only .jpg, .jpeg, .gif, or .png files are allowed.';
        }
    };

    ebc.moveMember = function (direction, originalMemberOrder, id) {
        let swapMemberId = -1;
        ebc.edited.members.forEach(function (member) {
            if(direction === 'down') {
                if(member.boardOrder === (originalMemberOrder + 1)) {
                    swapMemberId = member.id;
                }
            } else {
                if(member.boardOrder === (originalMemberOrder - 1)) {
                    swapMemberId = member.id;
                }
            }
        });
        for (let i=0; i < (ebc.edited.members.length); i++) {
            if(direction === 'down') {
                if(ebc.edited.members[i].id === id) {
                    ebc.edited.members[i].boardOrder++;
                }
                if(ebc.edited.members[i].id === swapMemberId) {
                    ebc.edited.members[i].boardOrder--;
                }
            } else {
                if(ebc.edited.members[i].id === id) {
                    ebc.edited.members[i].boardOrder--;
                }
                if(ebc.edited.members[i].id === swapMemberId) {
                    ebc.edited.members[i].boardOrder++;
                }
            }
        }
        ebc.membersChanged = true;
    };

    ebc.addMember = () => {
        ebc.newMemberId++;
        let newBoardOrder = 0;
        for (let i=0; i < (ebc.edited.members.length); i++) {
            if(ebc.edited.members[i].boardOrder > newBoardOrder) {
                newBoardOrder = ebc.edited.members[i].boardOrder;
            }
        }
        newBoardOrder++;
        ebc.edited.members.push({
            newMember: true,
            title: '',
            subtitle: '',
            name: 'New Member',
            description: '',
            boardOrder: newBoardOrder,
            id: `n${ebc.newMemberId}`,
            image: ''
        });
        ebc.membersChanged = true;
    };

    ebc.cancelDeleteMember = () => {
        ebc.deleteMemberPopup = false;
        ebc.deleteMemberIndex = -1;
    };

    ebc.deleteMemberWarning = (id) => {
        ebc.deleteMemberPopup = true;
        ebc.deleteMemberIndex = ebc.getMemberIndex(id);
    };

    ebc.deleteMember = () => {
        if(ebc.edited.members[ebc.deleteMemberIndex].newMember) {
            ebc.edited.members.splice(ebc.deleteMemberIndex, 1);
        } else {
            ebc.edited.members[ebc.deleteMemberIndex].deleted = true;
        }
        ebc.membersChanged = true;
        ebc.deleteMemberPopup = false;
        ebc.deleteMemberIndex = -1;
    };

    ebc.submitEdit = () => {
        boardService.updateBoard(ebc.edited)
        .then(function () {
            ebc.init();
            $location.path('/admin/edit-board');
        }, function (e) {
            if(e.data.error) {
                ebc[`${e.data.module}Error`] = e.data.status;
                ebc.displayErrorPopup = true;
            }
            console.error(e);
        });
    };

    ebc.init();
};