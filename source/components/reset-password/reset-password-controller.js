export default function (loginService, $rootScope, $location) {
    let rpc = this;
    if ($rootScope.isLoggedIn) {
        $location.path('/admin');
    }

    rpc.inputEmail = '';
    rpc.error = '';
    rpc.success = false;

    rpc.resetPassword = function () {
        loginService.resetPassword(rpc.inputEmail)
        .then(function() {
            rpc.success=true;
            rpc.successMessage='If you have an account in this system associated with that email address you will receieve a new password shortly.';
            rpc.error='';
        },
        function() {
            rpc.success = false;
            rpc.successMessage = '';
            rpc.error='Password reset failed';
        });
    };
};