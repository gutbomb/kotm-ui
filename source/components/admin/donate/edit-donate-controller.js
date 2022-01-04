export default function ($scope, donateService, $rootScope, pageService, adminService, $location) {
    let edc = this;
    

    edc.getContent = function () {
        pageService.getPage(`/donate`).then( function(pageMeta) {
            $rootScope.meta = pageMeta;
        })
        .then(function () {
            donateService.getDonateContent()
            .then(function (data){
                edc.pageContent = angular.copy(data);
                edc.edited = angular.copy(data);
                edc.edited.meta = angular.copy($rootScope.meta);
                edc.donation.program = edc.edited.programs[0].title;
            }, function (error) {
                console.error('failed to retrieve donate page:');
                console.error(error);
            });
        });
    };

    edc.init = function () {
        $scope.Math = window.Math;
        edc.expYears = [];
        for (let i=(moment().format('YYYY')); i<moment().add(6, 'years').format('YYYY'); i++) {
            edc.expYears.push(i);
        }
        edc.donation = {
            amount: '',
            program: '',
            firstName: '',
            lastName: '',
            email: '',
            contactOptIn: false,
            address1: '',
            address2: '',
            city: '',
            state: '',
            zipcode: '',
            country: '',
            card: '',
            expMonth: '',
            expYear: '',
            cvc: ''
        };
        edc.descriptionCkeditorConfig = {
            bodyClass: 'edit-donation-header',
            contentsCss: '/css/styles.css',
            removePlugins: 'iframe,flash,smiley',
            removeButtons: 'Source,Styles',
            skin: 'moono',
            format_tags: 'p;h1;h2;h3',
            scayt_autoStartup: true,
            extraPlugins: 'youtube',
            removeDialogTabs: ''
        };
        edc.otherWaysCkeditorConfig = {
            bodyClass: 'edit-other-ways',
            contentsCss: '/css/styles.css',
            removePlugins: 'iframe,flash,smiley',
            removeButtons: 'Source,Styles',
            skin: 'moono',
            format_tags: 'p;h1;h2;h3',
            scayt_autoStartup: true,
            extraPlugins: 'youtube',
            removeDialogTabs: ''
        };
        edc.newProgramId = 0;
        edc.programOpen = false;
        edc.programsChanged = false;
        edc.headlineChanged = false;
        edc.headlineOpen = false;
        edc.subtitleChanged = false;
        edc.subtitleOpen = false;
        edc.descriptionChanged = false;
        edc.descriptionOpen = false;
        edc.programsChanged = false;
        edc.programsOpen = false;
        edc.boilerplateChanged = false;
        edc.boilerplateOpen = false;
        edc.buttonTextChanged = false;
        edc.buttonTextOpen = false;
        edc.otherWaysHeadlineChanged = false;
        edc.otherWaysHeadlineOpen = false;
        edc.otherWaysTextChanged = false;
        edc.otherWaysTextOpen = false;
        edc.metaDescriptionChanged = false;
        edc.metaDescriptionOpen = false;
        edc.metaTitleChanged = false;
        edc.metaTitleOpen = false;
        edc.metaKeywordsChanged = false;
        edc.metaKeywordsOpen = false;
        edc.deleteProgramPopup = false;
        edc.imageChanged = false;
        edc.imageOpen = false;
        edc.deleteProgramIndex = -1;
        edc.numProgramsDeleted = 0;
        edc.getContent();
    };

    edc.getProgramIndex = (id, original = false) => {
        if (original) {
            for (let i=0; i < (edc.pageContent.programs.length); i++) {
                if(edc.pageContent.programs[i].id === id) {
                    return i;
                }
            }
        } else {
            for (let i=0; i < (edc.edited.programs.length); i++) {
                if(edc.edited.programs[i].id === id) {
                    return i;
                }
            }
        }
    };

    edc.openProgram = function (id) {
        edc.programOpen = true;
        edc.edited.programs[edc.getProgramIndex(id)].programOpen = true;
    };

    edc.addProgram = function () {
        edc.newProgramId++;
        edc.programsChanged = true;
        let newProgramOrder = 0;
        for (let i=0; i < (edc.edited.programs.length); i++) {
            if(edc.edited.programs[i].programOrder > newProgramOrder) {
                newProgramOrder = edc.edited.programs[i].programOrder;
            }
        }
        newProgramOrder++;
        edc.edited.programs.push({
            id: `n${edc.newProgramId}`,
            title: 'New Program',
            programOrder: newProgramOrder,
            newProgram: true
        });
    };

    edc.cancelProgram = function (id) {
        edc.programOpen = false;
        let programIndex = edc.getProgramIndex(id);
        if (edc.edited.programs[programIndex].newProgram) {
            edc.edited.programs[programIndex].title = 'New Program';
        } else {
            edc.edited.programs[programIndex].title = angular.copy(edc.pageContent.programs[edc.getProgramIndex(id, true)].title);
        }
        edc.edited.programs[programIndex].programOpen = false;
    };

    edc.saveProgram = function (id) {
        edc.programOpen = false;
        edc.edited.programs[edc.getProgramIndex(id)].programOpen = false;
    };

    edc.moveProgram = function (direction, originalProgramOrder, id) {
        let swapProgramId = -1;
        edc.edited.programs.forEach(function (program) {
            if(direction === 'down') {
                if(program.programOrder === (originalProgramOrder + 1)) {
                    swapProgramId = program.id;
                }
            } else {
                if(program.programOrder === (originalProgramOrder - 1)) {
                    swapProgramId = program.id;
                }
            }
        });
        for (let i=0; i < (edc.edited.programs.length); i++) {
            if(direction === 'down') {
                if(edc.edited.programs[i].id === id) {
                    edc.edited.programs[i].programOrder++;
                }
                if(edc.edited.programs[i].id === swapProgramId) {
                    edc.edited.programs[i].programOrder--;
                }
            } else {
                if(edc.edited.programs[i].id === id) {
                    edc.edited.programs[i].programOrder--;
                }
                if(edc.edited.programs[i].id === swapProgramId) {
                    edc.edited.programs[i].programOrder++;
                }
            }
        }
        edc.programsChanged = true;
    };

    edc.cancelDeleteProgram = () => {
        edc.deleteProgramPopup = false;
        edc.deleteProgramIndex = -1;
    };

    edc.deleteProgramWarning = (id) => {
        edc.deleteProgramPopup = true;
        edc.deleteProgramIndex = edc.getProgramIndex(id);
    };

    edc.deleteProgram = () => {
        edc.numProgramsDeleted++;
        let deletedProgramOrder = edc.edited.programs[edc.deleteProgramIndex].programOrder
        if(edc.edited.programs[edc.deleteProgramIndex].newProgram) {
            edc.edited.programs.splice(edc.deleteProgramIndex, 1);
        } else {
            edc.edited.programs[edc.deleteProgramIndex].deleted = true;
        }
        for (let i=deletedProgramOrder; i<edc.edited.programs.length; i++) {
            edc.edited.programs[i].programOrder--;
        }
        edc.programsChanged = true;
        edc.deleteProgramPopup = false;
        edc.deleteProgramIndex = -1;
    };

    edc.revertEdit = (module) => {
        if (module === 'metaDescription') {
            edc.edited.meta.description = angular.copy($rootScope.meta.description);
        } else if (module === 'metaTitle') {
            edc.edited.meta.title = angular.copy($rootScope.meta.title);
        } else if (module === 'metaKeywords') {
            edc.edited.meta.keywords = angular.copy($rootScope.meta.keywords);
        } else {
            edc.edited[module] = angular.copy(edc.pageContent[module]);
            edc[`${module}Changed`] = false;
            edc[`${module}Open`] = false;
            edc[`${module}Error`] = null;
        }
    }

    edc.saveEdit= (module) => {
        if (module === 'metaDescription') {
            edc.metaDescriptionOpen = false;
            edc.metaDescriptionChanged = true;
        } else if (module === 'metaTitle') {
            edc.metaTitleOpen = false;
            edc.metaTitleChanged = true;
        } else if (module === 'metaKeywords') {
            edc.metaKeywordsOpen = false;
            edc.metaKeywordsChanged = true;
        } else {
            edc[`${module}Changed`] = true;
            edc[`${module}Open`] = false;
            edc[`${module}Error`] = null;
        }
    };

    edc.cancelEdit = function (module) {
        if (module === 'metaDescription') {
            edc.edited.meta.description = angular.copy($rootScope.meta.description);
            edc.metaDescriptionOpen = false;
            edc.metaDescriptionChanged = false;
        } else if (module === 'metaTitle') {
            edc.edited.meta.title = angular.copy($rootScope.meta.title);
            edc.metaTitleOpen = false;
            edc.metaTitleChanged = false;
        } else if (module === 'metaKeywords') {
            edc.edited.meta.keywords = angular.copy($rootScope.meta.keywords);
            edc.metaKeywordsOpen = false;
            edc.metaKeywordsChanged = false;
        } else {
            edc.edited[module] = angular.copy(edc.pageContent[module]);
            edc[`${module}Open`] = false;
            edc[`${module}Changed`] = false;
        }
    };

    edc.selectImage = function (newImage) {
        edc.edited.image = newImage;
        edc.imageChanged = true;
        edc.imageOpen = false;
    };

    edc.openGallery = function () {
        edc.galleryPage = 0;
        adminService.getImages()
        .then(function (data) {
            edc.images = data;
        }, function (e) {
            console.error(e);
        })
        .then(function () {
            edc.imageOpen = true;
        });
    };

    edc.changeGalleryPage = function (direction) {
        if(direction === 'prev') {
            edc.galleryPage = edc.galleryPage - 30;
        } else {
            edc.galleryPage = edc.galleryPage + 30;
        }
    };

    edc.gallerySearch = function () {
        edc.galleryPage = 0;
    };

    edc.uploadImage = function () {
        edc.fileError = false;
        if (edc.uploadFile.type === 'image/png' ||
            edc.uploadFile.type === 'image/gif' ||
            edc.uploadFile.type === 'image/jpeg') {
            adminService.uploadImage(edc.uploadFile)
            .then((r) => {
                edc.selectImage(r.newFilename)
            }, (e) => {
                console.error(e);
                edc.fileError = 'Sorry, there was some sort of error uploading the image.';
            });
        } else {
            console.error('file type not allowed');
            edc.fileError = 'Sorry, only .jpg, .jpeg, .gif, or .png files are allowed.';
        }
    };

    edc.submitEdit = () => {
        donateService.updateDonateContent(edc.edited)
        .then(function () {
            edc.init();
            $location.path('/admin/edit-donate');
        }, function (e) {
            if(e.data.error) {
                ebc[`${e.data.module}Error`] = e.data.status;
                edc.displayErrorPopup = true;
            }
            console.error(e);
        });
    };

    edc.init();
}