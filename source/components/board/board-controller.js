export default function ($location, boardService) {
    let bc = this;

    bc.getBoard = function () {
        boardService.getBoard($location.path().replace('/', ''))
        .then(function (data){
            bc.board = data;
            bc.ready = true;
        })
    };

    bc.init = function () {
        bc.getBoard();
    }

    bc.init();
};