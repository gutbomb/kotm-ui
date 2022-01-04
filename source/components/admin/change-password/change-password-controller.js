export default function (loginService, $rootScope, $location) {
    let cpc = this;
    
    cpc.inputOriginalPassword = '';
    cpc.inputPassword = '';
    cpc.inputConfirmPassword = '';
    cpc.error = false;

    cpc.changePassword = function () {
        if(cpc.inputOriginalPassword === '') {
            cpc.error = 'Please supply your original password';
        }
        if(cpc.inputPassword !== cpc.inputConfirmPassword) {
            cpc.error = 'Your passwords do not match.  Please try again';
        }
        if(!cpc.error) {
            loginService.changePassword(cpc.inputOriginalPassword, cpc.inputPassword)
            .then(function () {
                $location.path('/admin');
            }, function (e) {
                cpc.error = e;
                console.error(e);
            })
        }
    };
};