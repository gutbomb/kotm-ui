/*************************************************************/
/* Modules                                                   */
/*************************************************************/
var app = angular.module('homeAssessmentApp', ['ngRoute', 'ngSanitize', 'angularMoment', 'ui.bootstrap', 'ngAnimate']);

/*************************************************************/
/* Configuration                                             */
/*************************************************************/
var appConfig = {
    apiUrl: '/assessments/api'
};

app.config(function ($httpProvider) {
    $httpProvider.interceptors.push('authenticationInterceptor');
});

/*************************************************************/
/* Injectors                                                 */
/*************************************************************/
var authenticationInterceptor = function ($injector) {
    return {
        request: function (config) {
            if (config.url.indexOf(appConfig.apiUrl + '/auth') < 0 && config.url.indexOf(appConfig.apiUrl) >= 0) {
                var token = $injector.get('loginService').getToken();
                if (token) {
                    config.headers.Authorization = 'Bearer ' + token;
                }
            }

            return config;
        },

        response: function (res) {
            if (res.config.url.indexOf(appConfig.apiUrl + '/auth') >= 0 && res.data.jwt) {
                var loginService = $injector.get('loginService');
                loginService.saveToken(res.data.jwt);
            }

            return res;
        }
    };
};

/*************************************************************/
/* Services                                                  */
/*************************************************************/
var loginService = function ($q, $http, $window, $rootScope, $location) {
    function changePassword (oldPassword, newPassword) {
        return $http.put(
            appConfig.apiUrl + '/change-password',
            {
                email: $rootScope.user.email,
                newPassword: newPassword,
                password: oldPassword
            }
        )
        .then(function (response) {
            return response.data;
        }, function (error) {
            return $q.reject(error);
        });
    }

    function clearToken () {
        $window.localStorage.clear();
    }

    function getActiveUser () {
        return this.activeUser;
    }

    function getToken () {
        return $window.localStorage['homeAssessmentToken'];
    }

    function getUser (userId) {
        return $http.get(appConfig.apiUrl + '/user/' + userId).then(function (response) {
            return response.data;
        });
    }

    function getUserId () {
        return parseToken(getToken()).data.id;
    }

    function isAdmin (userId) {
        return this.getUser(userId).then(function (user) {
            return user.userLevel === 'admin';
        });
    }

    function isAuthenticated () {
        var token = this.getToken();

        return !!token;
    }

    function loginUser (email, password) {
        var defer = $q.defer();
        var self = this;
        $http({
            data: {email: email, password: password},
            headers: {'Content-Type': 'application/json'},
            method: 'POST',
            url: appConfig.apiUrl + '/auth'
        }).then(function (resp) {
            if (resp.data && resp.data.jwt) {
                self.activeUser = self.parseToken(resp.data.jwt).id;
            }
            defer.resolve(true);
        }, function (response) {
            if (response.status === 405) {
                defer.resolve(false);
            } else if (response.status === 304) {
                defer.resolve(true);
            } else {
                defer.resolve(false);
            }
        })
        .catch(function (error) {
            defer.reject(error);
        });

        return defer.promise;
    }

    function logoutUser () {
        clearToken();
        $rootScope.isLoggedIn = false;
        $location.path('/');
    }

    function parseToken (token) {
        var base64Url = token.split('.')[1];
        var base64 = base64Url.replace('-', '+').replace('_', '/');
        return JSON.parse($window.atob(base64));
    }

    function resetPassword (email) {
        return $http.put(appConfig.apiUrl + '/reset-password/' + email)
        .then(function (response) {
            return response.data;
        })
        .catch(function (error) {
            return $q.reject(error);
        });
    }

    function saveToken (token) {
        $window.localStorage['homeAssessmentToken'] = token;
    }

    return {
        changePassword: changePassword,
        clearToken: clearToken,
        getActiveUser: getActiveUser,
        getToken: getToken,
        getUser: getUser,
        getUserId: getUserId,
        isAdmin: isAdmin,
        isAuthenticated: isAuthenticated,
        loginUser: loginUser,
        logoutUser: logoutUser,
        parseToken: parseToken,
        resetPassword: resetPassword,
        saveToken: saveToken
    };
};

