export default function (donateService) {
    let dc = this;

    dc.getContent = function () {
        donateService.getDonateContent()
        .then(function (data){
            dc.pageContent = data;
            dc.donation.program = dc.pageContent.programs[0].title;
        });
    };

    

    dc.init = function () {
        dc.getContent();
    };

    dc.init();
}