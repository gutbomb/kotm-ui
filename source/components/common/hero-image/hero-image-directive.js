export default function() {
    return {
        templateUrl : 'components/common/hero-image/hero-image-template.html',
        controller: 'heroImageController',
        controllerAs: 'hic',
        scope: {
            heroId: '='
        }
    };
}