/*************************************************************/
/* Directives                                                */
/*************************************************************/
var topNav = function () {
    return {
        controller: 'topNavController',
        controllerAs: 'tnc',
        restrict: 'E',
        templateUrl: 'components/top-nav/top-nav-template.html'
    };
};

/*************************************************************/
/* Controllers                                               */
/*************************************************************/
var appointmentsController = function ($rootScope, $http, $timeout) {
    var apc = this;

    apc.dateFormat = 'M/D/YYYY h:mm A';

    apc.addSession = function () {
        if (apc.clientSelect === '-1') {
            $http.post(
                appConfig.apiUrl + '/home-client',
                {
                    "email": apc.email,
                    "firstName": apc.firstName,
                    "language": apc.language,
                    "lastName": apc.lastName
                }
            )
            .then(function (response) {
                apc.createSession(response.data.id);
            });
        } else {
            apc.createSession(apc.homeClients[apc.clientSelect].id);
        }
    };

    apc.cancelEdit = function (index) {
        apc.editAffirm = '';
        apc.editNotes = '';
        apc.homeResponses[index].edit = false;
    };

    apc.changeClient = function () {
        apc.checkForm();
        if (apc.clientSelect !== "-1") {
            apc.firstName = apc.homeClients[apc.clientSelect].firstName;
            apc.lastName = apc.homeClients[apc.clientSelect].lastName;
            apc.email = apc.homeClients[apc.clientSelect].email;
            apc.language = apc.homeClients[apc.clientSelect].language;
        } else {
            apc.firstName = '';
            apc.lastName = '';
            apc.email = '';
            apc.language = 'en';
        }
    };

    apc.checkForm = function () {
        if (apc.firstName !== '' && apc.lastName !== '' && apc.email !== '') {
            apc.validForm = true;
        }
    };

    apc.createSession = function (clientId) {
        apc.clientLoading = true;
        var date = moment(apc.datePicker.date).format('yyyy-MM-DD');
        var time = moment(apc.hour + ':' + apc.minutes + ':00 ' + apc.ampm, "h:mm:ss A").format('HH:mm:ss');
        var sessionDate = date + ' ' + time;
        $http.post(
            appConfig.apiUrl + '/home-response',
            {
                "clientId": clientId,
                "sessionDate": sessionDate,
                "staffId": $rootScope.userId
            }
        )
        .then(function () {
            apc.initForm();
            apc.success = 'Your request was sent';
            $timeout(function () {
                apc.success = '';
            }, 5000);
        },
        function () {
            apc.error = 'There was an error sending the request';
            $timeout(function () {
                apc.error = '';
            }, 5000);
        });
    };

    apc.datePicker = {
        altInputFormats: ['M!/d!/yyyy'],
        clear: function () {
            this.date = null;
        },
        date: null,
        dateOptions: {
            dateDisabled: this.disabled,
            formatYear: 'yy',
            maxDate: new Date(2020, 5, 22),
            minDate: new Date(),
            showWeeks: false,
            startingDay: 0
        },
        disabled: function (data) {
            var date = data.date,
                mode = data.mode;
            return mode === 'day' && (date.getDay() === 0 || date.getDay() === 6);
        },
        format: 'M/d/yyyy',
        getDayClass: function (data) {
            var date = data.date,
                mode = data.mode;
            if (mode === 'day') {
                var dayToCheck = new Date(date).setHours(0, 0, 0, 0);
                for (var i = 0; i < this.events.length; i++) {
                    var currentDay = new Date(this.events[i].date).setHours(0, 0, 0, 0);

                    if (dayToCheck === currentDay) {
                        return this.events[i].status;
                    }
                }
            }

            return '';
        },
        inlineOptions: {
            customClass: this.getDayClass,
            minDate: new Date(),
            showWeeks: false
        },
        open1: function () {
            this.popup1.opened = true;
        },
        open2: function () {
            this.popup2.opened = true;
        },
        popup1: {
            opened: false
        },
        popup2: {
            opened: false
        },
        setDate: function (year, month, day) {
            this.date = new Date(year, month, day);
        },
        today: function () {
            this.date = new Date();
        },
        toggleMin: function () {
            this.inlineOptions.minDate = this.inlineOptions.minDate ? null : new Date();
            this.dateOptions.minDate = this.inlineOptions.minDate;
        }
    };

    apc.getClients = function () {
        apc.clientLoading = true;
        $http.get(appConfig.apiUrl + '/home-clients-by-staff/' + $rootScope.userId)
        .then(function (response) {
            apc.clientLoading = false;
            apc.homeClients = response.data;
        },
        function (e) {
            if (e.status = 404) {
                apc.error = 'No existing clients were found.';
            } else {
                apc.error = 'error: ' + e.data.error;
            }
            // 
        });
    };

    apc.getResponses = function () {
        apc.sessionLoading = true;
        $http.get(appConfig.apiUrl + '/home-responses-by-staff/' + $rootScope.userId)
        .then(function (response) {
            apc.sessionLoading = false;
            apc.homeResponses = response.data;
        },
        function (e) {
            if (e.status = 404) {
                apc.error = 'No existing clients were found.';
            } else {
                apc.error = 'error: ' + e.data.error;
            }
        });
    };

    apc.initForm = function () {
        apc.validForm = false;
        apc.clientSelect = '-1';
        apc.firstName = '';
        apc.lastName = '';
        apc.email = '';
        apc.hour = moment().format('h');
        var minutes = (Math.round(moment().format('mm') / 15) * 15) % 60;
        if (minutes < 15) {
            apc.minutes = '00';
        } else {
            apc.minutes = minutes.toString();
        }
        apc.ampm = moment().format('A');
        apc.getClients();
        apc.getResponses();
        apc.language = 'en';
        apc.success = '';
        apc.error = '';
        apc.clientLoading = false;
        apc.sessionLoading = false;
    };

    apc.saveEdit = function (index) {
        apc.sessionLoading = true;
        var updateObj = {};
        if (apc.editAffirm !== '') {
            updateObj.affirm = apc.editAffirm;
        }
        if (apc.homeResponses[index].notes !== apc.editNotes) {
            updateObj.notes = apc.editNotes;
        }
        $http.put(
            appConfig.apiUrl + '/home-response/' + apc.homeResponses[index].link, updateObj)
        .then(function () {
            apc.homeResponses[index].edit = false;
            apc.editNotes = '';
            apc.editAffirm = '';
            apc.getResponses();
        });
    };

    apc.startEdit = function (index) {
        for (var i = 0; i < apc.homeResponses.length; i++) {
            apc.homeResponses[i].edit = false;
        }
        apc.editNotes = apc.homeResponses[index].notes;
        apc.homeResponses[index].edit = true;
        if (apc.homeResponses[index].responseDate) {
            apc.editAffirm = apc.homeResponses[index].affirm;
        } else {
            apc.editAffirm = '';
        }
    };

    apc.datePicker.today();
    apc.datePicker.toggleMin();
    apc.initForm();
};

