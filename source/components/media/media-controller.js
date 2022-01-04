export default function (mediaService, $window) {
    let mc = this;
    
    mc.init = function () {
        mc.searchTerm = '';
        mc.getMedia();
    };

    mc.getMedia = function () {
        mediaService.getMedia()
        .then(function (data){
            mc.media = data;
        });
    };

    mc.openDocument = function (file) {
        $window.open(`/media/${file}`, '_blank');
    }

    mc.search = function () {
        if (mc.searchTerm !== '') {
            for (let i=0; i<mc.media.length; i++) {
                mc.media[i].archiveOpen = true;
            }
        } else {
            mc.closeSearch();
        }
    };

    mc.closeSearch = function () {
        mc.searchTerm = '';
        for (let i=0; i<mc.media.length; i++) {
            mc.media[i].archiveOpen = false;
        }
    }

    mc.isYearSame = function (date, nextDate) {
        if (moment(date).format('YYYY') === moment(nextDate).format('YYYY')) {
            return true;
        } else {
            return false;
        }
    }

    mc.init();
};