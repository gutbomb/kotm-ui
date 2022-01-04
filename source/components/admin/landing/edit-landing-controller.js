export default function ($routeParams, $location, landingService, adminService, $rootScope, pageService, $timeout, $scope) {
    let elc = this;

    elc.getLanding = function () {
        pageService.getPage(`/${$routeParams.link}`).then( function(pageMeta) {
            $rootScope.meta = pageMeta;
        })
        .then(function () {
            landingService.getLanding($routeParams.link)
            .then(function (data){
                elc.landing = angular.copy(data);
                elc.edited = angular.copy(data);
                ;
                elc.edited.meta = {
                    title: angular.copy($rootScope.meta.title),
                    description: angular.copy($rootScope.meta.description),
                    keywords: angular.copy($rootScope.meta.keywords)
                };
                elc.getFaqs();
            }, function (error) {
                console.error('failed to retrieve landing page:');
                console.error(error);
            })
        })
    };

    elc.init = function () {
        $scope.Math = window.Math;
        elc.getLanding();
        elc.newTabID = 0;
        elc.questionsChanged = false;
        elc.questionsOpen = false;
        elc.sectionsChanged = false;
        elc.sectionsOpen = false;
        elc.deleteSectionPopup = false;
        elc.deleteSectionIndex = -1;
        elc.deleteBulletPopup = false;
        elc.deleteBulletIndex = -1;
        elc.deleteBulletSectionIndex = -1;
        elc.newSectionID = 0;
        elc.headlineOpen = false;
        elc.headlineChanged = false;
        elc.questionsChanged = false;
        elc.questionsOpen = false;
        elc.pageDescriptionOpen = false;
        elc.pageDescriptionChanged = false;
        elc.metaDescriptionChanged = false;
        elc.metaTitleChanged = false;
        elc.metaKeywordsChanged = false;
        elc.metaDescriptionOpen = false;
        elc.metaTitleOpen = false;
        elc.metaKeywordsOpen = false;
        elc.ckeditorConfig = {
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
    };

    elc.getSectionIndex = (sectionId) => {
        for (let i=0; i < (elc.edited.sections.length); i++) {
            if(elc.edited.sections[i].id === sectionId) {
                return i;
            }
        }
    };

    elc.getBulletIndex = (sectionIndex, bulletId) => {
        for (let i=0; i < (elc.edited.sections[sectionIndex].bullets.length); i++) {
            if(elc.edited.sections[sectionIndex].bullets[i].id === bulletId) {
                return i;
            }
        }
    };

    elc.getOriginalSectionIndex = (sectionId) => {
        for (let i=0; i < (elc.landing.sections.length); i++) {
            if(elc.landing.sections[i].id === sectionId) {
                return i;
            }
        }
    }

    elc.getOriginalBulletIndex = (originalSectionIndex, bulletId) => {
        for (let i=0; i < (elc.landing.sections[originalSectionIndex].bullets.length); i++) {
            if(elc.landing.sections[originalSectionIndex].bullets[i].id === bulletId) {
                return i;
            }
        }
    }

    elc.selectImage = function (sectionId, newImage) {
        let sectionIndex = elc.getSectionIndex(sectionId);
        elc.edited.sections[sectionIndex].image = newImage;
        elc.sectionsChanged = true;
        elc.edited.sections[sectionIndex].imageOpen = false;
    };

    elc.openGallery = function (sectionId) {
        elc.galleryPage = 0;
        let sectionIndex = elc.getSectionIndex(sectionId);
        adminService.getImages()
        .then(function (data) {
            elc.images = data;
        }, function (e) {
            console.error(e);
        })
        .then(function () {
            elc.edited.sections[sectionIndex].imageOpen = true;
        });
    };

    elc.changeGalleryPage = function (direction) {
        if(direction === 'prev') {
            elc.galleryPage = elc.galleryPage - 30;
        } else {
            elc.galleryPage = elc.galleryPage + 30;
        }
    };

    elc.gallerySearch = function () {
        elc.galleryPage = 0;
    };

    elc.uploadImage = function (sectionId) {
        elc.fileError = false;
        if (elc.uploadFile.type === 'image/png' ||
            elc.uploadFile.type === 'image/gif' ||
            elc.uploadFile.type === 'image/jpeg') {
            adminService.uploadImage(elc.uploadFile)
            .then((r) => {
                elc.selectImage(sectionId, r.newFilename)
            }, (e) => {
                console.error(e);
                elc.fileError = 'Sorry, there was some sort of error uploading the image.';
            });
        } else {
            console.error('file type not allowed');
            elc.fileError = 'Sorry, only .jpg, .jpeg, .gif, or .png files are allowed.';
        }
    };

    elc.moveSection = function (direction, originalSectionOrder, id) {
        let swapSectionId = -1;
        elc.edited.sections.forEach(function (section) {
            if(direction === 'down') {
                if(section.sectionOrder === (originalSectionOrder + 1)) {
                    swapSectionId = section.id;
                }
            } else {
                if(section.sectionOrder === (originalSectionOrder - 1)) {
                    swapSectionId = section.id;
                }
            }
        });
        for (let i=0; i < (elc.edited.sections.length); i++) {
            if(direction === 'down') {
                if(elc.edited.sections[i].id === id) {
                    elc.edited.sections[i].sectionOrder++;
                }
                if(elc.edited.sections[i].id === swapSectionId) {
                    elc.edited.sections[i].sectionOrder--;
                }
            } else {
                if(elc.edited.sections[i].id === id) {
                    elc.edited.sections[i].sectionOrder--;
                }
                if(elc.edited.sections[i].id === swapSectionId) {
                    elc.edited.sections[i].sectionOrder++;
                }
            }
        }
        elc.sectionsChanged = true;
    };

    elc.moveBullet = function (direction, originalSectionOrder, tabId, sectionId) {
        let swapBulletId = -1;
        let sectionIndex = elc.getSectionIndex(sectionId);
        elc.edited.sections[sectionIndex].bullets.forEach(function (bullet) {
            if(direction === 'down') {
                if(bullet.bulletOrder === (originalBulletOrder + 1)) {
                    swapBulletId = bullet.id;
                }
            } else {
                if(bullet.bulletOrder === (originalBulletOrder - 1)) {
                    swapBulletId = bullet.id;
                }
            }
        });
        for (let i=0; i < (elc.edited.sections[sectionIndex].sections.length); i++) {
            if(direction === 'down') {
                if(elc.edited.sections[sectionIndex].bullets[i].id === bulletId) {
                    elc.edited.sections[sectionIndex].bullets[i].bulletOrder++;
                }
                if(elc.edited.sections[sectionIndex].bullets[i].id === swapBulletId) {
                    elc.edited.sections[sectionIndex].bullets[i].bulletOrder--;
                }
            } else {
                if(elc.edited.sections[sectionIndex].bullets[i].id === bulletId) {
                    elc.edited.sections[sectionIndex].bullets[i].bulletOrder--;
                }
                if(elc.edited.sections[sectionIndex].bullets[i].id === swapBulletId) {
                    elc.edited.sections[sectionIndex].bullets[i].bulletOrder++;
                }
            }
        }
        elc.sectionsChanged = true;
    };

    elc.revertEdit = (module, sectionId = false, bulletId = false) => {
        if(module === 'sections') {
            for(let original=0; original < elc.landing.sections.length; original++) {
                for(let edited=0; edited < elc.edited.sections.length; edited++) {
                    if(!elc.edited.sections[edited].newSection) {
                        if(elc.landing.sections[original].id === elc.edited.sections[edited].id) {
                            elc.edited.sections[edited].title = angular.copy(elc.landing.sections[original].title);
                            elc.edited.sections[edited].sectionOrder = angular.copy(elc.landing.sections[original].sectionOrder);
                            elc.edited.sections[edited].deleted = false;
                            
                        }
                    } else {
                        elc.edited.sections.splice(edited, 1);
                    }
                }
            }
            elc.sectionsChanged = false;
        } else if (module === 'bullets' && sectionId) {
            let sectionIndex = elc.getSectionIndex(sectionId);
            for(let original=0; original < elc.program.sections[sectionIndex].bullets.length; original++) {
                for(let edited=0; edited < elc.edited.sections[sectionIndex].bullets.length; edited++) {
                    if(!elc.edited.sections[sectionIndex].bullets[edited].newBullet) {
                        if(elc.program.sections[sectionIndex].bullets[original].id === elc.edited.sections[sectionIndex].bullets[edited].id) {
                            elc.edited.sections[sectionIndex].bullets[edited].title = angular.copy(elc.landing.sections[sectionIndex].bullets[original].title);
                            elc.edited.sections[sectionIndex].bullets[edited].bulletOrder = angular.copy(elc.landing.sections[sectionIndex].bullets[original].bulletOrder);
                            elc.edited.sections[sectionIndex].bullets[edited].deleted = false;
                            
                        }
                    } else {
                        elc.edited.sections[sectionIndex].bullets.splice(edited, 1);
                    }
                }
            }
            elc.bulletsChanged = false;
        } else if (module === 'metaDescription') {
            elc.edited.meta.description = angular.copy($rootScope.meta.description);
        } else if (module === 'metaTitle') {
            elc.edited.meta.title = angular.copy($rootScope.meta.title);
        } else if (module === 'metaKeywords') {
            elc.edited.meta.keywords = angular.copy($rootScope.meta.keywords);
        } else {
            elc.edited[module] = angular.copy(elc.landing[module]);
            elc[`${module}Changed`] = false;
            elc[`${module}Open`] = false;
            elc[`${module}Error`] = null;
        }
    }

    elc.saveEdit= (module, sectionId = false, bulletId = false) => {
        if (module === 'description' && sectionId && bulletId) {
            let sectionIndex = elc.getSectionIndex(sectionId);
            let bulletIndex = elc.getBulletIndex(sectionIndex, bulletId);
            elc.edited.sections[sectionIndex].bullets[bulletIndex].descriptionOpen = false;
            elc.edited.sections[sectionIndex].bullets[bulletIndex].descriptionChanged = true;
            elc.sectionsChanged = true;
        } else if (module === 'title' && sectionId && bulletId === false) {
            let sectionIndex = elc.getSectionIndex(sectionId);
            elc.edited.sections[sectionIndex].titleOpen = false;
            elc.edited.sections[sectionIndex].titleChanged = true;
            elc.sectionsChanged = true;
        } else if (module === 'title' && sectionId && bulletId) {
            let sectionIndex = elc.getSectionIndex(sectionId);
            let bulletIndex = elc.getBulletIndex(sectionIndex, bulletId);
            elc.edited.sections[sectionIndex].bullets[bulletIndex].titleOpen = false;
            elc.edited.sections[sectionIndex].bullets[bulletIndex].titleChanged = true;
            elc.sectionsChanged = true;
        } else if (module === 'link' && sectionId && bulletId) {
            let sectionIndex = elc.getSectionIndex(sectionId);
            let bulletIndex = elc.getBulletIndex(sectionIndex, bulletId);
            elc.edited.sections[sectionIndex].bullets[bulletIndex].linkOpen = false;
            elc.edited.sections[sectionIndex].bullets[bulletIndex].linkChanged = true;
            elc.sectionsChanged = true;
        } else if (module === 'question' && sectionId) {
            let questionIndex = elc.getQuestionIndex(sectionId);
            elc.edited.questions[questionIndex].questionOpen = false;
            elc.edited.questions[questionIndex].questionChanged = true;
            elc.questionsChanged = true;
        } else if (module === 'metaDescription') {
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

    elc.cancelEdit = function (module, sectionId = false, bulletId = false) {
        if (module === 'description' && sectionId && bulletId) {
            let sectionIndex = elc.getSectionIndex(sectionId);
            let bulletIndex = elc.getBulletIndex(sectionIndex, bulletId);
            elc.edited.sections[sectionIndex].bullets[bulletIndex].description = angular.copy(elc.landing.sections[sectionIndex].bullets[bulletIndex].description);
            elc.edited.sections[sectionIndex].bullets[bulletIndex].descriptionOpen = false;
        } else if (module === 'title' && sectionId && bulletId === false) {
            let sectionIndex = elc.getSectionIndex(sectionId);
            elc.edited.sections[sectionIndex].title = angular.copy(elc.landing.sections[sectionIndex].title);
            elc.edited.sections[sectionIndex].titleOpen = false;
            elc.edited.sections[sectionIndex].titleChanged = false;
        } else if (module === 'title' && sectionId && bulletId) {
            let sectionIndex = elc.getSectionIndex(sectionId);
            let bulletIndex = elc.getBulletIndex(sectionIndex, bulletId);
            elc.edited.sections[sectionIndex].bullets[bulletIndex].title = angular.copy(elc.landing.sections[sectionIndex].bullets[bulletIndex].title);
            elc.edited.sections[sectionIndex].bullets[bulletIndex].titleOpen = false;
            elc.edited.sections[sectionIndex].bullets[bulletIndex].titleChanged = false;
        } else if (module === 'icon' && sectionId && bulletId) {
            let sectionIndex = elc.getSectionIndex(sectionId);
            let bulletIndex = elc.getBulletIndex(sectionIndex, bulletId);
            elc.edited.sections[sectionIndex].bullets[bulletIndex].icon = angular.copy(elc.landing.sections[sectionIndex].bullets[bulletIndex].icon);
            elc.edited.sections[sectionIndex].bullets[bulletIndex].iconOpen = false;
            elc.edited.sections[sectionIndex].bullets[bulletIndex].iconChanged = false;
        } else if (module === 'link' && sectionId && bulletId) {
            let sectionIndex = elc.getSectionIndex(sectionId);
            let bulletIndex = elc.getBulletIndex(sectionIndex, bulletId);
            if(!elc.edited.sections[sectionIndex].bullets[bulletIndex].newBullet && !elc.edited.sections[sectionIndex].newSection) {
                let originalSectionIndex = elc.getOriginalSectionIndex(sectionId);
                let originalBulletIndex = elc.getOriginalBulletIndex(originalSectionIndex, bulletId);
                elc.edited.sections[sectionIndex].bullets[bulletIndex].link = angular.copy(elc.landing.sections[originalSectionIndex].bullets[originalBulletIndex].link);
                elc.edited.sections[sectionIndex].bullets[bulletIndex].linkText = angular.copy(elc.landing.sections[originalSectionIndex].bullets[originalBulletIndex].linkText);
            } else {
                elc.edited.sections[sectionIndex].bullets[bulletIndex].link = '';
                elc.edited.sections[sectionIndex].bullets[bulletIndex].linkText = '';
            }
            
            elc.edited.sections[sectionIndex].bullets[bulletIndex].linkOpen = false;
            
            
        } else if (module === 'question' && sectionId) {
            let questionIndex = elc.getQuestionIndex(sectionId);
            if(!elc.edited.questions[questionIndex].questionNew) {
                elc.edited.questions[questionIndex].question = angular.copy(elc.landing.questions[questionIndex].question);
                elc.edited.questions[questionIndex].answer = angular.copy(elc.landing.questions[questionIndex].answer);
                elc.edited.questions[questionIndex].questionOpen = false;
                elc.edited.questions[questionIndex].questionChanged = false;
            }
        } else if (module === 'metaDescription') {
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
            elc.edited[module] = angular.copy(elc.landing[module]);
            elc[`${module}Open`] = false;
        }
    };

    elc.chooseIcon = function (icon, sectionId, bulletId) {
        let sectionIndex = elc.getSectionIndex(sectionId);
        let bulletIndex = elc.getBulletIndex(sectionIndex, bulletId);
        elc.edited.sections[sectionIndex].bullets[bulletIndex].icon = icon;
        elc.sectionsChanged = true;
        elc.edited.sections[sectionIndex].bullets[bulletIndex].iconOpen = false;
    };

    elc.getFaqs = function () {
        landingService.getFaqs(elc.landing.id)
        .then(function (data){
            elc.landing.questions = angular.copy(data);
            elc.edited.questions = angular.copy(data);
        }, function (error) {
            console.error('failed to retrieve landing page questions:');
            console.error(error);
        })
    }

    elc.getQuestionIndex = (id) => {
        for (let i=0; i < (elc.edited.questions.length); i++) {
            if(elc.edited.questions[i].id === id) {
                return i;
            }
        }
    };

    elc.moveQuestion = function (direction, originalQuestionOrder, id) {
        let swapQuestionId = -1;
        elc.edited.questions.forEach(function (question) {
            if(direction === 'down') {
                if(question.questionOrder === (originalQuestionOrder + 1)) {
                    swapQuestionId = question.id;
                }
            } else {
                if(question.questionOrder === (originalQuestionOrder - 1)) {
                    swapQuestionId = question.id;
                }
            }
        });
        for (let i=0; i < (elc.edited.questions.length); i++) {
            if(direction === 'down') {
                if(elc.edited.questions[i].id === id) {
                    elc.edited.questions[i].questionOrder++;
                }
                if(elc.edited.questions[i].id === swapQuestionId) {
                    elc.edited.questions[i].questionOrder--;
                }
            } else {
                if(elc.edited.questions[i].id === id) {
                    elc.edited.questions[i].questionOrder--;
                }
                if(elc.edited.questions[i].id === swapQuestionId) {
                    elc.edited.questions[i].questionOrder++;
                }
            }
        }
        elc.questionsChanged = true;
    };

    elc.addNewQuestion = () => {
        elc.newTabID++;
        let newQuestionOrder = -1;
        for (let i=0; i < (elc.edited.questions.length); i++) {
            if(elc.edited.questions[i].questionOrder > newQuestionOrder) {
                newQuestionOrder = elc.edited.questions[i].questionOrder;
            }
        }
        newQuestionOrder++;
        elc.edited.questions.push({
            id: `n${elc.newTabID}`,
            question: 'New Question',
            answer: 'New Answer',
            questionOrder: newQuestionOrder,
            newQuestion: true
        });
        elc.questionsChanged;
    };

    elc.deleteQuestion = (id) => {
        let questionIndex = elc.getQuestionIndex(id);
        for (let i=0; i < (elc.edited.questions.length); i++) {
            if(elc.edited.questions[i].questionOrder>elc.edited.questions[questionIndex].questionOrder) {
                elc.edited.questions[i].questionOrder--;
            }
        }
        if (elc.edited.questions[questionIndex].questionNew) {
            elc.edited.questions.splice(questionIndex, 1);
        } else {
            elc.edited.questions[questionIndex].deleted = true;
        }
        elc.questionsChanged = true;
    };

    elc.addSection = () => {
        elc.newTabID++;
        let newSectionOrder = 0;
        for (let i=0; i < (elc.edited.sections.length); i++) {
            if(elc.edited.sections[i].sectionOrder > newSectionOrder) {
                newSectionOrder = elc.edited.sections[i].sectionOrder;
            }
        }
        newSectionOrder++;
        elc.edited.sections.push({
            newSection: true,
            title: 'New Section',
            sectionOrder: newSectionOrder,
            id: `n${elc.newTabID}`,
            image: null,
            bullets: []
        });
        elc.sectionsChanged = true;
    };

    elc.deleteSectionWarning = (id) => {
        elc.deleteSectionPopup = true;
        elc.deleteSectionIndex = elc.getSectionIndex(id);
    };

    elc.deleteSection = () => {
        if(elc.edited.sections[elc.deleteSectionIndex].newSection) {
            elc.edited.sections.splice(elc.deleteSectionIndex, 1);
        } else {
            elc.edited.sections[elc.deleteSectionIndex].deleted = true;
        }
        elc.sectionsChanged = true;
        elc.deleteSectionPopup = false;
        elc.deleteSectionIndex = -1;
    };

    elc.deleteBulletWarning = (sectionId, bulletId) => {
        elc.deleteBulletPopup = true;
        elc.deleteBulletSectionIndex = elc.getSectionIndex(sectionId)
        elc.deleteBulletIndex = elc.getBulletIndex(elc.deleteBulletSectionIndex, bulletId);
    };

    elc.deleteBullet = () => {
        if(elc.edited.sections[elc.deleteBulletSectionIndex].bullets[elc.deleteBulletIndex].newBullet) {
            elc.edited.sections[elc.deleteBulletSectionIndex].bullets.splice(elc.deleteBulletIndex, 1);
        } else {
            elc.edited.sections[elc.deleteBulletSectionIndex].bullets[elc.deleteBulletIndex].deleted = true;
        }
        elc.sectionsChanged = true;
        elc.deleteBulletPopup = false;
        elc.deleteBulletIndex = -1;
        elc.deleteBulletSectionIndex = -1;
    };

    elc.cancelDeleteSection = () => {
        elc.deleteSectionPopup = false;
        elc.deleteSectionIndex = -1;
    };

    elc.cancelDeleteBullet = () => {
        elc.deleteBulletPopup = false;
        elc.deleteBulletIndex = -1;
        elc.deleteBulletSectionIndex = -1;
    };

    elc.createnewBullet = (sectionId) => {
        elc.newTabID++;
        let sectionIndex = elc.getSectionIndex(sectionId);
        let newBulletOrder = 0;
        for (let i=0; i < (elc.edited.sections[sectionIndex].bullets.length); i++) {
            if(elc.edited.sections[sectionIndex].bullets[i].bulletOrder > newBulletOrder) {
                newBulletOrder = elc.edited.sections[sectionIndex].bullets[i].bulletOrder;
            }
        }
        newBulletOrder++;
        elc.edited.sections[sectionIndex].bullets.push({
            newBullet: true,
            title: 'New Bullet',
            bulletOrder: newBulletOrder,
            id: `n${elc.newTabID}`,
            link: '',
            linkText: '',
            icon: 'fas fa-tractor',
            description: ''
        });
        elc.sectionsChanged = true;
    };

    elc.submitEdit = () => {
        landingService.updateLanding(elc.edited.id, elc.edited)
        .then(function () {
            $location.path(`/admin/edit-landing/${elc.edited.link}`);
            elc.init();
        }, function (e) {
            if(e.data.error) {
                elc[`${e.data.module}Error`] = e.data.status;
                elc.displayErrorPopup = true;
            }
            console.error(e);
        });
    };

    elc.init();

    elc.icons = [
        'fas icon-donate',
        'fas icon-volunteer',
        'fas fa-desktop',
        'fas fa-money-check-alt',
        'fas fa-phone-alt',
        'fas fa-calendar-alt',
        'fas fa-drum',
        'fas fa-hands',
        'fas fa-ad',
        'fas fa-address-book',
        'far fa-address-book',
        'fas fa-address-card',
        'far fa-address-card',
        'fas fa-adjust',
        'fas fa-air-freshener',
        'fas fa-align-center',
        'fas fa-align-justify',
        'fas fa-align-left',
        'fas fa-align-right',
        'fas fa-allergies',
        'fas fa-ambulance',
        'fas fa-american-sign-language-interpreting',
        'fas fa-anchor',
        'fas fa-angle-double-down',
        'fas fa-angle-double-left',
        'fas fa-angle-double-right',
        'fas fa-angle-double-up',
        'fas fa-angle-down',
        'fas fa-angle-left',
        'fas fa-angle-right',
        'fas fa-angle-up',
        'fas fa-angry',
        'far fa-angry',
        'fas fa-ankh',
        'fas fa-apple-alt',
        'fas fa-archive',
        'fas fa-archway',
        'fas fa-arrow-alt-circle-down',
        'far fa-arrow-alt-circle-down',
        'fas fa-arrow-alt-circle-left',
        'far fa-arrow-alt-circle-left',
        'fas fa-arrow-alt-circle-right',
        'far fa-arrow-alt-circle-right',
        'fas fa-arrow-alt-circle-up',
        'far fa-arrow-alt-circle-up',
        'fas fa-arrow-circle-down',
        'fas fa-arrow-circle-left',
        'fas fa-arrow-circle-right',
        'fas fa-arrow-circle-up',
        'fas fa-arrow-down',
        'fas fa-arrow-left',
        'fas fa-arrow-right',
        'fas fa-arrow-up',
        'fas fa-arrows-alt',
        'fas fa-arrows-alt-h',
        'fas fa-arrows-alt-v',
        'fas fa-assistive-listening-systems',
        'fas fa-asterisk',
        'fas fa-at',
        'fas fa-atlas',
        'fas fa-atom',
        'fas fa-audio-description',
        'fas fa-award',
        'fas fa-baby',
        'fas fa-baby-carriage',
        'fas fa-backspace',
        'fas fa-backward',
        'fas fa-bacon',
        'fas fa-bahai',
        'fas fa-balance-scale',
        'fas fa-balance-scale-left',
        'fas fa-balance-scale-right',
        'fas fa-ban',
        'fas fa-band-aid',
        'fas fa-barcode',
        'fas fa-bars',
        'fas fa-baseball-ball',
        'fas fa-basketball-ball',
        'fas fa-bath',
        'fas fa-battery-empty',
        'fas fa-battery-full',
        'fas fa-battery-half',
        'fas fa-battery-quarter',
        'fas fa-battery-three-quarters',
        'fas fa-bed',
        'fas fa-beer',
        'fas fa-bell',
        'far fa-bell',
        'fas fa-bell-slash',
        'far fa-bell-slash',
        'fas fa-bezier-curve',
        'fas fa-bible',
        'fas fa-bicycle',
        'fas fa-biking',
        'fas fa-binoculars',
        'fas fa-biohazard',
        'fas fa-birthday-cake',
        'fas fa-blender',
        'fas fa-blender-phone',
        'fas fa-blind',
        'fas fa-blog',
        'fas fa-bold',
        'fas fa-bolt',
        'fas fa-bomb',
        'fas fa-bone',
        'fas fa-bong',
        'fas fa-book',
        'fas fa-book-dead',
        'fas fa-book-medical',
        'fas fa-book-open',
        'fas fa-book-reader',
        'fas fa-bookmark',
        'far fa-bookmark',
        'fas fa-border-all',
        'fas fa-border-none',
        'fas fa-border-style',
        'fas fa-bowling-ball',
        'fas fa-box',
        'fas fa-box-open',
        'fas fa-box-tissue',
        'fas fa-boxes',
        'fas fa-braille',
        'fas fa-brain',
        'fas fa-bread-slice',
        'fas fa-briefcase',
        'fas fa-briefcase-medical',
        'fas fa-broadcast-tower',
        'fas fa-broom',
        'fas fa-brush',
        'fas fa-bug',
        'fas fa-building',
        'far fa-building',
        'fas fa-bullhorn',
        'fas fa-bullseye',
        'fas fa-burn',
        'fas fa-bus',
        'fas fa-bus-alt',
        'fas fa-business-time',
        'fas fa-calculator',
        'fas fa-calendar',
        'far fa-calendar',
        'far fa-calendar-alt',
        'fas fa-calendar-check',
        'far fa-calendar-check',
        'fas fa-calendar-day',
        'fas fa-calendar-minus',
        'far fa-calendar-minus',
        'fas fa-calendar-plus',
        'far fa-calendar-plus',
        'fas fa-calendar-times',
        'far fa-calendar-times',
        'fas fa-calendar-week',
        'fas fa-camera',
        'fas fa-camera-retro',
        'fas fa-campground',
        'fas fa-candy-cane',
        'fas fa-cannabis',
        'fas fa-capsules',
        'fas fa-car',
        'fas fa-car-alt',
        'fas fa-car-battery',
        'fas fa-car-crash',
        'fas fa-car-side',
        'fas fa-caravan',
        'fas fa-caret-down',
        'fas fa-caret-left',
        'fas fa-caret-right',
        'fas fa-caret-square-down',
        'far fa-caret-square-down',
        'fas fa-caret-square-left',
        'far fa-caret-square-left',
        'fas fa-caret-square-right',
        'far fa-caret-square-right',
        'fas fa-caret-square-up',
        'far fa-caret-square-up',
        'fas fa-caret-up',
        'fas fa-carrot',
        'fas fa-cart-arrow-down',
        'fas fa-cart-plus',
        'fas fa-cash-register',
        'fas fa-cat',
        'fas fa-certificate',
        'fas fa-chair',
        'fas fa-chalkboard',
        'fas fa-chalkboard-teacher',
        'fas fa-charging-station',
        'fas fa-chart-area',
        'fas fa-chart-bar',
        'far fa-chart-bar',
        'fas fa-chart-line',
        'fas fa-chart-pie',
        'fas fa-check',
        'fas fa-check-circle',
        'far fa-check-circle',
        'fas fa-check-double',
        'fas fa-check-square',
        'far fa-check-square',
        'fas fa-cheese',
        'fas fa-chess',
        'fas fa-chess-bishop',
        'fas fa-chess-board',
        'fas fa-chess-king',
        'fas fa-chess-knight',
        'fas fa-chess-pawn',
        'fas fa-chess-queen',
        'fas fa-chess-rook',
        'fas fa-chevron-circle-down',
        'fas fa-chevron-circle-left',
        'fas fa-chevron-circle-right',
        'fas fa-chevron-circle-up',
        'fas fa-chevron-down',
        'fas fa-chevron-left',
        'fas fa-chevron-right',
        'fas fa-chevron-up',
        'fas fa-child',
        'fas fa-church',
        'fas fa-circle',
        'far fa-circle',
        'fas fa-circle-notch',
        'fas fa-city',
        'fas fa-clinic-medical',
        'fas fa-clipboard',
        'far fa-clipboard',
        'fas fa-clipboard-check',
        'fas fa-clipboard-list',
        'fas fa-clock',
        'far fa-clock',
        'fas fa-clone',
        'far fa-clone',
        'fas fa-closed-captioning',
        'far fa-closed-captioning',
        'fas fa-cloud',
        'fas fa-cloud-download-alt',
        'fas fa-cloud-meatball',
        'fas fa-cloud-moon',
        'fas fa-cloud-moon-rain',
        'fas fa-cloud-rain',
        'fas fa-cloud-showers-heavy',
        'fas fa-cloud-sun',
        'fas fa-cloud-sun-rain',
        'fas fa-cloud-upload-alt',
        'fas fa-cocktail',
        'fas fa-code',
        'fas fa-code-branch',
        'fas fa-coffee',
        'fas fa-cog',
        'fas fa-cogs',
        'fas fa-coins',
        'fas fa-columns',
        'fas fa-comment',
        'far fa-comment',
        'fas fa-comment-alt',
        'far fa-comment-alt',
        'fas fa-comment-dollar',
        'fas fa-comment-dots',
        'far fa-comment-dots',
        'fas fa-comment-medical',
        'fas fa-comment-slash',
        'fas fa-comments',
        'far fa-comments',
        'fas fa-comments-dollar',
        'fas fa-compact-disc',
        'fas fa-compass',
        'far fa-compass',
        'fas fa-compress',
        'fas fa-compress-alt',
        'fas fa-compress-arrows-alt',
        'fas fa-concierge-bell',
        'fas fa-cookie',
        'fas fa-cookie-bite',
        'fas fa-copy',
        'far fa-copy',
        'fas fa-copyright',
        'far fa-copyright',
        'fas fa-couch',
        'fas fa-credit-card',
        'far fa-credit-card',
        'fas fa-crop',
        'fas fa-crop-alt',
        'fas fa-cross',
        'fas fa-crosshairs',
        'fas fa-crow',
        'fas fa-crown',
        'fas fa-crutch',
        'fas fa-cube',
        'fas fa-cubes',
        'fas fa-cut',
        'fas fa-database',
        'fas fa-deaf',
        'fas fa-democrat',
        'fas fa-dharmachakra',
        'fas fa-diagnoses',
        'fas fa-dice',
        'fas fa-dice-d20',
        'fas fa-dice-d6',
        'fas fa-dice-five',
        'fas fa-dice-four',
        'fas fa-dice-one',
        'fas fa-dice-six',
        'fas fa-dice-three',
        'fas fa-dice-two',
        'fas fa-digital-tachograph',
        'fas fa-directions',
        'fas fa-disease',
        'fas fa-divide',
        'fas fa-dizzy',
        'far fa-dizzy',
        'fas fa-dna',
        'fas fa-dog',
        'fas fa-dollar-sign',
        'fas fa-dolly',
        'fas fa-dolly-flatbed',
        'fas fa-donate',
        'fas fa-door-closed',
        'fas fa-door-open',
        'fas fa-dot-circle',
        'far fa-dot-circle',
        'fas fa-dove',
        'fas fa-download',
        'fas fa-drafting-compass',
        'fas fa-dragon',
        'fas fa-draw-polygon',
        'fas fa-drum-steelpan',
        'fas fa-drumstick-bite',
        'fas fa-dumbbell',
        'fas fa-dumpster',
        'fas fa-dumpster-fire',
        'fas fa-dungeon',
        'fas fa-edit',
        'far fa-edit',
        'fas fa-egg',
        'fas fa-eject',
        'fas fa-ellipsis-h',
        'fas fa-ellipsis-v',
        'fas fa-envelope',
        'far fa-envelope',
        'fas fa-envelope-open',
        'far fa-envelope-open',
        'fas fa-envelope-open-text',
        'fas fa-envelope-square',
        'fas fa-equals',
        'fas fa-eraser',
        'fas fa-ethernet',
        'fas fa-euro-sign',
        'fas fa-exchange-alt',
        'fas fa-exclamation',
        'fas fa-exclamation-circle',
        'fas fa-exclamation-triangle',
        'fas fa-expand',
        'fas fa-expand-alt',
        'fas fa-expand-arrows-alt',
        'fas fa-external-link-alt',
        'fas fa-external-link-square-alt',
        'fas fa-eye',
        'far fa-eye',
        'fas fa-eye-dropper',
        'fas fa-eye-slash',
        'far fa-eye-slash',
        'fas fa-fan',
        'fas fa-fast-backward',
        'fas fa-fast-forward',
        'fas fa-faucet',
        'fas fa-fax',
        'fas fa-feather',
        'fas fa-feather-alt',
        'fas fa-female',
        'fas fa-fighter-jet',
        'fas fa-file',
        'far fa-file',
        'fas fa-file-alt',
        'far fa-file-alt',
        'fas fa-file-archive',
        'far fa-file-archive',
        'fas fa-file-audio',
        'far fa-file-audio',
        'fas fa-file-code',
        'far fa-file-code',
        'fas fa-file-contract',
        'fas fa-file-csv',
        'fas fa-file-download',
        'fas fa-file-excel',
        'far fa-file-excel',
        'fas fa-file-export',
        'fas fa-file-image',
        'far fa-file-image',
        'fas fa-file-import',
        'fas fa-file-invoice',
        'fas fa-file-invoice-dollar',
        'fas fa-file-medical',
        'fas fa-file-medical-alt',
        'fas fa-file-pdf',
        'far fa-file-pdf',
        'fas fa-file-powerpoint',
        'far fa-file-powerpoint',
        'fas fa-file-prescription',
        'fas fa-file-signature',
        'fas fa-file-upload',
        'fas fa-file-video',
        'far fa-file-video',
        'fas fa-file-word',
        'far fa-file-word',
        'fas fa-fill',
        'fas fa-fill-drip',
        'fas fa-film',
        'fas fa-filter',
        'fas fa-fingerprint',
        'fas fa-fire',
        'fas fa-fire-alt',
        'fas fa-fire-extinguisher',
        'fas fa-first-aid',
        'fas fa-fish',
        'fas fa-fist-raised',
        'fas fa-flag',
        'far fa-flag',
        'fas fa-flag-checkered',
        'fas fa-flag-usa',
        'fas fa-flask',
        'fas fa-flushed',
        'far fa-flushed',
        'fas fa-folder',
        'far fa-folder',
        'fas fa-folder-minus',
        'fas fa-folder-open',
        'far fa-folder-open',
        'fas fa-folder-plus',
        'fas fa-font',
        'fas fa-football-ball',
        'fas fa-forward',
        'fas fa-frog',
        'fas fa-frown',
        'far fa-frown',
        'fas fa-frown-open',
        'far fa-frown-open',
        'fas fa-funnel-dollar',
        'fas fa-futbol',
        'far fa-futbol',
        'fas fa-gamepad',
        'fas fa-gas-pump',
        'fas fa-gavel',
        'fas fa-gem',
        'far fa-gem',
        'fas fa-genderless',
        'fas fa-ghost',
        'fas fa-gift',
        'fas fa-gifts',
        'fas fa-glass-cheers',
        'fas fa-glass-martini',
        'fas fa-glass-martini-alt',
        'fas fa-glass-whiskey',
        'fas fa-glasses',
        'fas fa-globe',
        'fas fa-globe-africa',
        'fas fa-globe-americas',
        'fas fa-globe-asia',
        'fas fa-globe-europe',
        'fas fa-golf-ball',
        'fas fa-gopuram',
        'fas fa-graduation-cap',
        'fas fa-greater-than',
        'fas fa-greater-than-equal',
        'fas fa-grimace',
        'far fa-grimace',
        'fas fa-grin',
        'far fa-grin',
        'fas fa-grin-alt',
        'far fa-grin-alt',
        'fas fa-grin-beam',
        'far fa-grin-beam',
        'fas fa-grin-beam-sweat',
        'far fa-grin-beam-sweat',
        'fas fa-grin-hearts',
        'far fa-grin-hearts',
        'fas fa-grin-squint',
        'far fa-grin-squint',
        'fas fa-grin-squint-tears',
        'far fa-grin-squint-tears',
        'fas fa-grin-stars',
        'far fa-grin-stars',
        'fas fa-grin-tears',
        'far fa-grin-tears',
        'fas fa-grin-tongue',
        'far fa-grin-tongue',
        'fas fa-grin-tongue-squint',
        'far fa-grin-tongue-squint',
        'fas fa-grin-tongue-wink',
        'far fa-grin-tongue-wink',
        'fas fa-grin-wink',
        'far fa-grin-wink',
        'fas fa-grip-horizontal',
        'fas fa-grip-lines',
        'fas fa-grip-lines-vertical',
        'fas fa-grip-vertical',
        'fas fa-guitar',
        'fas fa-h-square',
        'fas fa-hamburger',
        'fas fa-hammer',
        'fas fa-hamsa',
        'fas fa-hand-holding',
        'fas fa-hand-holding-heart',
        'fas fa-hand-holding-medical',
        'fas fa-hand-holding-usd',
        'fas fa-hand-holding-water',
        'fas fa-hand-lizard',
        'far fa-hand-lizard',
        'fas fa-hand-middle-finger',
        'fas fa-hand-paper',
        'far fa-hand-paper',
        'fas fa-hand-peace',
        'far fa-hand-peace',
        'fas fa-hand-point-down',
        'far fa-hand-point-down',
        'fas fa-hand-point-left',
        'far fa-hand-point-left',
        'fas fa-hand-point-right',
        'far fa-hand-point-right',
        'fas fa-hand-point-up',
        'far fa-hand-point-up',
        'fas fa-hand-pointer',
        'far fa-hand-pointer',
        'fas fa-hand-rock',
        'far fa-hand-rock',
        'fas fa-hand-scissors',
        'far fa-hand-scissors',
        'fas fa-hand-sparkles',
        'fas fa-hand-spock',
        'far fa-hand-spock',
        'fas fa-hands-helping',
        'fas fa-hands-wash',
        'fas fa-handshake',
        'far fa-handshake',
        'fas fa-handshake-alt-slash',
        'fas fa-handshake-slash',
        'fas fa-hanukiah',
        'fas fa-hard-hat',
        'fas fa-hashtag',
        'fas fa-hat-cowboy',
        'fas fa-hat-cowboy-side',
        'fas fa-hat-wizard',
        'fas fa-hdd',
        'far fa-hdd',
        'fas fa-head-side-cough',
        'fas fa-head-side-cough-slash',
        'fas fa-head-side-mask',
        'fas fa-head-side-virus',
        'fas fa-heading',
        'fas fa-headphones',
        'fas fa-headphones-alt',
        'fas fa-headset',
        'fas fa-heart',
        'far fa-heart',
        'fas fa-heart-broken',
        'fas fa-heartbeat',
        'fas fa-helicopter',
        'fas fa-highlighter',
        'fas fa-hiking',
        'fas fa-hippo',
        'fas fa-history',
        'fas fa-hockey-puck',
        'fas fa-holly-berry',
        'fas fa-home',
        'fas fa-horse',
        'fas fa-horse-head',
        'fas fa-hospital',
        'far fa-hospital',
        'fas fa-hospital-alt',
        'fas fa-hospital-symbol',
        'fas fa-hospital-user',
        'fas fa-hot-tub',
        'fas fa-hotdog',
        'fas fa-hotel',
        'fas fa-hourglass',
        'far fa-hourglass',
        'fas fa-hourglass-end',
        'fas fa-hourglass-half',
        'fas fa-hourglass-start',
        'fas fa-house-damage',
        'fas fa-house-user',
        'fas fa-hryvnia',
        'fas fa-i-cursor',
        'fas fa-ice-cream',
        'fas fa-icicles',
        'fas fa-icons',
        'fas fa-id-badge',
        'far fa-id-badge',
        'fas fa-id-card',
        'far fa-id-card',
        'fas fa-id-card-alt',
        'fas fa-igloo',
        'fas fa-image',
        'far fa-image',
        'fas fa-images',
        'far fa-images',
        'fas fa-inbox',
        'fas fa-indent',
        'fas fa-industry',
        'fas fa-infinity',
        'fas fa-info',
        'fas fa-info-circle',
        'fas fa-italic',
        'fas fa-jedi',
        'fas fa-joint',
        'fas fa-journal-whills',
        'fas fa-kaaba',
        'fas fa-key',
        'fas fa-keyboard',
        'far fa-keyboard',
        'fas fa-khanda',
        'fas fa-kiss',
        'far fa-kiss',
        'fas fa-kiss-beam',
        'far fa-kiss-beam',
        'fas fa-kiss-wink-heart',
        'far fa-kiss-wink-heart',
        'fas fa-kiwi-bird',
        'fas fa-landmark',
        'fas fa-language',
        'fas fa-laptop',
        'fas fa-laptop-code',
        'fas fa-laptop-house',
        'fas fa-laptop-medical',
        'fas fa-laugh',
        'far fa-laugh',
        'fas fa-laugh-beam',
        'far fa-laugh-beam',
        'fas fa-laugh-squint',
        'far fa-laugh-squint',
        'fas fa-laugh-wink',
        'far fa-laugh-wink',
        'fas fa-layer-group',
        'fas fa-leaf',
        'fas fa-lemon',
        'far fa-lemon',
        'fas fa-less-than',
        'fas fa-less-than-equal',
        'fas fa-level-down-alt',
        'fas fa-level-up-alt',
        'fas fa-life-ring',
        'far fa-life-ring',
        'fas fa-lightbulb',
        'far fa-lightbulb',
        'fas fa-link',
        'fas fa-lira-sign',
        'fas fa-list',
        'fas fa-list-alt',
        'far fa-list-alt',
        'fas fa-list-ol',
        'fas fa-list-ul',
        'fas fa-location-arrow',
        'fas fa-lock',
        'fas fa-lock-open',
        'fas fa-long-arrow-alt-down',
        'fas fa-long-arrow-alt-left',
        'fas fa-long-arrow-alt-right',
        'fas fa-long-arrow-alt-up',
        'fas fa-low-vision',
        'fas fa-luggage-cart',
        'fas fa-lungs',
        'fas fa-lungs-virus',
        'fas fa-magic',
        'fas fa-magnet',
        'fas fa-mail-bulk',
        'fas fa-male',
        'fas fa-map',
        'far fa-map',
        'fas fa-map-marked',
        'fas fa-map-marked-alt',
        'fas fa-map-marker',
        'fas fa-map-marker-alt',
        'fas fa-map-pin',
        'fas fa-map-signs',
        'fas fa-marker',
        'fas fa-mars',
        'fas fa-mars-double',
        'fas fa-mars-stroke',
        'fas fa-mars-stroke-h',
        'fas fa-mars-stroke-v',
        'fas fa-mask',
        'fas fa-medal',
        'fas fa-medkit',
        'fas fa-meh',
        'far fa-meh',
        'fas fa-meh-blank',
        'far fa-meh-blank',
        'fas fa-meh-rolling-eyes',
        'far fa-meh-rolling-eyes',
        'fas fa-memory',
        'fas fa-menorah',
        'fas fa-mercury',
        'fas fa-meteor',
        'fas fa-microchip',
        'fas fa-microphone',
        'fas fa-microphone-alt',
        'fas fa-microphone-alt-slash',
        'fas fa-microphone-slash',
        'fas fa-microscope',
        'fas fa-minus',
        'fas fa-minus-circle',
        'fas fa-minus-square',
        'far fa-minus-square',
        'fas fa-mitten',
        'fas fa-mobile',
        'fas fa-mobile-alt',
        'fas fa-money-bill',
        'fas fa-money-bill-alt',
        'far fa-money-bill-alt',
        'fas fa-money-bill-wave',
        'fas fa-money-bill-wave-alt',
        'fas fa-money-check',
        'fas fa-monument',
        'fas fa-moon',
        'far fa-moon',
        'fas fa-mortar-pestle',
        'fas fa-mosque',
        'fas fa-motorcycle',
        'fas fa-mountain',
        'fas fa-mouse',
        'fas fa-mouse-pointer',
        'fas fa-mug-hot',
        'fas fa-music',
        'fas fa-network-wired',
        'fas fa-neuter',
        'fas fa-newspaper',
        'far fa-newspaper',
        'fas fa-not-equal',
        'fas fa-notes-medical',
        'fas fa-object-group',
        'far fa-object-group',
        'fas fa-object-ungroup',
        'far fa-object-ungroup',
        'fas fa-oil-can',
        'fas fa-om',
        'fas fa-otter',
        'fas fa-outdent',
        'fas fa-pager',
        'fas fa-paint-brush',
        'fas fa-paint-roller',
        'fas fa-palette',
        'fas fa-pallet',
        'fas fa-paper-plane',
        'far fa-paper-plane',
        'fas fa-paperclip',
        'fas fa-parachute-box',
        'fas fa-paragraph',
        'fas fa-parking',
        'fas fa-passport',
        'fas fa-pastafarianism',
        'fas fa-paste',
        'fas fa-pause',
        'fas fa-pause-circle',
        'far fa-pause-circle',
        'fas fa-paw',
        'fas fa-peace',
        'fas fa-pen',
        'fas fa-pen-alt',
        'fas fa-pen-fancy',
        'fas fa-pen-nib',
        'fas fa-pen-square',
        'fas fa-pencil-alt',
        'fas fa-pencil-ruler',
        'fas fa-people-arrows',
        'fas fa-people-carry',
        'fas fa-pepper-hot',
        'fas fa-percent',
        'fas fa-percentage',
        'fas fa-person-booth',
        'fas fa-phone',
        'fas fa-phone-slash',
        'fas fa-phone-square',
        'fas fa-phone-square-alt',
        'fas fa-phone-volume',
        'fas fa-photo-video',
        'fas fa-piggy-bank',
        'fas fa-pills',
        'fas fa-pizza-slice',
        'fas fa-place-of-worship',
        'fas fa-plane',
        'fas fa-plane-arrival',
        'fas fa-plane-departure',
        'fas fa-plane-slash',
        'fas fa-play',
        'fas fa-play-circle',
        'far fa-play-circle',
        'fas fa-plug',
        'fas fa-plus',
        'fas fa-plus-circle',
        'fas fa-plus-square',
        'far fa-plus-square',
        'fas fa-podcast',
        'fas fa-poll',
        'fas fa-poll-h',
        'fas fa-poo',
        'fas fa-poo-storm',
        'fas fa-poop',
        'fas fa-portrait',
        'fas fa-pound-sign',
        'fas fa-power-off',
        'fas fa-pray',
        'fas fa-praying-hands',
        'fas fa-prescription',
        'fas fa-prescription-bottle',
        'fas fa-prescription-bottle-alt',
        'fas fa-print',
        'fas fa-procedures',
        'fas fa-project-diagram',
        'fas fa-pump-medical',
        'fas fa-pump-soap',
        'fas fa-puzzle-piece',
        'fas fa-qrcode',
        'fas fa-question',
        'fas fa-question-circle',
        'far fa-question-circle',
        'fas fa-quidditch',
        'fas fa-quote-left',
        'fas fa-quote-right',
        'fas fa-quran',
        'fas fa-radiation',
        'fas fa-radiation-alt',
        'fas fa-rainbow',
        'fas fa-random',
        'fas fa-receipt',
        'fas fa-record-vinyl',
        'fas fa-recycle',
        'fas fa-redo',
        'fas fa-redo-alt',
        'fas fa-registered',
        'far fa-registered',
        'fas fa-remove-format',
        'fas fa-reply',
        'fas fa-reply-all',
        'fas fa-republican',
        'fas fa-restroom',
        'fas fa-retweet',
        'fas fa-ribbon',
        'fas fa-ring',
        'fas fa-road',
        'fas fa-robot',
        'fas fa-rocket',
        'fas fa-route',
        'fas fa-rss',
        'fas fa-rss-square',
        'fas fa-ruble-sign',
        'fas fa-ruler',
        'fas fa-ruler-combined',
        'fas fa-ruler-horizontal',
        'fas fa-ruler-vertical',
        'fas fa-running',
        'fas fa-rupee-sign',
        'fas fa-sad-cry',
        'far fa-sad-cry',
        'fas fa-sad-tear',
        'far fa-sad-tear',
        'fas fa-satellite',
        'fas fa-satellite-dish',
        'fas fa-save',
        'far fa-save',
        'fas fa-school',
        'fas fa-screwdriver',
        'fas fa-scroll',
        'fas fa-sd-card',
        'fas fa-search',
        'fas fa-search-dollar',
        'fas fa-search-location',
        'fas fa-search-minus',
        'fas fa-search-plus',
        'fas fa-seedling',
        'fas fa-server',
        'fas fa-shapes',
        'fas fa-share',
        'fas fa-share-alt',
        'fas fa-share-alt-square',
        'fas fa-share-square',
        'far fa-share-square',
        'fas fa-shekel-sign',
        'fas fa-shield-alt',
        'fas fa-shield-virus',
        'fas fa-ship',
        'fas fa-shipping-fast',
        'fas fa-shoe-prints',
        'fas fa-shopping-bag',
        'fas fa-shopping-basket',
        'fas fa-shopping-cart',
        'fas fa-shower',
        'fas fa-shuttle-van',
        'fas fa-sign',
        'fas fa-sign-in-alt',
        'fas fa-sign-language',
        'fas fa-sign-out-alt',
        'fas fa-signal',
        'fas fa-signature',
        'fas fa-sim-card',
        'fas fa-sitemap',
        'fas fa-skating',
        'fas fa-skiing',
        'fas fa-skiing-nordic',
        'fas fa-skull',
        'fas fa-skull-crossbones',
        'fas fa-slash',
        'fas fa-sleigh',
        'fas fa-sliders-h',
        'fas fa-smile',
        'far fa-smile',
        'fas fa-smile-beam',
        'far fa-smile-beam',
        'fas fa-smile-wink',
        'far fa-smile-wink',
        'fas fa-smog',
        'fas fa-smoking',
        'fas fa-smoking-ban',
        'fas fa-sms',
        'fas fa-snowboarding',
        'fas fa-snowflake',
        'far fa-snowflake',
        'fas fa-snowman',
        'fas fa-snowplow',
        'fas fa-soap',
        'fas fa-socks',
        'fas fa-solar-panel',
        'fas fa-sort',
        'fas fa-sort-alpha-down',
        'fas fa-sort-alpha-down-alt',
        'fas fa-sort-alpha-up',
        'fas fa-sort-alpha-up-alt',
        'fas fa-sort-amount-down',
        'fas fa-sort-amount-down-alt',
        'fas fa-sort-amount-up',
        'fas fa-sort-amount-up-alt',
        'fas fa-sort-down',
        'fas fa-sort-numeric-down',
        'fas fa-sort-numeric-down-alt',
        'fas fa-sort-numeric-up',
        'fas fa-sort-numeric-up-alt',
        'fas fa-sort-up',
        'fas fa-spa',
        'fas fa-space-shuttle',
        'fas fa-spell-check',
        'fas fa-spider',
        'fas fa-spinner',
        'fas fa-splotch',
        'fas fa-spray-can',
        'fas fa-square',
        'far fa-square',
        'fas fa-square-full',
        'fas fa-square-root-alt',
        'fas fa-stamp',
        'fas fa-star',
        'far fa-star',
        'fas fa-star-and-crescent',
        'fas fa-star-half',
        'far fa-star-half',
        'fas fa-star-half-alt',
        'fas fa-star-of-david',
        'fas fa-star-of-life',
        'fas fa-step-backward',
        'fas fa-step-forward',
        'fas fa-stethoscope',
        'fas fa-sticky-note',
        'far fa-sticky-note',
        'fas fa-stop',
        'fas fa-stop-circle',
        'far fa-stop-circle',
        'fas fa-stopwatch',
        'fas fa-stopwatch-20',
        'fas fa-store',
        'fas fa-store-alt',
        'fas fa-store-alt-slash',
        'fas fa-store-slash',
        'fas fa-stream',
        'fas fa-street-view',
        'fas fa-strikethrough',
        'fas fa-stroopwafel',
        'fas fa-subscript',
        'fas fa-subway',
        'fas fa-suitcase',
        'fas fa-suitcase-rolling',
        'fas fa-sun',
        'far fa-sun',
        'fas fa-superscript',
        'fas fa-surprise',
        'far fa-surprise',
        'fas fa-swatchbook',
        'fas fa-swimmer',
        'fas fa-swimming-pool',
        'fas fa-synagogue',
        'fas fa-sync',
        'fas fa-sync-alt',
        'fas fa-syringe',
        'fas fa-table',
        'fas fa-table-tennis',
        'fas fa-tablet',
        'fas fa-tablet-alt',
        'fas fa-tablets',
        'fas fa-tachometer-alt',
        'fas fa-tag',
        'fas fa-tags',
        'fas fa-tape',
        'fas fa-tasks',
        'fas fa-taxi',
        'fas fa-teeth',
        'fas fa-teeth-open',
        'fas fa-temperature-high',
        'fas fa-temperature-low',
        'fas fa-tenge',
        'fas fa-terminal',
        'fas fa-text-height',
        'fas fa-text-width',
        'fas fa-th',
        'fas fa-th-large',
        'fas fa-th-list',
        'fas fa-theater-masks',
        'fas fa-thermometer',
        'fas fa-thermometer-empty',
        'fas fa-thermometer-full',
        'fas fa-thermometer-half',
        'fas fa-thermometer-quarter',
        'fas fa-thermometer-three-quarters',
        'fas fa-thumbs-down',
        'far fa-thumbs-down',
        'fas fa-thumbs-up',
        'far fa-thumbs-up',
        'fas fa-thumbtack',
        'fas fa-ticket-alt',
        'fas fa-times',
        'fas fa-times-circle',
        'far fa-times-circle',
        'fas fa-tint',
        'fas fa-tint-slash',
        'fas fa-tired',
        'far fa-tired',
        'fas fa-toggle-off',
        'fas fa-toggle-on',
        'fas fa-toilet',
        'fas fa-toilet-paper',
        'fas fa-toilet-paper-slash',
        'fas fa-toolbox',
        'fas fa-tools',
        'fas fa-tooth',
        'fas fa-torah',
        'fas fa-torii-gate',
        'fas fa-tractor',
        'fas fa-trademark',
        'fas fa-traffic-light',
        'fas fa-trailer',
        'fas fa-train',
        'fas fa-tram',
        'fas fa-transgender',
        'fas fa-transgender-alt',
        'fas fa-trash',
        'fas fa-trash-alt',
        'far fa-trash-alt',
        'fas fa-trash-restore',
        'fas fa-trash-restore-alt',
        'fas fa-tree',
        'fas fa-trophy',
        'fas fa-truck',
        'fas fa-truck-loading',
        'fas fa-truck-monster',
        'fas fa-truck-moving',
        'fas fa-truck-pickup',
        'fas fa-tshirt',
        'fas fa-tty',
        'fas fa-tv',
        'fas fa-umbrella',
        'fas fa-umbrella-beach',
        'fas fa-underline',
        'fas fa-undo',
        'fas fa-undo-alt',
        'fas fa-universal-access',
        'fas fa-university',
        'fas fa-unlink',
        'fas fa-unlock',
        'fas fa-unlock-alt',
        'fas fa-upload',
        'fas fa-user',
        'far fa-user',
        'fas fa-user-alt',
        'fas fa-user-alt-slash',
        'fas fa-user-astronaut',
        'fas fa-user-check',
        'fas fa-user-circle',
        'far fa-user-circle',
        'fas fa-user-clock',
        'fas fa-user-cog',
        'fas fa-user-edit',
        'fas fa-user-friends',
        'fas fa-user-graduate',
        'fas fa-user-injured',
        'fas fa-user-lock',
        'fas fa-user-md',
        'fas fa-user-minus',
        'fas fa-user-ninja',
        'fas fa-user-nurse',
        'fas fa-user-plus',
        'fas fa-user-secret',
        'fas fa-user-shield',
        'fas fa-user-slash',
        'fas fa-user-tag',
        'fas fa-user-tie',
        'fas fa-user-times',
        'fas fa-users',
        'fas fa-users-cog',
        'fas fa-utensil-spoon',
        'fas fa-utensils',
        'fas fa-vector-square',
        'fas fa-venus',
        'fas fa-venus-double',
        'fas fa-venus-mars',
        'fas fa-vial',
        'fas fa-vials',
        'fas fa-video',
        'fas fa-video-slash',
        'fas fa-vihara',
        'fas fa-virus',
        'fas fa-virus-slash',
        'fas fa-viruses',
        'fas fa-voicemail',
        'fas fa-volleyball-ball',
        'fas fa-volume-down',
        'fas fa-volume-mute',
        'fas fa-volume-off',
        'fas fa-volume-up',
        'fas fa-vote-yea',
        'fas fa-vr-cardboard',
        'fas fa-walking',
        'fas fa-wallet',
        'fas fa-warehouse',
        'fas fa-water',
        'fas fa-wave-square',
        'fas fa-weight',
        'fas fa-weight-hanging',
        'fas fa-wheelchair',
        'fas fa-wifi',
        'fas fa-wind',
        'fas fa-window-close',
        'far fa-window-close',
        'fas fa-window-maximize',
        'far fa-window-maximize',
        'fas fa-window-minimize',
        'far fa-window-minimize',
        'fas fa-window-restore',
        'far fa-window-restore',
        'fas fa-wine-bottle',
        'fas fa-wine-glass',
        'fas fa-wine-glass-alt',
        'fas fa-won-sign',
        'fas fa-wrench',
        'fas fa-x-ray',
        'fas fa-yen-sign',
        'fas fa-yin-yang'
    ];

    
};