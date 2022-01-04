import { search } from "angular-animate";

export default function ($scope, $routeParams, $window, programService, $rootScope, $timeout, $location, pageService) {
    let pc = this;

    $window.scrollTo(0, 0);

    pc.getProgram = function () {
        programService.getProgram($routeParams.program)
        .then(function (data){
            pc.program = data;
            pc.ready = true;
            pc.getLocations();
            pc.getFaqs();
        }, function(e) {
            if (e.status === 404) {
                $location.path(`/error/program/${$routeParams.program}`);
            };
        })
        .then(function () {
            if($routeParams.tabSlug) {
               pc.activeTab = pc.getTabIndex($routeParams.tabSlug);
               pc.getTabMeta();
            } else {
                pc.activeTab = 0;
                $rootScope.metaTab = false;
            }
            pc.program.tabs[pc.activeTab].activeTab = true;
        });
    };

    pc.getLocations = function () {
        programService.getLocations(pc.program.id)
        .then(function (data){
            pc.program.locations = data;
        }, function () {
            pc.program.locations = false;
        });
    };

    pc.getTabMeta = function () {
        pageService.getPage(`/programs/${$routeParams.program}`).then( function(pageMeta) {
            $rootScope.metaTab = pageMeta;
            $rootScope.metaTab.title = `${pc.program.name} - ${pc.program.tabs[pc.activeTab].title}`;
        });
    };

    pc.getFaqs = function () {
        programService.getFaqs(pc.program.id)
        .then(function (data){
            pc.program.questions = data;
        }, function () {
            pc.program.questions = false;
        });
    }

    pc.getTabIndex = function (slug) {
        for (let i=0; i < (pc.program.tabs.length); i++) {
            if(pc.program.tabs[i].slug === slug) {
                return i;
            }
        }
    }

    pc.init = function () {
        $rootScope.metaTab = false;
        pc.getProgram();
    };

    pc.changeTab = (tab) => {
        if(tab === 0) {
            $location.update_path(`/programs/${$routeParams.program}`);
            $rootScope.metaTab = false;
        } else {
            $location.update_path(`/programs/${$routeParams.program}/${pc.program.tabs[tab].slug}`)
            pc.getTabMeta();
        }
        pc.program.tabs[pc.activeTab].activeTab = false;
        pc.activeTab = tab;
        pc.program.tabs[pc.activeTab].activeTab = true;        
        return true;
    };

    pc.init();
}