export default function favoriteEvent(){
    
    $('head').append('<link rel="stylesheet" href="/static/css/page/search/search-bar/tools/favorite.css" type="text/css" />');


    let session = {};
    if (localStorage.getItem('cv-center') === null) {
        console.log('nouveau utilisateur')
        session = {
            'userId': ((new Date()).getMilliseconds() + Math.floor(Math.random() * Math.floor(999999))),
            'favorite': []
        }
        localStorage.setItem('cv-center', JSON.stringify(session));
    } else {
        session = JSON.parse(localStorage.getItem('cv-center'));
        console.log('utilisateur ', session.userId)
        console.log('session ', session)
    }

    refreshFavorite(session);


    $('body').on('click', '.btn-del-item-history',function(){
        let id = $(this).data('index');
        if(session.favorite) {
            session.favorite.splice(id,1);
            localStorage.setItem('cv-center', JSON.stringify(session));
            console.log(session);
            refreshFavorite(session)
            refreshLigthBtnAddFav(session)
        }
    })

    $('body').on('click', '.btn-favories-add-cv',function(e){
        e.preventDefault();
        e.stopPropagation();
        let session = JSON.parse(localStorage.getItem('cv-center'));

        if (session.favorite === undefined) session.favorite = [];

        session.favorite.push({'filename': $(this).data('filename')})
        console.log('session ', session)
        localStorage.setItem('cv-center', JSON.stringify(session));
        refreshFavorite(session)
        return false;
    })






    function refreshLigthBtnAddFav(session){

        let params = new URLSearchParams(location.search); 
        for(let id in session.favorite) {
            if (params.get('filename') !== null && params.get('filename') == session.favorite[id].filename) {

                $('.btn-favories-add-cv i').css('color', "rgb(245, 228, 78)")
            }else{
                $('.btn-favories-add-cv i').css('color', "")
            }
        }
    }



    // Remplis la liste des favoris et active le bouton favori du filename en cours de consultation
    function refreshFavorite(session) {
        $('.nav-link-favories-cv ul li').remove();

        if( session.favorite && session.favorite.length == 0) {
            $('.nav-link-favories-cv ul').append(`<li>Aucun favori enregistré</li>`)
        }

        for(let id in session.favorite) {

            let searchParam = getParamUrlSearch();
    
            let params = new URLSearchParams(location.search); 
            
            if (params.get('filename') !== null && params.get('filename') == session.favorite[id].filename) {
                $('.btn-favories-add-cv i').css('color', "rgb(245, 228, 78)")
            }
    
            $('.nav-link-favories-cv ul')
                .append(`<li><i class="btn-del-item-history material-icons" data-index="${id}">delete</i><a href="?${searchParam}filename=${session.favorite[id].filename}">${session.favorite[id].filename}</a></li>`)

        }
    }



}