var assessmentController = function ($http, $routeParams) {
    var ac = this;
    ac.dateFormat = 'LLLL';
    ac.error = '';
    ac.message = '';
    ac.noAffirm = true;
    ac.loading = false;

    $http.get(appConfig.apiUrl + '/home-response/' + $routeParams.id)
        .then(function (response) {
            if (response.data.responseDate) {
                if (response.data.client.language === 'es') {
                    ac.message = "Ya ha presentado una respuesta para esta solicitud. Muchas gracias.";
                } else {
                    ac.message = "You have already submitted a response for this request. Thank you.";
                }
            }
            ac.displayDate = moment(response.data.sessionDate);
            if (response.data.client.language === 'es') {
                ac.displayDate.locale('es-us');
            }
            ac.homeResponse = response.data;
        },
        function () {
            ac.error = 'There was an error fetching your questionnaire.';
        }
    );

    ac.changeLanguage = function () {
        ac.displayDate.locale(ac.homeResponse.client.language + '-us');
    };

    ac.doSubmit = function () {
        ac.loading = true;
        $http.put(
            appConfig.apiUrl + '/home-response/' + $routeParams.id,
            {"affirm": ac.affirm}
        )
        .then(function () {
            ac.loading = false;
            if (ac.homeResponse.client.language === 'es') {
                ac.message = 'Muchas gracias.  Se ha enviado su respuesta a ' + ac.homeResponse.staff.firstName + ' ' + ac.homeResponse.staff.lastName + '.';
            } else {
                ac.message = 'Thank you.  Your response has been sent to ' + ac.homeResponse.staff.firstName + ' ' + ac.homeResponse.staff.lastName + '.';
            }
        },
        function () {
            if (ac.homeResponse.client.language === 'es') {
                ac.error = 'Ocurrió un error al enviar su respuesta.  Comuníquese con ' + ac.homeResponse.staff.firstName + ' ' + ac.homeResponse.staff.lastName + ' a través de un correo electrónico a ' + ac.homeResponse.staff.email + ' de inmediato.';
            } else {
                ac.error = 'There was an error submitting your response.  Please contact ' + ac.homeResponse.staff.firstName + ' ' + ac.homeResponse.staff.lastName + ' at ' + ac.homeResponse.staff.email + ' right away.';
            }
        });
    };

    ac.submit = function () {
        if (ac.affirm) {
            ac.doSubmit();
        }
    };
};

