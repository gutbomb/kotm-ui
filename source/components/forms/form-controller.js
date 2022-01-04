export default function ($location, formService, $routeParams, $window) {
    let fc = this;

    fc.init = function () {
        fc.success = false;
        fc.error = false;
        fc.submitting = false;
        fc.uploading = false;
        formService.getForm($routeParams.formUrl)
        .then(function (form) {
            fc.form = form;
        }, function (e) {
            if (e.status === 404) {
                $location.path(`/error/form/${$routeParams.formUrl}`);
            } else {
                fc.error = true;
                fc.errorMessage = e.data.error;
            }
        });
    };

    fc.submitForm = function () {
        fc.submitting = true;
        formService.submitForm(fc.form)
        .then(function () {
            fc.submitting = false;
            fc.success = true;
            var dataLayer = $window.dataLayer = $window.dataLayer || [];
            dataLayer.push({
                event: 'formSubmit',
                attributes: {
                    title: fc.form.title
                }
            });
        }, function (e) {
            fc.submitting = false;
            fc.error = true;
            fc.errorMessage = e.data.error;
        });
    };

    fc.uploadFile = function(sectionId, fieldId) {
        fc.uploading = true;
        let sectionIndex = fc.getSectionIndex(sectionId);
        let fieldIndex = fc.getFieldIndex(sectionIndex, fieldId);
        let filename = fc.form.sections[sectionIndex].fields[fieldIndex].value[0].name;
        formService.uploadFile(fc.form.sections[sectionIndex].fields[fieldIndex].value[0])
        .then(function (r) {
            fc.uploading = false;
            fc.form.sections[sectionIndex].fields[fieldIndex].type = 'uploadFile';
            fc.form.sections[sectionIndex].fields[fieldIndex].value = `${r.prefix}${filename}`;
        });
    };

    fc.removeFile = function(sectionId, fieldId) {
        let sectionIndex = fc.getSectionIndex(sectionId);
        let fieldIndex = fc.getFieldIndex(sectionIndex, fieldId);
        fc.form.sections[sectionIndex].fields[fieldIndex].type = 'file';
        fc.form.sections[sectionIndex].fields[fieldIndex].value = '';
    };

    fc.getSectionIndex = (sectionId) => {
        for (let i=0; i < (fc.form.sections.length); i++) {
            if(fc.form.sections[i].id === sectionId) {
                return i;
            }
        }
    };

    fc.getFieldIndex = (sectionIndex, fieldId) => {
        for (let i=0; i < (fc.form.sections[sectionIndex].fields.length); i++) {
            if(fc.form.sections[sectionIndex].fields[i].id === fieldId) {
                return i;
            }
        }
    };

    fc.init();
};




