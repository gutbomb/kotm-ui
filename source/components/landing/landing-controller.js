export default function ($location, landingService) {
    let lc = this;

    lc.getLanding = function () {
        landingService.getLanding($location.path().replace('/', ''))
        .then(function (data){
            lc.landing = data;
            lc.getFaqs();
        });
    };

    lc.getFaqs = function () {
        landingService.getFaqs(lc.landing.id)
        .then(function (data){
            lc.landing.questions = data;
        });
    };

    lc.init = function () {
        lc.getLanding();
    };

    lc.init();
};