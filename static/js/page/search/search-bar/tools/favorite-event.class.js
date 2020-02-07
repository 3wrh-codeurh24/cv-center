import Global from "../../lib/global.class.js";

export default class favoriteEvent extends Global {
    
    constructor(){
        super()
        let that = this;
        
        $('head').append('<link rel="stylesheet" href="/static/css/page/search/search-bar/tools/favorite.css" type="text/css" />');

        let session = this.session_start();
    
        this.refreshFavorite(session);
    
    
        $('body').on('click', '.btn-del-item-favorite',function(){
            let id = $(this).data('index');
            if(session.favorite) {
                session.favorite.splice(id,1);
                localStorage.setItem('cv-center', JSON.stringify(session));
                console.log(session);
                that.refreshFavorite(session)
                that.refreshLigthBtnAddFav(session)
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
            that.refreshFavorite(session)
            return false;
        })
    }






    refreshLigthBtnAddFav(session){

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
    refreshFavorite(session) {
        $('.nav-link-favories-cv ul li').remove();

        if( session.favorite && session.favorite.length == 0) {
            $('.nav-link-favories-cv ul').append(`<li>Aucun favori enregistré</li>`)
        }

        for(let id in session.favorite) {

            let searchParam = this.getParamUrlSearch();
    
            let params = new URLSearchParams(location.search); 
            
            if (params.get('filename') !== null && params.get('filename') == session.favorite[id].filename) {
                $('.btn-favories-add-cv i').css('color', "rgb(245, 228, 78)")
            }
    
            $('.nav-link-favories-cv ul')
                .append(`<li><i class="btn-del-item-favorite material-icons" data-index="${id}">close</i><a href="?${searchParam}filename=${session.favorite[id].filename}">${session.favorite[id].filename}</a></li>`)

        }
    }



}