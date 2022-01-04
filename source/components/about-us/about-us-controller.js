export default function ($timeout, programService, aboutUsService) {
    let auc = this;

    auc.getPrograms = function () {
        programService.getPrograms()
        .then(function (data){
            auc.programs = data;
        });
    };
    
    auc.getPrograms(); 

    auc.getHistory = function () {
        aboutUsService.getHistory()
        .then(function (data){
            auc.history = data;
        });
    };
    
    auc.getHistory(); 

    // $timeout(() => {
    //     const faders = document.querySelectorAll('.fade-in');
    //     const appearOptions = {
    //         threshhold: 1
    //     };

    //     const appearOnScroll = new IntersectionObserver (function(entries, appearOnScroll) {
    //         entries.forEach(entry => {
    //             if (!entry.isIntersecting) {
    //                 return;
    //             } else {
    //                 entry.target.classList.add('appear');
    //                 appearOnScroll.unobserve(entry.target);
    //             }
    //         })
    //     }, appearOptions);

    //     faders.forEach(fader => {
    //         appearOnScroll.observe(fader);
    //     });
    // }, 500);

    
}