var changePasswordController = function ($rootScope, loginService, $location) {
    var cpc = this;

    cpc.confirmPassword = '';
    cpc.currentPassword = '';
    cpc.message = '';
    cpc.newPassword = '';

    cpc.changePassword = function () {
        if (cpc.confirmPassword === cpc.newPassword) {
            if (cpc.isPasswordValid(cpc.newPassword)) {
                loginService.changePassword(cpc.currentPassword, cpc.newPassword)
                .then(function () {
                    $location.path('/');
                },
                function () {
                    cpc.message = 'Sorry, the current password does not match our records.  Please try again.';
                });
            } else {
                cpc.message = 'Sorry, your new password does not meet the above requirements.  Please try again.';
            }
        } else {
            cpc.message = 'Sorry, your new passwords do not match.  Please try again.';
        }
    };

    cpc.isPasswordValid = function () {
        if (cpc.passwordPattern.charLength() && cpc.passwordPattern.lowercase() && cpc.passwordPattern.uppercase() && cpc.passwordPattern.special()) {
            return true;
        }

        return false;
    };

    cpc.passwordPattern = {
        charLength: function () {
            if (cpc.newPassword.length >= 8) {
                return true;
            }

            return false;
        },
        lowercase: function () {
            var regex = /^(?=.*[a-z]).+$/;

            if (regex.test(cpc.newPassword)) {
                return true;
            }

            return false;
        },
        special: function () {
            var regex = /^(?=.*[0-9_\W]).+$/;

            if (regex.test(cpc.newPassword)) {
                return true;
            }

            return false;
        },
        uppercase: function () {
            var regex = /^(?=.*[A-Z]).+$/;

            if (regex.test(cpc.newPassword)) {
                return true;
            }

            return false;
        }
    };
};

var entriesController = function ($http, $timeout) {
    var ec = this;
    ec.dateFormat = 'M/D/YYYY h:mm A';
    ec.success = '';
    ec.error = '';

    ec.checkEntry = function () {
        $http({
            data: {
                "color": ec.color,
                "number": ec.number
            },
            headers: {
                'Content-Type': 'application/json'
            },
            method: 'POST',
            url: appConfig.apiUrl + '/denial-by-client'
        })
        .then(function (response) {
            ec.message = response.data.date;
        });
    };

    ec.initForm = function () {
        ec.color = '';
        ec.number = '';
        ec.message = '';
        ec.status = 1;
        ec.language = 'en';
        ec.loading = false;
    };

    ec.submitStatus = function () {
        ec.loading = true;
        $http({
            data: {
                "color": ec.color,
                "number": ec.number,
                "status": ec.status
            },
            headers: {
                'Content-Type': 'application/json'
            },
            method: 'POST',
            url: appConfig.apiUrl + '/entry'
        })
        .then(function () {
            ec.initForm();
            ec.success = 'Response Recorded';
            $timeout(function () {
                ec.success = '';
            }, 5000);
        },
        function (e) {
            ec.loading = false;
            ec.error = 'Error: ' + e.data.error;
            $timeout(function () {
                ec.error = '';
            }, 5000);
        });
    };

    ec.initForm();
};

