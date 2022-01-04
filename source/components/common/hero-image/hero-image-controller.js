export default function ($interval, $timeout, $scope, heroImageService) {
    let hic = this;

    hic.mouseOnHero = false;
    hic.overlayClass = 'no-overlay';
    hic.activeHero = 0;
    hic.hero = [];
    
    hic.start = function () {
        hic.stop();

        hic.interval = $interval(() => {
            if(!hic.mouseOnHero) {
                hic.changeHero('next')
            }
        }, 10000);
    };

    hic.stop = function () {
        $interval.cancel(hic.interval);
    };

    $scope.$watch('heroId', function(newVal, oldVal) {
        hic.stop();
        hic.getHero(newVal);
    });

    hic.changeHero = (direction) => {
        hic.hero.items[hic.activeHero].active = false;
        if (direction === 'next') {
            if (hic.activeHero === (hic.hero.items.length - 1)) {
                hic.activeHero = 0;
            } else {
                hic.activeHero++;
            }
        } else if (direction === 'previous') {
            if (hic.activeHero === 0) {
                hic.activeHero = hic.hero.items.length - 1;
            } else {
                hic.activeHero--;
            }
        } else {
            hic.activeHero = direction;
        }
        hic.hero.items[hic.activeHero].active = true;
        hic.overlayClass = hic.hero.items[hic.activeHero].position;
        
        return true;
    }

    hic.getHero = function (heroId = $scope.heroId) {
        heroImageService.getHero(heroId)
        .then(function (data){
            hic.hero = data;
        })
        .finally(function () {
            hic.hero.items[0].active = true;
            hic.overlayClass = hic.hero.items[0].position;
            if(hic.hero.items.length > 1) {
                hic.start();
            } else {
                hic.stop();
            }
        });
    };
    
    hic.getHero();

    $scope.$on('$destroy', function() {
        hic.stop();
    });
}