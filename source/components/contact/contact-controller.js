export default function (contactService, $window) {
    let cc = this;
    
    cc.submitContact = function () {
        contactService.submitContact(cc.contact)
        .then(function (r) {
            cc.success = true;
            var dataLayer = $window.dataLayer = $window.dataLayer || [];
                dataLayer.push({
                    event: 'formSubmit',
                    attributes: {
                        title: 'Contact'
                    }
                });
        }, function (e) {
            cc.error = true;
        })
    };
};