var hangTagsController = function ($http, $timeout) {
    var htc = this;
    htc.error = '';
    htc.success = '';

    htc.createHangTag = function () {
        htc.loading = true;
        $http.post(
            appConfig.apiUrl + '/entry-client',
            {
                "color": htc.color,
                "firstName": htc.firstName,
                "lastName": htc.lastName,
                "number": htc.number
            }
        )
        .then(function () {
            htc.initForm();
            htc.success = 'Hangtag added successfully';
            $timeout(function () {
                htc.success = '';
            }, 5000);
        },
        function (e) {
            htc.error = 'error:' + e.data.error;
            $timeout(function () {
                htc.error = '';
            }, 5000);
        });
    };

    htc.initForm = function () {
        htc.color = '';
        htc.firstName = '';
        htc.lastName = '';
        htc.number = '';
        htc.loading = false;
    };

    htc.initForm();
};

var loginController = function (loginService, $rootScope, $location) {
    var lc = this;
    if ($rootScope.isLoggedIn) {
        $location.path('/main');
    }

    lc.inputEmail = '';
    lc.inputPassword = '';

    lc.login = function login () {
        loginService.loginUser(lc.inputEmail, lc.inputPassword).then(function (data) {
            if (data) {
                $rootScope.isLoggedIn = true;
                $location.path('/main');
            } else {
                alert('login failed');
            }
        })
        .catch(function (error) {
            alert('An error occurred.\n' + error);
        });
    };
};

var mainController = function () {
    var mc = this;
};

var resetPasswordController = function ($rootScope, loginService) {
    var rpc = this;

    rpc.inputEmail = '';

    rpc.resetPassword = function () {
        loginService.resetPassword(rpc.inputEmail)
        .then(function (response) {
            rpc.message = response.message;
        },
        function (error) {
            rpc.message = error.error;
        });
    };
};

var staffAttestController = function ($rootScope, $location, $http) {
    var sac = this;
    sac.todayDate = moment().format('dddd, MMMM Do YYYY');
    sac.error = '';
    sac.message = '';
    sac.noAffirm = true;
    sac.loading = false;

    // if($rootScope.user.currentAttest) {
    //     $location.path('/');
    // }

    sac.doSubmit = function () {
        sac.loading = true;
        $http.post(
            appConfig.apiUrl + '/staff-attest',
            {"affirm": sac.affirm, "userId": $rootScope.user.id}
        )
        .then(function () {
            $rootScope.user.currentAttest = true;
            sac.loading = false;
            if (sac.affirm) {
                $location.path('/');
            } else {
                sac.message = 'Thank you.  Your response has been recorded.  Please contact your supervisor about rescheduling or reassigning any appointments you may have scheduled today.';
            }
        },
        function () {
            sac.error = 'There was an error submitting your response.  Please contact your supervisor to let them know your response right away.';
        });
    };

    sac.submit = function () {
        if (sac.affirm) {
            sac.doSubmit();
        }
    };
};

var topNavController = function ($rootScope, loginService) {
    var tnc = this;

    tnc.logout = function () {
        loginService.logoutUser();
    };
};

