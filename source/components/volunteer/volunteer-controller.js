export default function ($location) {
    let vc = this;

    vc.init = function () {
        if($location.search().vol_sys_page) {
            vc.iframe = $location.search().vol_sys_page;
        } else {
            vc.iframe = '/volunteer-system/app/index.php';
        }
    }
    vc.init();
}