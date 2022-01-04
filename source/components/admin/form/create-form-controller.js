export default function ($location, formService, $routeParams, $window, $anchorScroll, $timeout) {
    let cfc = this;

    cfc.init = function () {
        cfc.formChanged = false;
        cfc.colorChanged = false;
        cfc.colorOpen = false;
        cfc.descriptionChanged = false;
        cfc.descriptionOpen = false;
        cfc.urlChanged = false;
        cfc.titleChanged = false;
        cfc.titleOpen = false;
        cfc.metaDescriptionChanged = false;
        cfc.metaTitleChanged = false;
        cfc.metaKeywordsChanged = false;
        cfc.success = false;
        cfc.error = false;
        cfc.submitting = false;
        cfc.uploading = false;
        cfc.deletedSection = {};
        cfc.deletedField = {};
        cfc.deletedItem = {};
        cfc.newItemId = 0;
        cfc.submitOpen = false;
        cfc.submitChanged = false;
        cfc.emailSubjectOpen = false;
        cfc.emailSubjectChanged = false;
        cfc.successTitleChanged = false;
        cfc.successTitleOpen = false;
        cfc.successMessageChanged = false;
        cfc.successMessageOpen = false;
        cfc.recipientsChanged = false;
        cfc.recipientsOpen = false;
        cfc.form = {
            title: 'New Form',
            emailSubject: '',
            recipients: '',
            description: '',
            url: '',
            color: 'green',
            success: {
                title: '',
                message: ''
            },
            sections: [],
            submit: 'Submit'
        };
        cfc.ckeditorConfig = {
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

    cfc.selectColor = function (color) {
        cfc.form.color = color;
        cfc.colorChanged = true;
        cfc.colorOpen = false;
        if (cfc.form.layout === 'page') {
            cfc.ckeditorConfig.bodyClass = `accent-${cfc.form.color} article-wrapper-content-text`;
        } else {
            cfc.ckeditorConfig.bodyClass = `accent-${cfc.form.color} article-wrapper-content`;
        }
    };

    cfc.encodeUrl = function () {
        cfc.form.url = encodeURI(cfc.form.url.replace(/ /g, '-').toLowerCase());
    };

    cfc.saveEdit = function (module, sectionId=false, fieldId=false, itemId=false) {
        if (sectionId && module==='sectionTitle') {
            let sectionIndex = cfc.getSectionIndex(sectionId);
            cfc.formChanged = true;
            cfc.form.sections[sectionIndex].titleOpen = false;
            console.log(cfc.form);
        } else if (sectionId && fieldId && module==='fieldTitle') {
            let sectionIndex = cfc.getSectionIndex(sectionId);
            let fieldIndex = cfc.getFieldIndex(sectionIndex, fieldId);
            cfc.formChanged = true;
            cfc.form.sections[sectionIndex].fields[fieldIndex].titleOpen = false;
        } else if (sectionId && fieldId && itemId && module==='itemTitle') {
            let sectionIndex = cfc.getSectionIndex(sectionId);
            let fieldIndex = cfc.getFieldIndex(sectionIndex, fieldId);
            let itemIndex = cfc.getItemIndex(sectionIndex, fieldIndex, itemId);
            cfc.formChanged = true;
            cfc.form.sections[sectionIndex].fields[fieldIndex].items[itemIndex].titleOpen = false;
        } else {
            cfc[`${module}Changed`] = true;
            cfc[`${module}Open`] = false;
            cfc[`${module}Error`] = null;
        }
    };

    cfc.cancelEdit = function (module, sectionId=false, fieldId=false, itemId=false) {
        if (sectionId && module==='sectionTitle') {
            let sectionIndex = cfc.getSectionIndex(sectionId);
            cfc.form.sections[sectionIndex].title = '';
            cfc.form.sections[sectionIndex].titleChanged = false;
            cfc.form.sections[sectionIndex].titleOpen = false;
        } else if (sectionId && fieldId && module==='fieldTitle') {
            let sectionIndex = cfc.getSectionIndex(sectionId);
            let fieldIndex = cfc.getFieldIndex(sectionIndex, fieldId);
            cfc.form.sections[sectionIndex].fields[fieldIndex].title = '';
            cfc.form.sections[sectionIndex].fields[fieldIndex].titleChanged = false;
            cfc.form.sections[sectionIndex].fields[fieldIndex].titleOpen = false;
        } else if (sectionId && fieldId && itemId && module==='itemTitle') {
            let sectionIndex = cfc.getSectionIndex(sectionId);
            let fieldIndex = cfc.getFieldIndex(sectionIndex, fieldId);
            let itemIndex = cfc.getItemIndex(sectionIndex, fieldIndex, itemId);
            cfc.form.sections[sectionIndex].fields[fieldIndex].items[itemIndex].title = '';
            cfc.form.sections[sectionIndex].fields[fieldIndex].items[itemIndex].titleChanged = false;
            cfc.form.sections[sectionIndex].fields[fieldIndex].items[itemIndex].titleOpen = false;
        } else if (module==='successTitle') {
            cfc.form.success.title = '';
            cfc.form.successTitleChanged = false;
            cfc.form.successTitleOpen = false;
        } else if (module==='successMessage') {
            cfc.form.success.title = '';
            cfc.form.successMessageChanged = false;
            cfc.form.successMessageOpen = false;
        } else {
            cfc.form[module] = '';
            cfc[`${module}Changed`] = false;
            cfc[`${module}Open`] = false;
            cfc[`${module}Error`] = null;
        }
    };

    cfc.getSectionIndex = (sectionId) => {
        for (let i=0; i < (cfc.form.sections.length); i++) {
            if(cfc.form.sections[i].id === sectionId) {
                return i;
            }
        }
    };

    cfc.getFieldIndex = (sectionIndex, fieldId) => {
        console.log(sectionIndex);
        for (let i=0; i < (cfc.form.sections[sectionIndex].fields.length); i++) {
            if(cfc.form.sections[sectionIndex].fields[i].id === fieldId) {
                return i;
            }
        }
    };

    cfc.getItemIndex = (sectionIndex, fieldIndex, itemId) => {
        for (let i=0; i < (cfc.form.sections[sectionIndex].fields[fieldIndex].items.length); i++) {
            if(cfc.form.sections[sectionIndex].fields[fieldIndex].items[i].id === itemId) {
                return i;
            }
        }
    };

    cfc.selectType = (type, sectionId, fieldId) => {
        let sectionIndex = cfc.getSectionIndex(sectionId);
        let fieldIndex = cfc.getFieldIndex(sectionIndex, fieldId);
        cfc.form.sections[sectionIndex].fields[fieldIndex].type = type;
        cfc.form.sections[sectionIndex].fields[fieldIndex].typeOpen = false;
        cfc.formChanged = true;
    }

    cfc.moveSection = function (direction, originalSectionOrder, id) {
        let swapSectionId = -1;
        cfc.form.sections.forEach(function (section) {
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
        for (let i=0; i < (cfc.form.sections.length); i++) {
            if(direction === 'down') {
                if(cfc.form.sections[i].id === id) {
                    cfc.form.sections[i].order++;
                }
                if(cfc.form.sections[i].id === swapSectionId) {
                    cfc.form.sections[i].order--;
                }
            } else {
                if(cfc.form.sections[i].id === id) {
                    cfc.form.sections[i].order--;
                }
                if(cfc.form.sections[i].id === swapSectionId) {
                    cfc.form.sections[i].order++;
                }
            }
        }
        cfc.formChanged = true;
    };

    cfc.moveField = function (direction, originalFieldOrder, sectionId, fieldId) {
        let swapFieldId = -1;
        let sectionIndex = cfc.getSectionIndex(sectionId);
        cfc.form.sections[sectionIndex].fields.forEach(function (field) {
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
        for (let i=0; i < (cfc.form.sections[sectionIndex].fields.length); i++) {
            if(direction === 'down') {
                if(cfc.form.sections[sectionIndex].fields[i].id === fieldId) {
                    cfc.form.sections[sectionIndex].fields[i].order++;
                }
                if(cfc.form.sections[sectionIndex].fields[i].id === swapFieldId) {
                    cfc.form.sections[sectionIndex].fields[i].order--;
                }
            } else {
                if(cfc.form.sections[sectionIndex].fields[i].id === fieldId) {
                    cfc.form.sections[sectionIndex].fields[i].order--;
                }
                if(cfc.form.sections[sectionIndex].fields[i].id === swapFieldId) {
                    cfc.form.sections[sectionIndex].fields[i].order++;
                }
            }
        }
        cfc.formChanged = true;
    };

    cfc.deleteSectionWarning = (sectionId) => {
        let sectionIndex = cfc.getSectionIndex(sectionId);
        cfc.deleteSectionPopup = true;
        cfc.deletedSection = {
            sectionIndex: sectionIndex,
            title: cfc.form.sections[sectionIndex].title
        }
    };

    cfc.deleteFieldWarning = (sectionId, fieldId) => {
        let sectionIndex = cfc.getSectionIndex(sectionId);
        let fieldIndex = cfc.getFieldIndex(sectionIndex, fieldId);
        cfc.deleteFieldPopup = true;
        cfc.deletedField = {
            sectionIndex: sectionIndex,
            fieldIndex: fieldIndex,
            title: cfc.form.sections[sectionIndex].fields[fieldIndex].title
        }
    };

    cfc.deleteItemWarning = (sectionId, fieldId, itemId) => {
        let sectionIndex = cfc.getSectionIndex(sectionId);
        let fieldIndex = cfc.getFieldIndex(sectionIndex, fieldId);
        let itemIndex = cfc.getItemIndex(sectionIndex, fieldIndex, itemId);
        cfc.deleteItemPopup = true;
        cfc.deletedItem = {
            sectionIndex: sectionIndex,
            fieldIndex: fieldIndex,
            itemIndex: itemIndex,
            title: cfc.form.sections[sectionIndex].fields[fieldIndex].items[itemIndex].title
        }
    };

    cfc.deleteSection = () => {
        cfc.form.sections.splice(cfc.deletedSection.sectionIndex, 1);
        cfc.deleteSectionPopup = false;
        cfc.formChanged = true;
        cfc.deletedSection = {};
    }

    cfc.deleteField = () => {
        cfc.form.sections[cfc.deletedField.sectionIndex].fields.splice(cfc.deletedField.fieldIndex, 1);
        cfc.deleteFieldPopup = false;
        cfc.formChanged = true;
        cfc.deletedField = {};
    }

    cfc.deleteItem = () => {
        cfc.form.sections[cfc.deletedItem.sectionIndex].fields[cfc.deletedItem.fieldIndex].items.splice(cfc.deletedItem.itemIndex, 1);
        cfc.deleteItemPopup = false;
        cfc.formChanged = true;
        cfc.deletedItem = {};
    }

    cfc.cancelDeleteSection = () => {
        cfc.deleteSectionPopup = false;
        cfc.deletedSection = {};
    }

    cfc.cancelDeleteItem = () => {
        cfc.deleteFieldPopup = false;
        cfc.deletedField = {};
    }

    cfc.cancelDeleteItem = () => {
        cfc.deleteItemPopup = false;
        cfc.deletedItem = {};
    }

    cfc.submitEdit = () => {
        if(cfc.form.url) {
            formService.createForm(cfc.form)
            .then(function () {
                $timeout(() => {
                    $location.path(`/admin/edit-form/${cfc.form.url}`);
                }, 500);
            }, function (e) {
                if(e.data.error) {
                    cfc[`${e.data.module}Error`] = e.data.status;
                    cfc.responseError = e.data.status;
                    cfc.responseErrorPopup = true;
                }
                console.error(e);
            });
        } else {
            cfc.urlError = 'invalid URL';
        }
    };

    cfc.addItem = (sectionId, fieldId) => {
        cfc.newItemId++;
        let sectionIndex = cfc.getSectionIndex(sectionId);
        let fieldIndex = cfc.getFieldIndex(sectionIndex, fieldId);
        cfc.form.sections[sectionIndex].fields[fieldIndex].items.push({
            id: `n${cfc.newItemId}`,
            title: 'New Option',
            value: false,
            order: cfc.form.sections[sectionIndex].fields[fieldIndex].items.length,
            other: false,
            titleOpen: true,
            newItem: true
        });
        cfc.formChanged = true;
    }

    cfc.addField = (sectionId) => {
        cfc.newItemId++;
        let sectionIndex = cfc.getSectionIndex(sectionId);
        cfc.form.sections[sectionIndex].fields.push({
            id: `N${cfc.newItemId}`,
            items: [],
            order: cfc.form.sections[sectionIndex].fields.length,
            required: false,
            title: 'New Field',
            type: 'text',
            value: '',
            newField: true
        });
        cfc.formChanged = true;
        $timeout(() => {
            $anchorScroll(`FN${cfc.newItemId}`);
        }, 500);
    }

    cfc.addSection = () => {
        cfc.newItemId++;
        cfc.form.sections.push({
            id: `N${cfc.newItemId}`,
            fields: [],
            order: cfc.form.sections.length,
            title: '',
            newSection: true
        });
        cfc.formChanged = true;
        $timeout(() => {
            $anchorScroll(`SN${cfc.newItemId}`);
        }, 500);
    }

    cfc.moveItem = function (direction, originalItemOrder, sectionId, fieldId, itemId) {
        let swapItemId = -1;
        let sectionIndex = cfc.getSectionIndex(sectionId);
        let fieldIndex = cfc.getFieldIndex(sectionIndex, fieldId);
        cfc.form.sections[sectionIndex].fields[fieldIndex].items.forEach(function (item) {
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
        for (let i=0; i < (cfc.form.sections[sectionIndex].fields[fieldIndex].items.length); i++) {
            if(direction === 'down') {
                if(cfc.form.sections[sectionIndex].fields[fieldIndex].items[i].id === itemId) {
                    cfc.form.sections[sectionIndex].fields[fieldIndex].items[i].order++;
                }
                if(cfc.form.sections[sectionIndex].fields[fieldIndex].items[i].id === swapItemId) {
                    cfc.form.sections[sectionIndex].fields[fieldIndex].items[i].order--;
                }
            } else {
                if(cfc.form.sections[sectionIndex].fields[fieldIndex].items[i].id === itemId) {
                    cfc.form.sections[sectionIndex].fields[fieldIndex].items[i].order--;
                }
                if(cfc.form.sections[sectionIndex].fields[fieldIndex].items[i].id === swapItemId) {
                    cfc.form.sections[sectionIndex].fields[fieldIndex].items[i].order++;
                }
            }
        }
        cfc.formChanged = true;
    };

    cfc.init();
};