var usersController = function ($http, $rootScope, $location, $timeout, loginService) {
    if($rootScope.isAdmin === false) {
        $location.path('/');
    }

    var uc = this;
    uc.dateFormat = 'M/D/YYYY h:mm A';
    
    uc.addUser = function () {
        uc.loading = true;
        $http.post(
            appConfig.apiUrl + '/user',
            {
                "email": uc.email,
                "firstName": uc.firstName,
                "lastName": uc.lastName,
                "programId": uc.program
            }
        )
        .then(function (response) {
            uc.initForm();
            uc.success = 'User created successfully';
            $timeout(function () {
                uc.success = '';
            }, 5000);
        },
        function (e) {
            uc.error = 'error: ' + e.error;
        });
    };

    uc.cancelEdit = function (index) {
        uc.hideTooltip();
        uc.users[index].edit = false
    };

    uc.changeUserLock = function (index) {
        uc.hideTooltip();
        var updateObj = {};
        if (uc.users[index].enabled) {
            updateObj.enabled = 0;
            var lockAction = 'disabled';
        } else {
            updateObj.enabled = 1;
            var lockAction = 'enabled';
        }
        uc.loading = true;
        $http.put(
            appConfig.apiUrl + '/user/' + uc.users[index].id, updateObj)
        .then(function () {
            uc.loading = false;
            uc.success = 'User ' + uc.users[index].firstName + ' ' + uc.users[index].lastName + ' has been ' + lockAction;
            $timeout(function () {
                uc.success = '';
            }, 5000);
            uc.getUsers();
        });
    }

    uc.checkForm = function () {
        if (uc.firstName === '' || uc.lastName === '' || uc.email === '') {
            uc.validForm = false;
        } else {
            uc.validForm = true;
        }
    };

    uc.getPrograms = function () {
        uc.loading = true;
        $http.get(appConfig.apiUrl + '/program')
        .then(function (response) {
            uc.loading = false;
            uc.programs = response.data;
        },
        function (e) {
            if (e.status = 404) {
                uc.error = 'No programs were found.';
            } else {
                uc.error = 'error: ' + e.data.error;
            }
        });
    };

    uc.getUsers = function () {
        uc.loading = true;
        $http.get(appConfig.apiUrl + '/user')
        .then(function (response) {
            uc.program = $rootScope.programId;
            uc.loading = false;
            uc.users = response.data;
            $(function () {
                $('[data-toggle="tooltip"]').tooltip({
                    trigger: 'hover'
                })
            });
        },
        function (e) {
            if (e.status = 404) {
                apc.error = 'No users were found.';
            } else {
                apc.error = 'error: ' + e.data.error;
            }
        });
    };

    uc.hideTooltip = function () {
        $('[data-toggle="tooltip"]').tooltip('hide');
        $(function () {
            $('[data-toggle="tooltip"]').tooltip({
                trigger: 'hover'
            })
        });
    };

    uc.initForm = function () {
        uc.success = '';
        uc.error = '';
        uc.validForm = false;
        uc.loading = false;
        uc.firstName = '';
        uc.lastName = '';
        uc.email = '';
        uc.getUsers();
    }

    uc.resetPassword = function (index) {
        uc.loading = true;
        loginService.resetPassword(uc.users[index].email)
        .then(function (response) {
            uc.loading = false;
            uc.success = 'Password successfully reset for ' + uc.users[index].firstName + ' ' + uc.users[index].lastName;
            $timeout(function () {
                uc.success = '';
            }, 5000);
        },
        function (error) {
            uc.message = 'error: ' + error.error;
        });
    };

    uc.saveEdit = function (index) {
        uc.loading = true;
        var updateObj = {};
        if (uc.users[index].firstName !== uc.editFirstName) {
            updateObj.firstName = uc.editFirstName;
        }
        if (uc.users[index].email !== uc.editEmail) {
            updateObj.email = uc.editEmail;
        }
        if (uc.users[index].lastName !== uc.editLastName) {
            updateObj.lastName = uc.editLastName;
        }
        if (uc.users[index].userLevel !== uc.editUserLevel) {
            updateObj.userLevel = uc.editUserLevel;
        }
        if (uc.users[index].programId !== uc.editProgram) {
            updateObj.programId = uc.editProgram;
        }
        $http.put(
            appConfig.apiUrl + '/user/' + uc.users[index].id, updateObj)
        .then(function () {
            uc.loading = false;
            uc.users[index].edit = false;
            uc.editFirstName = '';
            uc.editLastName = '';
            uc.editEmail = '';
            uc.editUserLevel = '';
            uc.getUsers();
            uc.success = 'Successfully edited user: ' + uc.users[index].firstName + ' ' + uc.users[index].lastName;
            $timeout(function () {
                uc.success = '';
            }, 5000);
        });
    };

    uc.startEdit = function (index) {
        uc.hideTooltip();
        for (var i = 0; i < uc.users.length; i++) {
            uc.users[i].edit = false;
        }
        uc.editFirstName = uc.users[index].firstName;
        uc.editLastName = uc.users[index].lastName;
        uc.editEmail = uc.users[index].email;
        uc.editUserLevel = uc.users[index].userLevel;
        uc.editProgram = uc.users[index].programId;
        uc.users[index].edit = true;
    };

    uc.toggleHistory = function (index) {
        uc.hideTooltip();
        if(uc.users[index].statusHistory.length > 1) {
            if(uc.users[index].displayHistory) {
                uc.users[index].displayHistory = false;
            } else {
                uc.users[index].displayHistory = true;
            }
        }
    };

    uc.initForm();
    uc.getPrograms();
};

