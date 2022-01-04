export default function ($timeout, $scope, $routeParams, $window, programService, adminService, $location, heroImageService, pageService, $rootScope) {
    let epc = this;

    $window.scrollTo(0, 0);

    epc.getProgram = function () {
        pageService.getPage(`/programs/${$routeParams.program}`).then( function(pageMeta) {
            $rootScope.meta = pageMeta;
        })
        .then(function() {
            programService.getProgram($routeParams.program)
            .then(function (data){
                epc.program = angular.copy(data);
                if($rootScope.user.role === 'admin') {
                    epc.getRevisions();
                }

                adminService.getStaged('program', epc.program.id)
                .then(function (stagedData) {
                    epc.edited = angular.copy(stagedData.object);
                    epc.staged = true;
                    epc.stagedDate = stagedData.created;
                    epc.stagedId = stagedData.id;
                    for (let i=0; i < (epc.edited.tabs.length); i++) {
                        if(epc.edited.tabs[i].tabOrder === 0) {
                            epc.changeTab(epc.edited.tabs[i].id);
                        }
                    }
                    epc.edited.meta = {
                        title: angular.copy($rootScope.meta.title),
                        description: angular.copy($rootScope.meta.description),
                        keywords: angular.copy($rootScope.meta.keywords),
                        oldLink: angular.copy($rootScope.meta.url)
                    };
                    epc.ckeditorConfig = {
                        bodyClass: `accent-${epc.edited.color} article-wrapper-content-text`,
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
                }, function () {
                    epc.edited = angular.copy(data);
                    epc.edited.meta = angular.copy($rootScope.meta);
                    for (let i=0; i < (epc.edited.tabs.length); i++) {
                        if(epc.edited.tabs[i].tabOrder === 0) {
                            epc.changeTab(epc.edited.tabs[i].id);
                        }
                    }
                    epc.edited.meta = {
                        title: angular.copy($rootScope.meta.title),
                        description: angular.copy($rootScope.meta.description),
                        keywords: angular.copy($rootScope.meta.keywords),
                        oldLink: angular.copy($rootScope.meta.url)
                    };
                    epc.ckeditorConfig = {
                        bodyClass: `accent-${epc.edited.color} article-wrapper-content-text`,
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
                epc.ready = true;
                epc.getLocations();
                epc.getFaqs();
            }, function (error) {
                console.error('failed to retrieve program:');
                console.error(error);
            })
        });
    };

    epc.getRevisions = function () {
        adminService.getRevisions('program', epc.program.id)
        .then(function (data) {
            epc.revisions = data;
        })
        .then(function () {
            if ($routeParams.revision) {
                epc.selectedRevision = parseInt($routeParams.revision);
                epc.changeRevision();
            }
        });
    };

    epc.changeRevision = function () {
        if(epc.selectedRevision === 0) {
            if($routeParams.revision) {
                $location.path(`/admin/edit-program/${$routeParams.program}`);
            } else {
                epc.init();
            }
        } else {
            adminService.getRevision(epc.selectedRevision)
            .then(function (data) {
                epc.staged = true;
                epc.edited = angular.copy(data.object);
                epc.edited.meta = {
                    title: angular.copy($rootScope.meta.title),
                    description: angular.copy($rootScope.meta.description),
                    keywords: angular.copy($rootScope.meta.keywords),
                    oldLink: angular.copy($rootScope.meta.url)
                };
                epc.stagedDate = data.created;
                epc.stagedId = data.id;
                epc.stagedApproved = data.approved;
                epc.stagedFirstName = data.firstname;
                epc.stagedLastName = data.lastname;
                epc.revisionDescription = data.description;
            });
        }
    }

    epc.getLocations = function () {
        programService.getLocations(epc.program.id)
        .then(function (data){
            epc.program.locations = angular.copy(data);
            epc.edited.locations = angular.copy(data);
        }, function (error) {
            console.error('failed to retrieve program locations:');
            console.error(error);
            epc.program.locations = [];
            epc.edited.locations = [];
        })
    }

    epc.getFaqs = function () {
        programService.getFaqs(epc.program.id)
        .then(function (data){
            epc.program.questions = angular.copy(data);
            epc.edited.questions = angular.copy(data);
        }, function (error) {
            epc.program.questions = [];
            epc.edited.questions = [];
            console.error('failed to retrieve program questions:');
            console.error(error);
        })
    }

    epc.init = function () {
        epc.getProgram();
        epc.selectedRevision = 0;
        epc.newRevisionDescriptionOpen = false;
        epc.revisionDescription = false;
        epc.revisionDescriptionOpen = false;
        epc.staged = false;
        epc.stagedId = false;
        epc.stagedApproved = false;
        epc.stagedFirstName = false;
        epc.stagedLastName = false;
        epc.tabOrderChanged = false;
        epc.tabsChanged = false;
        epc.tabsOpen = false;
        epc.deleteTabPopup = false;
        epc.deleteTabIndex = -1;
        epc.deleteSectionPopup = false;
        epc.deleteSectionIndex = -1;
        epc.deleteSectionTabIndex = -1;
        epc.newTabID = 0;
        epc.nameOpen = false;
        epc.nameChanged = false;
        epc.sectionsChanged = false;
        epc.locationsChanged = false;
        epc.locationsOpen = false;
        epc.locationsTitleChanged = false;
        epc.locationsDescriptionChanged = false;
        epc.applyButtonChanged = false;
        epc.questionsChanged = false;
        epc.questionsOpen = false;
        epc.homeImageChanged = false;
        epc.homeImageOpen = false;
        epc.aboutImageChanged = false;
        epc.aboutImageOpen = false;
        epc.linkChanged = false;
        epc.linkOpen = false;
        epc.linkError = false;
        epc.mediumDescriptionChanged = false;
        epc.mediumDescriptionOpen = false;
        epc.shortDescriptionChanged = false;
        epc.shortDescriptionOpen = false;
        epc.heroOpen = false;
        epc.heroChanged = false;
        epc.metaDescriptionChanged = false;
        epc.metaTitleChanged = false;
        epc.metaKeywordsChanged = false;
        epc.metaDescriptionOpen = false;
        epc.metaTitleOpen = false;
        epc.metaKeywordsOpen = false;
        $scope.Math = window.Math;
    };

    epc.moveTab = function (direction, originalTabOrder, id) {
        let swapTabId = -1;
        epc.edited.tabs.forEach(function (tab) {
            if(direction === 'right') {
                if(tab.tabOrder === (originalTabOrder + 1)) {
                    swapTabId = tab.id;
                }
            } else {
                if(tab.tabOrder === (originalTabOrder - 1)) {
                    swapTabId = tab.id;
                }
            }
        });
        for (let i=0; i < (epc.edited.tabs.length); i++) {
            if(direction === 'right') {
                if(epc.edited.tabs[i].id === id) {
                    epc.edited.tabs[i].tabOrder++;
                }
                if(epc.edited.tabs[i].id === swapTabId) {
                    epc.edited.tabs[i].tabOrder--;
                }
            } else {
                if(epc.edited.tabs[i].id === id) {
                    epc.edited.tabs[i].tabOrder--;
                }
                if(epc.edited.tabs[i].id === swapTabId) {
                    epc.edited.tabs[i].tabOrder++;
                }
            }
        }
        epc.tabsChanged = true;
    };

    epc.moveSection = function (direction, originalSectionOrder, tabId, sectionId) {
        let swapSectionId = -1;
        let tabIndex = epc.getTabIndex(tabId);
        epc.edited.tabs[tabIndex].sections.forEach(function (section) {
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
        for (let i=0; i < (epc.edited.tabs[tabIndex].sections.length); i++) {
            if(direction === 'down') {
                if(epc.edited.tabs[tabIndex].sections[i].id === sectionId) {
                    epc.edited.tabs[tabIndex].sections[i].sectionOrder++;
                }
                if(epc.edited.tabs[tabIndex].sections[i].id === swapSectionId) {
                    epc.edited.tabs[tabIndex].sections[i].sectionOrder--;
                }
            } else {
                if(epc.edited.tabs[tabIndex].sections[i].id === sectionId) {
                    epc.edited.tabs[tabIndex].sections[i].sectionOrder--;
                }
                if(epc.edited.tabs[tabIndex].sections[i].id === swapSectionId) {
                    epc.edited.tabs[tabIndex].sections[i].sectionOrder++;
                }
            }
        }
        epc.sectionsChanged = true;
    };

    epc.changeTab = (tab) => {
        for (let i=0; i < (epc.edited.tabs.length); i++) {
            if(epc.edited.tabs[i].activeTab) {
                epc.edited.tabs[i].activeTab = false;
            }
            if(epc.edited.tabs[i].id === tab) {
                epc.edited.tabs[i].activeTab = true;
            }
        }
        epc.activeTab = tab;
    };

    epc.saveTabNameEdit = (id) => {
        let tabIndex = epc.getTabIndex(id);
        epc.edited.tabs[tabIndex].nameEdit = false;
        epc.tabsChanged = true;
        epc.edited.tabs[tabIndex].slug = encodeURI(epc.edited.tabs[tabIndex].title.replace(/ /g, '-').toLowerCase());
    };

    epc.cancelTabNameEdit = (id) => {
        for (let i=0; i < epc.program.tabs.length; i++) {
            if(epc.program.tabs[i].id === id) {
                epc.edited.tabs[epc.getTabIndex(id)].title = angular.copy(epc.program.tabs[i].title);
            }
        }
    };

    epc.revertEdit = (module, tabId = false, sectionId = false) => {
        if(module === 'tabs') {
            for(let original=0; original < epc.program.tabs.length; original++) {
                for(let edited=0; edited < epc.edited.tabs.length; edited++) {
                    if(!epc.edited.tabs[edited].newTab) {
                        if(epc.program.tabs[original].id === epc.edited.tabs[edited].id) {
                            epc.edited.tabs[edited].title = angular.copy(epc.program.tabs[original].title);
                            epc.edited.tabs[edited].tabOrder = angular.copy(epc.program.tabs[original].tabOrder);
                            epc.edited.tabs[edited].deleted = false;
                            
                        }
                    } else {
                        epc.edited.tabs.splice(edited, 1);
                    }
                }
            }
            epc.tabsChanged = false;
        } else if (module === 'sections' && tabId) {
            let tabIndex = epc.getTabIndex(tabId);
            for(let original=0; original < epc.program.tabs[tabIndex].sections.length; original++) {
                for(let edited=0; edited < epc.edited.tabs[tabIndex].sections.length; edited++) {
                    if(!epc.edited.tabs[tabIndex].sections[edited].newSection) {
                        if(epc.program.tabs[tabIndex].sections[original].id === epc.edited.tabs[tabIndex].sections[edited].id) {
                            epc.edited.tabs[tabIndex].sections[edited].title = angular.copy(epc.program.tabs[tabIndex].sections[original].title);
                            epc.edited.tabs[tabIndex].sections[edited].sectionOrder = angular.copy(epc.program.tabs[tabIndex].sections[original].sectionOrder);
                            epc.edited.tabs[tabIndex].sections[edited].deleted = false;
                            
                        }
                    } else {
                        epc.edited.tabs[tabIndex].sections.splice(edited, 1);
                    }
                }
            }
            epc.sectionsChanged = false;
        } else if (module === 'applyButton') {
            epc.edited.displayApplyButton = angular.copy(epc.program.displayApplyButton);
            epc.edited.applyButtonLink = angular.copy(epc.program.applyButtonLink);
            epc.edited.applyButtonText = angular.copy(epc.program.applyButtonText);
            epc.applyButtonChanged = false;
        } else if (module === 'metaDescription') {
            epc.edited.meta.description = angular.copy($rootScope.meta.description);
        } else if (module === 'metaTitle') {
            epc.edited.meta.title = angular.copy($rootScope.meta.title);
        } else if (module === 'metaKeywords') {
            epc.edited.meta.keywords = angular.copy($rootScope.meta.keywords);
        } else {
            epc.edited[module] = angular.copy(epc.program[module]);
            epc[`${module}Changed`] = false;
            epc[`${module}Open`] = false;
            epc[`${module}Error`] = null;
        }
    }

    epc.saveEdit= (module, tabId = false, sectionId = false) => {
        if (module === 'text' && tabId && sectionId) {
            let tabIndex = epc.getTabIndex(tabId);
            let sectionIndex = epc.getSectionIndex(tabIndex, sectionId);
            epc.edited.tabs[tabIndex].sections[sectionIndex].textOpen = false;
            epc.edited.tabs[tabIndex].sections[sectionIndex].textChanged = true;
            epc.sectionsChanged = true;
        } else if (module === 'title' && tabId && sectionId) {
            let tabIndex = epc.getTabIndex(tabId);
            let sectionIndex = epc.getSectionIndex(tabIndex, sectionId);
            epc.edited.tabs[tabIndex].sections[sectionIndex].titleOpen = false;
            epc.edited.tabs[tabIndex].sections[sectionIndex].titleChanged = true;
            epc.sectionsChanged = true;
        } else if (module === 'button' && tabId && sectionId) {
            let tabIndex = epc.getTabIndex(tabId);
            let sectionIndex = epc.getSectionIndex(tabIndex, sectionId);
            epc.edited.tabs[tabIndex].sections[sectionIndex].buttonOpen = false;
            epc.edited.tabs[tabIndex].sections[sectionIndex].buttonChanged = true;
            epc.sectionsChanged = true;
        } else if (module === 'applyButton') {
            epc.applyButtonChanged = true;
            epc.applyButtonOpen = false;
        } else if (module === 'question' && tabId) {
            let questionIndex = epc.getQuestionIndex(tabId);
            epc.edited.questions[questionIndex].questionOpen = false;
            epc.edited.questions[questionIndex].questionChanged = true;
            epc.questionsChanged = true;
        } else if (module === 'metaDescription') {
            epc.metaDescriptionOpen = false;
            epc.metaDescriptionChanged = true;
        } else if (module === 'metaTitle') {
            epc.metaTitleOpen = false;
            epc.metaTitleChanged = true;
        } else if (module === 'metaKeywords') {
            epc.metaKeywordsOpen = false;
            epc.metaKeywordsChanged = true;
        } else {
            epc[`${module}Changed`] = true;
            epc[`${module}Open`] = false;
            epc[`${module}Error`] = null;
        }
    };

    epc.cancelEdit = function (module, tabId = false, sectionId = false) {
        if (module === 'text' && tabId && sectionId) {
            let tabIndex = epc.getTabIndex(tabId);
            let sectionIndex = epc.getSectionIndex(tabIndex, sectionId);
            epc.edited.tabs[tabIndex].sections[sectionIndex].text = angular.copy(epc.program.tabs[tabIndex].sections[sectionIndex].text);
            epc.edited.tabs[tabIndex].sections[sectionIndex].textOpen = false;
        } else if (module === 'title' && tabId && sectionId) {
            let tabIndex = epc.getTabIndex(tabId);
            let sectionIndex = epc.getSectionIndex(tabIndex, sectionId);
            epc.edited.tabs[tabIndex].sections[sectionIndex].title = angular.copy(epc.program.tabs[tabIndex].sections[sectionIndex].title);
            epc.edited.tabs[tabIndex].sections[sectionIndex].titleOpen = false;
            epc.edited.tabs[tabIndex].sections[sectionIndex].titleChanged = false;
        } else if (module === 'button' && tabId && sectionId) {
            let tabIndex = epc.getTabIndex(tabId);
            let sectionIndex = epc.getSectionIndex(tabIndex, sectionId);
            if(!epc.edited.tabs[tabIndex].sections[sectionIndex].buttonNew) {
                epc.edited.tabs[tabIndex].sections[sectionIndex].buttonLink = angular.copy(epc.program.tabs[tabIndex].sections[sectionIndex].buttonLink);
                epc.edited.tabs[tabIndex].sections[sectionIndex].buttonText = angular.copy(epc.program.tabs[tabIndex].sections[sectionIndex].buttonText);
                epc.edited.tabs[tabIndex].sections[sectionIndex].buttonChanged = false;
            }
            epc.edited.tabs[tabIndex].sections[sectionIndex].buttonOpen = false;
        } else if (module === 'applyButton') {
            epc.edited.displayApplyButton = angular.copy(epc.program.displayApplyButton);
            epc.edited.applyButtonLink = angular.copy(epc.program.applyButtonLink);
            epc.edited.applyButtonText = angular.copy(epc.program.applyButtonText);
            epc.applyButtonChanged = false;
            epc.applyButtonOpen = false;
        } else if (module === 'question' && tabId) {
            let questionIndex = epc.getQuestionIndex(tabId);
            if(!epc.edited.questions[questionIndex].questionNew) {
                epc.edited.questions[questionIndex].question = angular.copy(epc.program.questions[questionIndex].question);
                epc.edited.questions[questionIndex].answer = angular.copy(epc.program.questions[questionIndex].answer);
                epc.edited.questions[questionIndex].questionOpen = false;
                epc.edited.questions[questionIndex].questionChanged = false;
            }
        } else if (module === 'metaDescription') {
            epc.metaDescriptionOpen = false;
            epc.metaDescriptionChanged = false;
            epc.edited.meta.description = angular.copy($rootScope.meta.description);
        } else if (module === 'metaTitle') {
            epc.metaTitleOpen = false;
            epc.metaTitleChanged = false;
            epc.edited.meta.title = angular.copy($rootScope.meta.title);
        } else if (module === 'metaKeywords') {
            epc.metaKeywordsOpen = false;
            epc.metaKeywordsChanged = false;
            epc.edited.meta.keywords = angular.copy($rootScope.meta.keywords);
        } else {
            epc.edited[module] = angular.copy(epc.program[module]);
            epc[`${module}Open`] = false;
        }
    };

    epc.removeStaged = function () {
        adminService.removeStaged(epc.stagedId)
        .then(function () {
            epc.init();
        });
    };

    epc.rejectRevision = function () {
        adminService.rejectRevision(epc.stagedId)
        .then(function () {
            epc.init();
        });
    };

    epc.approveRevision = function () {
        adminService.approveRevision(epc.stagedId)
        .then(function () {
            epc.submitEdit();
        });
    };

    epc.deleteTabWarning = (id) => {
        epc.deleteTabPopup = true;
        epc.deleteTabIndex = epc.getTabIndex(id);
    };

    epc.deleteTab = () => {
        if(epc.edited.tabs[epc.deleteTabIndex].newTab) {
            epc.edited.tabs.splice(epc.deleteTabIndex, 1);
        } else {
            epc.edited.tabs[epc.deleteTabIndex].deleted = true;
        }
        epc.tabsChanged = true;
        epc.deleteTabPopup = false;
        epc.deleteTabIndex = -1;
    };

    epc.deleteSectionWarning = (tabId, sectionId) => {
        epc.deleteSectionPopup = true;
        epc.deleteSectionTabIndex = epc.getTabIndex(tabId)
        epc.deleteSectionIndex = epc.getSectionIndex(epc.deleteSectionTabIndex, sectionId);
    };

    epc.deleteSection = () => {
        if(epc.edited.tabs[epc.deleteSectionTabIndex].sections[epc.deleteSectionIndex].newSection) {
            epc.edited.tabs[epc.deleteSectionTabIndex].sections.splice(epc.deleteSectionIndex, 1);
        } else {
            epc.edited.tabs[epc.deleteSectionTabIndex].sections[epc.deleteSectionIndex].deleted = true;
        }
        epc.sectionsChanged = true;
        epc.deleteSectionPopup = false;
        epc.deleteSectionIndex = -1;
        epc.deleteSectionTabIndex = -1;
    };

    epc.cancelDeleteTab = () => {
        epc.deleteTabPopup = false;
        epc.deleteTabIndex = -1;
    };

    epc.cancelDeleteSection = () => {
        epc.deleteSectionPopup = false;
        epc.deleteSectionIndex = -1;
        epc.deleteSectionTabIndex = -1;
    };

    epc.createNewTab = () => {
        epc.newTabID++;
        let newTabOrder = 0;
        for (let i=0; i < (epc.edited.tabs.length); i++) {
            if(epc.edited.tabs[i].tabOrder > newTabOrder) {
                newTabOrder = epc.edited.tabs[i].tabOrder;
            }
        }
        newTabOrder++;
        epc.edited.tabs.push({
            newTab: true,
            title: 'New Tab',
            tabOrder: newTabOrder,
            id: `n${epc.newTabID}`,
            link: null,
            sections: []
        });
        epc.tabsChanged = true;
    };

    epc.getTabIndex = (id) => {
        for (let i=0; i < (epc.edited.tabs.length); i++) {
            if(epc.edited.tabs[i].id === id) {
                return i;
            }
        }
    };

    epc.getSectionIndex = (tabIndex, sectionId) => {
        for (let i=0; i < (epc.edited.tabs[tabIndex].sections.length); i++) {
            if(epc.edited.tabs[tabIndex].sections[i].id === sectionId) {
                return i;
            }
        }
    };

    epc.getLocationIndex = (id) => {
        for (let i=0; i < (epc.edited.locations.length); i++) {
            if(epc.edited.locations[i].id === id) {
                return i;
            }
        }
    };

    epc.getQuestionIndex = (id) => {
        for (let i=0; i < (epc.edited.questions.length); i++) {
            if(epc.edited.questions[i].id === id) {
                return i;
            }
        }
    };

    epc.createNewSection = (tabId) => {
        epc.newTabID++;
        let tabIndex = epc.getTabIndex(tabId);
        let newSectionOrder = 0;
        for (let i=0; i < (epc.edited.tabs[tabIndex].sections.length); i++) {
            if(epc.edited.tabs[tabIndex].sections[i].sectionOrder > newSectionOrder) {
                newSectionOrder = epc.edited.tabs[tabIndex].sections[i].sectionOrder;
            }
        }
        newSectionOrder++;
        epc.edited.tabs[tabIndex].sections.push({
            newSection: true,
            title: 'New Section',
            sectionOrder: newSectionOrder,
            id: `n${epc.newTabID}`,
            buttonLink: null,
            buttonText: null,
            image: null,
            imagePosition: 'article-image-left',
            text: ''
        });
        epc.sectionsChanged = true;
    };

    epc.moveImage = (tabId, sectionId, direction) => {
        let tabIndex = epc.getTabIndex(tabId);
        epc.edited.tabs[tabIndex].sections[epc.getSectionIndex(tabIndex, sectionId)].imagePosition = `article-image-${direction}`;
        epc.sectionsChanged = true;
    };

    epc.deleteImage = (tabId, sectionId) => {
        let tabIndex = epc.getTabIndex(tabId);
        let sectionIndex = epc.getSectionIndex(tabIndex, sectionId);
        epc.edited.tabs[tabIndex].sections[sectionIndex].imagePosition = null;
        epc.edited.tabs[tabIndex].sections[sectionIndex].image = null;
        epc.sectionsChanged = true;
    };

    epc.deleteButton = (tabId, sectionId) => {
        let tabIndex = epc.getTabIndex(tabId);
        let sectionIndex = epc.getSectionIndex(tabIndex, sectionId);
        epc.edited.tabs[tabIndex].sections[sectionIndex].buttonLink = null;
        epc.edited.tabs[tabIndex].sections[sectionIndex].buttonText = null;
        epc.sectionsChanged = true;
    };

    epc.createNewLink = (tabId, sectionId) => {
        let tabIndex = epc.getTabIndex(tabId);
        let sectionIndex = epc.getSectionIndex(tabIndex, sectionId);
        epc.edited.tabs[tabIndex].sections[sectionIndex].buttonLink = '#';
        epc.edited.tabs[tabIndex].sections[sectionIndex].buttonText = 'New Button';
        epc.edited.tabs[tabIndex].sections[sectionIndex].buttonNew = true;
        epc.sectionsChanged = true;
    };

    epc.moveLocation = function (direction, originalLocationOrder, id) {
        let swapLocationId = -1;
        epc.edited.locations.forEach(function (location) {
            if(direction === 'right') {
                if(location.locationOrder === (originalLocationOrder + 1)) {
                    swapLocationId = location.id;
                }
            } else {
                if(location.locationOrder === (originalLocationOrder - 1)) {
                    swapLocationId = location.id;
                }
            }
        });
        for (let i=0; i < (epc.edited.locations.length); i++) {
            if(direction === 'right') {
                if(epc.edited.locations[i].id === id) {
                    epc.edited.locations[i].locationOrder++;
                }
                if(epc.edited.locations[i].id === swapLocationId) {
                    epc.edited.locations[i].locationOrder--;
                }
            } else {
                if(epc.edited.locations[i].id === id) {
                    epc.edited.locations[i].locationOrder--;
                }
                if(epc.edited.locations[i].id === swapLocationId) {
                    epc.edited.locations[i].locationOrder++;
                }
            }
        }
        epc.locationsChanged = true;
    };

    epc.deleteLocation = (id) => {
        let locationIndex = epc.getLocationIndex(id);
        for (let i=0; i < (epc.edited.locations.length); i++) {
            if(epc.edited.locations[i].locationOrder>epc.edited.locations[locationIndex].locationOrder) {
                epc.edited.locations[i].locationOrder--;
            }
        }
        if (!epc.edited.locations[locationIndex].newLocation) {
            epc.edited.locations[locationIndex].deleted = true;
        } else {
            epc.edited.locations.splice(locationIndex, 1);
        }
        
        epc.locationsChanged = true;
    };

    epc.enableApplyButton = () => {
        epc.edited.displayApplyButton = true;
        epc.applyButtonChanged = true;
    };

    epc.disableApplyButton = () => {
        epc.edited.displayApplyButton = false;
        epc.applyButtonChanged = true;
    };

    epc.moveQuestion = function (direction, originalQuestionOrder, id) {
        let swapQuestionId = -1;
        epc.edited.questions.forEach(function (question) {
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
        for (let i=0; i < (epc.edited.questions.length); i++) {
            if(direction === 'down') {
                if(epc.edited.questions[i].id === id) {
                    epc.edited.questions[i].questionOrder++;
                }
                if(epc.edited.questions[i].id === swapQuestionId) {
                    epc.edited.questions[i].questionOrder--;
                }
            } else {
                if(epc.edited.questions[i].id === id) {
                    epc.edited.questions[i].questionOrder--;
                }
                if(epc.edited.questions[i].id === swapQuestionId) {
                    epc.edited.questions[i].questionOrder++;
                }
            }
        }
        epc.questionsChanged = true;
    };

    epc.addNewQuestion = () => {
        epc.newTabID++;
        let newQuestionOrder = -1;
        for (let i=0; i < (epc.edited.questions.length); i++) {
            if(epc.edited.questions[i].questionOrder > newQuestionOrder) {
                newQuestionOrder = epc.edited.questions[i].questionOrder;
            }
        }
        newQuestionOrder++;
        epc.edited.questions.push({
            id: `n${epc.newTabID}`,
            question: 'New Question',
            answer: 'New Answer',
            questionOrder: newQuestionOrder,
            newQuestion: true
        });
        epc.questionsChanged = true;
    };

    epc.deleteQuestion = (id) => {
        let questionIndex = epc.getQuestionIndex(id);
        for (let i=0; i < (epc.edited.questions.length); i++) {
            if(epc.edited.questions[i].questionOrder>epc.edited.questions[questionIndex].questionOrder) {
                epc.edited.questions[i].questionOrder--;
            }
        }
        if (epc.edited.questions[questionIndex].questionNew) {
            epc.edited.questions.splice(questionIndex, 1);
        } else {
            epc.edited.questions[questionIndex].deleted = true;
        }
        epc.questionsChanged = true;
    };

    epc.selectImage = function (tabId, sectionId, newImage) {
        let tabIndex = epc.getTabIndex(tabId);
        let sectionIndex = epc.getSectionIndex(tabIndex, sectionId);
        epc.edited.tabs[tabIndex].sections[sectionIndex].image = newImage;
        if(!epc.edited.tabs[tabIndex].sections[sectionIndex].imagePosition) {
            epc.edited.tabs[tabIndex].sections[sectionIndex].imagePosition = 'article-image-left';
        }
        epc.sectionsChanged = true;
        epc.edited.tabs[tabIndex].sections[sectionIndex].imageOpen = false;
    };

    epc.openGallery = function (tabId, sectionId) {
        epc.galleryPage = 0;
        let tabIndex = epc.getTabIndex(tabId);
        let sectionIndex = epc.getSectionIndex(tabIndex, sectionId);
        adminService.getImages()
        .then(function (data) {
            epc.images = data;
        }, function (e) {
            console.error(e);
        })
        .then(function () {
            epc.edited.tabs[tabIndex].sections[sectionIndex].imageOpen = true;
        });
    };

    epc.changeGalleryPage = function (direction) {
        if(direction === 'prev') {
            epc.galleryPage = epc.galleryPage - 30;
        } else {
            epc.galleryPage = epc.galleryPage + 30;
        }
    };

    epc.gallerySearch = function () {
        epc.galleryPage = 0;
    };

    epc.uploadImage = function (tabId, sectionId) {
        epc.fileError = false;
        if (epc.uploadFile.type === 'image/png' ||
            epc.uploadFile.type === 'image/gif' ||
            epc.uploadFile.type === 'image/jpeg') {
            adminService.uploadImage(epc.uploadFile)
            .then((r) => {
                epc.selectImage(tabId, sectionId, r.newFilename)
            }, (e) => {
                console.error(e);
                epc.fileError = 'Sorry, there was some sort of error uploading the image.';
            });
        } else {
            console.error('file type not allowed');
            epc.fileError = 'Sorry, only .jpg, .jpeg, .gif, or .png files are allowed.';
        }
    };

    epc.openHeroGallery = function () {
        heroImageService.getHeroes()
        .then(function (data) {
            epc.heroes = data;
        }, function (e) {
            console.error('unable to fetch heroes');
            console.error(e);
        });
        epc.heroOpen = true;
    };

    epc.selectHero = function (heroId) {
        epc.edited.heroId = heroId;
        epc.heroChanged = true;
        epc.heroOpen = false;
    }

    epc.selectMetaImage = function (module, newImage) {
        epc.edited[`${module}Image`] = newImage;
        epc[`${module}ImageChanged`] = true;
        epc[`${module}ImageOpen`] = false;
    };

    epc.openMetaGallery = function (module) {
        epc.galleryPage = 0;
        adminService.getImages()
        .then(function (data) {
            epc.images = data;
        }, function (e) {
            console.error(e);
        })
        .then(function () {
            epc[`${module}ImageOpen`] = true;
        });
    };

    epc.uploadMetaImage = function (module) {
        epc.fileError = false;
        if (epc.uploadFile.type === 'image/png' ||
            epc.uploadFile.type === 'image/gif' ||
            epc.uploadFile.type === 'image/jpeg') {
            adminService.uploadImage(epc.uploadFile)
            .then((r) => {
                $timeout(() => {
                    epc.selectMetaImage(module, r.newFilename)
                }, 1000);
            }, (e) => {
                console.error(e);
                epc.fileError = 'Sorry, there was some sort of error uploading the image.';
            });
        } else {
            console.error('file type not allowed');
            epc.fileError = 'Sorry, only .jpg, .jpeg, .gif, or .png files are allowed.';
        }
    };

    epc.selectLocation = function (locationIndex, locationId) {
        epc.edited.locations.push(angular.copy(epc.allLocations[locationIndex]));
        let newLocationIndex = epc.getLocationIndex(locationId);
        epc.edited.locations[newLocationIndex].locationNew = true;
        let newLocationOrder = -1;
        for (let i=0; i < (epc.edited.locations.length); i++) {
            if(epc.edited.locations[i].id!==locationId) {
                if(epc.edited.locations[i].locationOrder > newLocationOrder) {
                    newLocationOrder = epc.edited.locations[i].locationOrder;
                }
            }
        }
        newLocationOrder++;
        epc.edited.locations[newLocationIndex].locationOrder = newLocationOrder;
        epc.locationsChanged = true;
        epc.locationsOpen = false;
    };

    epc.openLocations = function () {
        adminService.getLocations()
        .then(function (data) {
            epc.allLocations = data;
        }, function (e) {
            console.error(e);
        })
        .then(function () {
            for (let i=0; i < epc.edited.locations.length; i++) {
                for(let r=0; r < epc.allLocations.length; r++) {
                    if(epc.allLocations[r].id === epc.edited.locations[i].id) {
                        epc.allLocations[r].existing = true;
                    }
                }
            }
        })
        .then(function () {
            epc.locationsOpen = true;
        });
    };

    epc.submitEdit = () => {
        epc.newRevisionDescriptionOpen = false;
        programService.updateProgram(epc.edited.id, epc.edited)
        .then(function () {
            $location.path(`/admin/edit-program/${epc.edited.link}`);
            epc.init();
        }, function (e) {
            if(e.data.error) {
                epc[`${e.data.module}Error`] = e.data.status;
                epc.responseError = e.data.status;
                epc.responseErrorPopup = true;
            }
            console.error(e);
        });
    };

    epc.init();
}