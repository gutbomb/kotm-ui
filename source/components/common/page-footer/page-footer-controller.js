export default function (pageFooterService, $rootScope) {
    let pfc = this;

    pageFooterService.getFooter()
    .then((menu) => {
        $rootScope.footer = menu;
    });

    pfc.copyrightYear = moment().format('YYYY');
}