var viewAppointmentsController = function ($rootScope, $http, $routeParams, $location, loginService) {
    var vac = this;
    
    if ($routeParams.id) {
        vac.staffView = true;
        if ($routeParams.id !== $rootScope.userId) {
            if($rootScope.isAdmin === false) {
                $location.path('/');
            }
        }
    } else {
        vac.staffView = false;
        if($rootScope.isAdmin === false) {
            $location.path('/');
        }
    }

    vac.dateFormat = 'M/D/YYYY h:mm A';

    vac.cancelEdit = function (index) {
        vac.editAffirm = '';
        vac.editNotes = '';
        vac.homeResponses[index].edit = false;
    };

    vac.getResponses = function () {
        if (vac.staffView) {
            vac.apiUrl = appConfig.apiUrl + '/home-responses-by-staff/' + $routeParams.id;
        } else {
            vac.apiUrl = appConfig.apiUrl + '/home-response'
        }
        vac.loading = true;
        $http.get(vac.apiUrl)
        .then(function (response) {
            vac.loading = false;
            vac.homeResponses = response.data;
        },
        function (e) {
            if (e.status = 404) {
                vac.error = 'No appointments were found.';
            } else {
                vac.error = 'error: ' + e.data.error;
            }
        });
    };

    vac.getStaff = function () {
        if ($routeParams.id === $rootScope.userId) {
            vac.staff = $rootScope.user;
        } else {
            loginService.getUser($routeParams.id)
            .then(function (user) {
                vac.staff = user;
            });
        }
    }

    vac.initForm = function () {
        if (vac.staffView) {
            vac.getStaff();
        }
        vac.getResponses();
        vac.success = '';
        vac.error = '';
        vac.loading = false;
    };

    vac.saveEdit = function (index) {
        vac.sessionLoading = true;
        var updateObj = {};
        if (vac.editAffirm !== '') {
            updateObj.affirm = vac.editAffirm;
        }
        if (vac.homeResponses[index].notes !== vac.editNotes) {
            updateObj.notes = vac.editNotes;
        }
        $http.put(
            appConfig.apiUrl + '/home-response/' + vac.homeResponses[index].link, updateObj)
        .then(function () {
            vac.homeResponses[index].edit = false;
            vac.editNotes = '';
            vac.editAffirm = '';
            vac.getResponses();
        },
        function (e) {
            vac.error('there was an error saving changes')
        });
    };

    vac.startEdit = function (index) {
        for (var i = 0; i < vac.homeResponses.length; i++) {
            vac.homeResponses[i].edit = false;
        }
        vac.editNotes = vac.homeResponses[index].notes;
        vac.homeResponses[index].edit = true;
        if (vac.homeResponses[index].responseDate) {
            vac.editAffirm = vac.homeResponses[index].affirm;
        } else {
            vac.editAffirm = '';
        }
    };

    vac.initForm();
};

var viewEntriesController = function ($http, $routeParams) {
    var vec = this;

    vec.dateFormat = 'M/D/YYYY h:mm A';

    vec.getClientEntries = function () {
        $http.get(appConfig.apiUrl + '/entry/' + $routeParams.color + '/' + $routeParams.number)
        .then(function (response) {
            vec.entries = response.data;
        },
        function (e) {
            if (e.status = 404) {
                vec.error = 'No entries were found.';
            } else {
                vec.error = 'error: ' + e.data.error;
            }
        });
    };

    vec.getEntries = function () {
        $http.get(appConfig.apiUrl + '/entry')
        .then(function (response) {
            vec.entries = response.data;
        },
        function (e) {
            if (e.status = 404) {
                vec.error = 'No entries were found.';
            } else {
                vec.error = 'error: ' + e.data.error;
            }
        });
    };

    if ($routeParams.color) {
        vec.clientView = true;
        vec.color = $routeParams.color;
        vec.number = $routeParams.number;
        vec.getClientEntries();
    } else {
        vec.clientView = false;
        vec.getEntries();
    }
};

/*************************************************************/
/* Declarations                                              */
/*************************************************************/
app.controller('appointmentsController', appointmentsController);
app.controller('assessmentController', assessmentController);
app.factory('authenticationInterceptor', authenticationInterceptor);
app.controller('changePasswordController', changePasswordController);
app.controller('entriesController', entriesController);
app.controller('hangTagsController', hangTagsController);
app.controller('loginController', loginController);
app.factory('loginService', loginService);
app.controller('mainController', mainController);
app.controller('resetPasswordController', resetPasswordController);
app.controller('staffAttestController', staffAttestController);
app.directive('topNav', topNav);
app.controller('topNavController', topNavController);
app.controller('usersController', usersController);
app.controller('viewAppointmentsController', viewAppointmentsController);
app.controller('viewEntriesController', viewEntriesController);

