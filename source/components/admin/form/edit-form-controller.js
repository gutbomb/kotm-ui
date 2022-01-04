export default function ($location, formService, $routeParams, $window, $anchorScroll, $timeout) {
    let efc = this;

    efc.init = function () {
        efc.formChanged = false;
        efc.colorChanged = false;
        efc.colorOpen = false;
        efc.descriptionChanged = false;
        efc.descriptionOpen = false;
        efc.urlChanged = false;
        efc.titleChanged = false;
        efc.titleOpen = false;
        efc.metaDescriptionChanged = false;
        efc.metaTitleChanged = false;
        efc.metaKeywordsChanged = false;
        efc.success = false;
        efc.error = false;
        efc.submitting = false;
        efc.uploading = false;
        efc.deletedSection = {};
        efc.deletedField = {};
        efc.deletedItem = {};
        efc.newItemId = 0;
        efc.submitOpen = false;
        efc.submitChanged = false;
        efc.emailSubjectOpen = false;
        efc.emailSubjectChanged = false;
        efc.successTitleChanged = false;
        efc.successTitleOpen = false;
        efc.successMessageChanged = false;
        efc.successMessageOpen = false;
        efc.recipientsChanged = false;
        efc.recipientsOpen = false;
        formService.getForm($routeParams.formUrl)
        .then(function (form) {
            efc.form = angular.copy(form);
            efc.edited = angular.copy(form);
        }, function (e) {
            if (e.status === 404) {
                $location.path(`/error/form/${$routeParams.formUrl}`);
            } else {
                efc.error = true;
                efc.errorMessage = e.data.error;
            }
        });
        efc.ckeditorConfig = {
            bodyClass: 'article-wrapper-content',
            contentsCss: '/css/styles.css',
            removePlugins: 'iframe,flash,smiley',
            removeButtons: 'Source,Styles',
            skin: 'moono',
            format_tags: 'p;h1;h2;h3',
            scayt_autoStartup: true,
            extraPlugins: 'youtube',
            removeDialogTabs: ''
        };
    };

    efc.selectColor = function (color) {
        efc.edited.color = color;
        efc.colorChanged = true;
        efc.colorOpen = false;
        if (efc.edited.layout === 'page') {
            efc.ckeditorConfig.bodyClass = `accent-${efc.edited.color} article-wrapper-content-text`;
        } else {
            efc.ckeditorConfig.bodyClass = `accent-${efc.edited.color} article-wrapper-content`;
        }
    };

    efc.encodeUrl = function () {
        efc.edited.url = encodeURI(efc.edited.url.replace(/ /g, '-').toLowerCase());
    };

    efc.saveEdit = function (module, sectionId=false, fieldId=false, itemId=false) {
        if (sectionId && module==='sectionTitle') {
            let sectionIndex = efc.getSectionIndex(sectionId);
            efc.formChanged = true;
            efc.edited.sections[sectionIndex].titleOpen = false;
        } else if (sectionId && fieldId && module==='fieldTitle') {
            let sectionIndex = efc.getSectionIndex(sectionId);
            let fieldIndex = efc.getFieldIndex(sectionIndex, fieldId);
            efc.formChanged = true;
            efc.edited.sections[sectionIndex].fields[fieldIndex].titleOpen = false;
        } else if (sectionId && fieldId && itemId && module==='itemTitle') {
            let sectionIndex = efc.getSectionIndex(sectionId);
            let fieldIndex = efc.getFieldIndex(sectionIndex, fieldId);
            let itemIndex = efc.getItemIndex(sectionIndex, fieldIndex, itemId);
            efc.formChanged = true;
            efc.edited.sections[sectionIndex].fields[fieldIndex].items[itemIndex].titleOpen = false;
        } else {
            efc[`${module}Changed`] = true;
            efc[`${module}Open`] = false;
            efc[`${module}Error`] = null;
        }
    };

    efc.cancelEdit = function (module, sectionId=false, fieldId=false, itemId=false) {
        if (sectionId && module==='sectionTitle') {
            let sectionIndex = efc.getSectionIndex(sectionId);
            let originalSectionIndex = efc.getOriginalSectionIndex(sectionId);
            efc.edited.sections[sectionIndex].title = angular.copy(efc.form.sections[originalSectionIndex].title);
            efc.edited.sections[sectionIndex].titleChanged = false;
            efc.edited.sections[sectionIndex].titleOpen = false;
        } else if (sectionId && fieldId && module==='fieldTitle') {
            let sectionIndex = efc.getSectionIndex(sectionId);
            let fieldIndex = efc.getFieldIndex(sectionIndex, fieldId);
            let originalSectionIndex = efc.getOriginalSectionIndex(sectionId);
            let originalFieldIndex = efc.getOriginalFieldIndex(originalSectionIndex, fieldId);
            efc.edited.sections[sectionIndex].fields[fieldIndex].title = angular.copy(efc.form.sections[originalSectionIndex].fields[originalFieldIndex].title);
            efc.edited.sections[sectionIndex].fields[fieldIndex].titleChanged = false;
            efc.edited.sections[sectionIndex].fields[fieldIndex].titleOpen = false;
        } else if (sectionId && fieldId && itemId && module==='itemTitle') {
            let sectionIndex = efc.getSectionIndex(sectionId);
            let fieldIndex = efc.getFieldIndex(sectionIndex, fieldId);
            let itemIndex = efc.getItemIndex(sectionIndex, fieldIndex, itemId);
            let originalSectionIndex = efc.getOriginalSectionIndex(sectionId);
            let originalFieldIndex = efc.getOriginalFieldIndex(originalSectionIndex, fieldId);
            let originalItemIndex = efc.getOriginalItemIndex(originalSectionIndex, originalFieldIndex, itemId);
            efc.edited.sections[sectionIndex].fields[fieldIndex].items[itemIndex].title = angular.copy(efc.form.sections[originalSectionIndex].fields[originalFieldIndex].items[originalItemIndex].title);
            efc.edited.sections[sectionIndex].fields[fieldIndex].items[itemIndex].titleChanged = false;
            efc.edited.sections[sectionIndex].fields[fieldIndex].items[itemIndex].titleOpen = false;
        } else if (module==='successTitle') {
            efc.form.success.title = angular.copy(efc.form.success.title);
            efc.form.successTitleChanged = false;
            efc.form.successTitleOpen = false;
        } else if (module==='successMessage') {
            efc.form.success.title = angular.copy(efc.form.success.message);
            efc.form.successMessageChanged = false;
            efc.form.successMessageOpen = false;
        } else {
            efc.edited[module] = angular.copy(efc.form[module]);
            efc[`${module}Changed`] = false;
            efc[`${module}Open`] = false;
            efc[`${module}Error`] = null;
        }
    };

    efc.getSectionIndex = (sectionId) => {
        for (let i=0; i < (efc.edited.sections.length); i++) {
            if(efc.edited.sections[i].id === sectionId) {
                return i;
            }
        }
    };

    efc.getFieldIndex = (sectionIndex, fieldId) => {
        for (let i=0; i < (efc.edited.sections[sectionIndex].fields.length); i++) {
            if(efc.edited.sections[sectionIndex].fields[i].id === fieldId) {
                return i;
            }
        }
    };

    efc.getItemIndex = (sectionIndex, fieldIndex, itemId) => {
        for (let i=0; i < (efc.edited.sections[sectionIndex].fields[fieldIndex].items.length); i++) {
            if(efc.edited.sections[sectionIndex].fields[fieldIndex].items[i].id === itemId) {
                return i;
            }
        }
    };

    efc.getOriginalSectionIndex = (sectionId) => {
        for (let i=0; i < (efc.form.sections.length); i++) {
            if(efc.form.sections[i].id === sectionId) {
                return i;
            }
        }
    };

    efc.getOriginalFieldIndex = (sectionIndex, fieldId) => {
        for (let i=0; i < (efc.form.sections[sectionIndex].fields.length); i++) {
            if(efc.form.sections[sectionIndex].fields[i].id === fieldId) {
                return i;
            }
        }
    };

    efc.getOriginalItemIndex = (sectionIndex, fieldIndex, itemId) => {
        for (let i=0; i < (efc.form.sections[sectionIndex].fields[fieldIndex].items.length); i++) {
            if(efc.form.sections[sectionIndex].fields[fieldIndex].items[i].id === itemId) {
                return i;
            }
        }
    };

    efc.selectType = (type, sectionId, fieldId) => {
        let sectionIndex = efc.getSectionIndex(sectionId);
        let fieldIndex = efc.getFieldIndex(sectionIndex, fieldId);
        efc.edited.sections[sectionIndex].fields[fieldIndex].type = type;
        efc.edited.sections[sectionIndex].fields[fieldIndex].typeOpen = false;
        efc.formChanged = true;
    }

    efc.moveSection = function (direction, originalSectionOrder, id) {
        let swapSectionId = -1;
        efc.edited.sections.forEach(function (section) {
            if(direction === 'down') {
                if(section.order === (originalSectionOrder + 1)) {
                    swapSectionId = section.id;
                }
            } else {
                if(section.order === (originalSectionOrder - 1)) {
                    swapSectionId = section.id;
                }
            }
        });
        for (let i=0; i < (efc.edited.sections.length); i++) {
            if(direction === 'down') {
                if(efc.edited.sections[i].id === id) {
                    efc.edited.sections[i].order++;
                }
                if(efc.edited.sections[i].id === swapSectionId) {
                    efc.edited.sections[i].order--;
                }
            } else {
                if(efc.edited.sections[i].id === id) {
                    efc.edited.sections[i].order--;
                }
                if(efc.edited.sections[i].id === swapSectionId) {
                    efc.edited.sections[i].order++;
                }
            }
        }
        efc.formChanged = true;
    };

    efc.moveField = function (direction, originalFieldOrder, sectionId, fieldId) {
        let swapFieldId = -1;
        let sectionIndex = efc.getSectionIndex(sectionId);
        efc.edited.sections[sectionIndex].fields.forEach(function (field) {
            if(direction === 'down') {
                if(field.order === (originalFieldOrder + 1)) {
                    swapFieldId = field.id;
                }
            } else {
                if(field.order === (originalFieldOrder - 1)) {
                    swapFieldId = field.id;
                }
            }
        });
        for (let i=0; i < (efc.edited.sections[sectionIndex].fields.length); i++) {
            if(direction === 'down') {
                if(efc.edited.sections[sectionIndex].fields[i].id === fieldId) {
                    efc.edited.sections[sectionIndex].fields[i].order++;
                }
                if(efc.edited.sections[sectionIndex].fields[i].id === swapFieldId) {
                    efc.edited.sections[sectionIndex].fields[i].order--;
                }
            } else {
                if(efc.edited.sections[sectionIndex].fields[i].id === fieldId) {
                    efc.edited.sections[sectionIndex].fields[i].order--;
                }
                if(efc.edited.sections[sectionIndex].fields[i].id === swapFieldId) {
                    efc.edited.sections[sectionIndex].fields[i].order++;
                }
            }
        }
        efc.formChanged = true;
    };

    efc.deleteSectionWarning = (sectionId) => {
        let sectionIndex = efc.getSectionIndex(sectionId);
        efc.deleteSectionPopup = true;
        efc.deletedSection = {
            sectionIndex: sectionIndex,
            title: efc.edited.sections[sectionIndex].title
        }
    };

    efc.deleteFieldWarning = (sectionId, fieldId) => {
        let sectionIndex = efc.getSectionIndex(sectionId);
        let fieldIndex = efc.getFieldIndex(sectionIndex, fieldId);
        efc.deleteFieldPopup = true;
        efc.deletedField = {
            sectionIndex: sectionIndex,
            fieldIndex: fieldIndex,
            title: efc.edited.sections[sectionIndex].fields[fieldIndex].title
        }
    };

    efc.deleteItemWarning = (sectionId, fieldId, itemId) => {
        let sectionIndex = efc.getSectionIndex(sectionId);
        let fieldIndex = efc.getFieldIndex(sectionIndex, fieldId);
        let itemIndex = efc.getItemIndex(sectionIndex, fieldIndex, itemId);
        efc.deleteItemPopup = true;
        efc.deletedItem = {
            sectionIndex: sectionIndex,
            fieldIndex: fieldIndex,
            itemIndex: itemIndex,
            title: efc.edited.sections[sectionIndex].fields[fieldIndex].items[itemIndex].title
        }
    };

    efc.deleteSection = () => {
        if (efc.edited.sections[efc.deletedSection.sectionIndex].newSection) {
            efc.edited.sections.splice(efc.deletedSection.sectionIndex, 1);
        } else {
            efc.edited.sections[efc.deletedSection.sectionIndex].deleted = true;
        }
        
        efc.deleteSectionPopup = false;
        efc.formChanged = true;
        efc.deletedSection = {};
    }

    efc.deleteField = () => {
        if(efc.edited.sections[efc.deletedField.sectionIndex].fields[efc.deletedField.fieldIndex].newField) {
            efc.edited.sections[efc.deletedField.sectionIndex].fields.splice(efc.deletedField.fieldIndex, 1);
        } else {
            efc.edited.sections[efc.deletedField.sectionIndex].fields[efc.deletedField.fieldIndex].deleted = true;
        }
        efc.deleteFieldPopup = false;
        efc.formChanged = true;
        efc.deletedField = {};
    }

    efc.deleteItem = () => {
        if (efc.edited.sections[efc.deletedItem.sectionIndex].fields[efc.deletedItem.fieldIndex].items[efc.deletedItem.itemIndex].newItem) {
            efc.edited.sections[efc.deletedItem.sectionIndex].fields[efc.deletedItem.fieldIndex].items.splice(efc.deletedItem.itemIndex, 1);
        } else {
            efc.edited.sections[efc.deletedItem.sectionIndex].fields[efc.deletedItem.fieldIndex].items[efc.deletedItem.itemIndex].deleted = true;
        }
        efc.deleteItemPopup = false;
        efc.formChanged = true;
        efc.deletedItem = {};
    }

    efc.cancelDeleteSection = () => {
        efc.deleteSectionPopup = false;
        efc.deletedSection = {};
    }

    efc.cancelDeleteItem = () => {
        efc.deleteFieldPopup = false;
        efc.deletedField = {};
    }

    efc.cancelDeleteItem = () => {
        efc.deleteItemPopup = false;
        efc.deletedItem = {};
    }

    efc.submitEdit = () => {
        if(efc.edited.url) {
            formService.updateForm(efc.edited.url, efc.edited)
            .then(function () {
                $location.path(`/admin/edit-form/${efc.edited.url}`);
                $timeout(() => {
                    efc.init()
                }, 500);
            }, function (e) {
                if(e.data.error) {
                    efc[`${e.data.module}Error`] = e.data.status;
                    efc.responseError = e.data.status;
                    efc.responseErrorPopup = true;
                }
                console.error(e);
            });
        } else {
            efc.urlError = 'invalid URL';
        }
    };

    efc.addItem = (sectionId, fieldId) => {
        efc.newItemId++;
        let sectionIndex = efc.getSectionIndex(sectionId);
        let fieldIndex = efc.getFieldIndex(sectionIndex, fieldId);
        efc.edited.sections[sectionIndex].fields[fieldIndex].items.push({
            id: `n${efc.newItemId}`,
            title: 'New Option',
            value: false,
            order: efc.edited.sections[sectionIndex].fields[fieldIndex].items.length,
            other: false,
            titleOpen: true,
            newItem: true
        });
        efc.formChanged = true;
    }

    efc.addField = (sectionId) => {
        efc.newItemId++;
        let sectionIndex = efc.getSectionIndex(sectionId);
        efc.edited.sections[sectionIndex].fields.push({
            id: `N${efc.newItemId}`,
            items: [],
            order: efc.edited.sections[sectionIndex].fields.length,
            required: false,
            title: 'New Field',
            type: 'text',
            value: '',
            newField: true
        });
        efc.formChanged = true;
        $timeout(() => {
            $anchorScroll(`FN${efc.newItemId}`);
        }, 500);
    }

    efc.addSection = () => {
        efc.newItemId++;
        efc.edited.sections.push({
            id: `N${efc.newItemId}`,
            fields: [],
            order: efc.edited.sections.length,
            title: '',
            newSection: true
        });
        efc.formChanged = true;
        $timeout(() => {
            $anchorScroll(`SN${efc.newItemId}`);
        }, 500);
    }

    efc.moveItem = function (direction, originalItemOrder, sectionId, fieldId, itemId) {
        let swapItemId = -1;
        let sectionIndex = efc.getSectionIndex(sectionId);
        let fieldIndex = efc.getFieldIndex(sectionIndex, fieldId);
        efc.edited.sections[sectionIndex].fields[fieldIndex].items.forEach(function (item) {
            if(direction === 'down') {
                if(item.order === (originalItemOrder + 1)) {
                    swapItemId = item.id;
                }
            } else {
                if(item.order === (originalItemOrder - 1)) {
                    swapItemId = item.id;
                }
            }
        });
        for (let i=0; i < (efc.edited.sections[sectionIndex].fields[fieldIndex].items.length); i++) {
            if(direction === 'down') {
                if(efc.edited.sections[sectionIndex].fields[fieldIndex].items[i].id === itemId) {
                    efc.edited.sections[sectionIndex].fields[fieldIndex].items[i].order++;
                }
                if(efc.edited.sections[sectionIndex].fields[fieldIndex].items[i].id === swapItemId) {
                    efc.edited.sections[sectionIndex].fields[fieldIndex].items[i].order--;
                }
            } else {
                if(efc.edited.sections[sectionIndex].fields[fieldIndex].items[i].id === itemId) {
                    efc.edited.sections[sectionIndex].fields[fieldIndex].items[i].order--;
                }
                if(efc.edited.sections[sectionIndex].fields[fieldIndex].items[i].id === swapItemId) {
                    efc.edited.sections[sectionIndex].fields[fieldIndex].items[i].order++;
                }
            }
        }
        efc.formChanged = true;
    };

    efc.init();
};