/*************************************************************/
/* Routes                                                    */
/*************************************************************/
app.config(function ($routeProvider) {
    $routeProvider
        .when('/', {
            controller: loginController,
            controllerAs: 'lc',
            templateUrl: 'components/login/login-template.html'
        })
        .when('/appointments', {
            controller: appointmentsController,
            controllerAs: 'apc',
            templateUrl: 'components/appointments/appointments-template.html'
        })
        .when('/assessment/:id', {
            controller: assessmentController,
            controllerAs: 'ac',
            templateUrl: 'components/assessment/assessment-template.html'
        })
        .when('/change-password', {
            controller: changePasswordController,
            controllerAs: 'cpc',
            templateUrl: 'components/change-password/change-password-template.html'
        })
        .when('/entries', {
            controller: entriesController,
            controllerAs: 'ec',
            templateUrl: 'components/entries/entries-template.html'
        })
        .when('/hang-tags', {
            controller: hangTagsController,
            controllerAs: 'htc',
            templateUrl: 'components/hang-tags/hang-tags-template.html'
        })
        .when('/main', {
            controller: mainController,
            controllerAs: 'mc',
            templateUrl: 'components/main/main-template.html'
        })
        .when('/reset-password', {
            controller: resetPasswordController,
            controllerAs: 'rpc',
            templateUrl: 'components/reset-password/reset-password-template.html'
        })
        .when('/staff-attest', {
            controller: staffAttestController,
            controllerAs: 'sac',
            templateUrl: 'components/staff-attest/staff-attest-template.html'
        })
        .when('/users', {
            controller: usersController,
            controllerAs: 'uc',
            templateUrl: 'components/users/users-template.html'
        })
        .when('/view-appointments', {
            controller: viewAppointmentsController,
            controllerAs: 'vac',
            templateUrl: 'components/view-appointments/view-appointments-template.html'
        })
        .when('/view-appointments/:id', {
            controller: viewAppointmentsController,
            controllerAs: 'vac',
            templateUrl: 'components/view-appointments/view-appointments-template.html'
        })
        .when('/view-entries', {
            controller: viewEntriesController,
            controllerAs: 'vec',
            templateUrl: 'components/view-entries/view-entries-template.html'
        })
        .when('/view-entries/:color/:number', {
            controller: viewEntriesController,
            controllerAs: 'vec',
            templateUrl: 'components/view-entries/view-entries-template.html'
        })
        .otherwise({
            redirectTo: '/'
        });
});

/*************************************************************/
/* Run                                                       */
/*************************************************************/
app.run(function ($rootScope, $location, loginService, $templateCache) {
    $rootScope.$on('$routeChangeStart', function () {
        $('[data-toggle="tooltip"]').tooltip('hide');
        $(function () {
            $('[data-toggle="tooltip"]').tooltip({
                trigger: 'hover'
            })
        });
        $rootScope.isLoggedIn = loginService.isAuthenticated();
        if ($rootScope.isLoggedIn === true) {
            if ((new Date().getTime() / 1000) > loginService.parseToken(loginService.getToken()).exp) {
                loginService.logoutUser();
            }
            $rootScope.userId = loginService.getUserId();
            loginService.getUser($rootScope.userId).then(function (user) {
                $rootScope.user = user;
                $rootScope.programId = user.programId;
                if ($rootScope.user.userLevel === 'admin' || $rootScope.user.userLevel === 'superadmin') {
                    $rootScope.isAdmin = true;
                } else {
                    $rootScope.isAdmin = false;
                }
                if ($rootScope.user.passwordMustChange === '1') {
                    $location.path('/change-password');
                } else {
                    if($rootScope.user.currentAttest === false) {
                        $location.path('/staff-attest');
                    }
                }
            });
        } else {
            if ($location.path().includes('assessment') === false && $location.path().includes('reset-password') === false) {
                $location.path('/');
            }
        }
        if (typeof (current) !== "undefined") {
            $templateCache.remove(current.templateUrl);
        }
    });
});
