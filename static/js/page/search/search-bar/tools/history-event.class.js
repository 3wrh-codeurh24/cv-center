import Global from "../../lib/global.class.js";

export default class historyEvent extends Global{
    
    constructor(){
        super();
        let that = this;
        $('head').append('<link rel="stylesheet" href="/static/css/page/search/search-bar/tools/history.css" type="text/css" />');


        let session = this.session_start();


        let params = new URLSearchParams(location.search); 
        let urlSearchParam = params.get('filename');
        

        if (urlSearchParam !== null ) {
            let isSave = (session.history.access.length && urlSearchParam.localeCompare(session.history.access[0].filename) == 0) ? true : false;
            
            if (!isSave) {
                
                session.history.access.unshift({'filename': urlSearchParam, 'timestamp': Date.now(), 'date': this.frenchDateTimeString()})

                if (session.history.access.length > 15) session.history.access.pop();

                console.log('enregistrement de history', session.history.access)
                localStorage.setItem('cv-center', JSON.stringify(session));
                this.refreshHistory(session);
            }
        }

        this.refreshHistory(session);



        $('body').on('click', '.btn-del-item-history',function(){
            let id = $(this).data('index');
            if(session.history.access) {
                session.history.access.splice(id,1);
                localStorage.setItem('cv-center', JSON.stringify(session));
                that.refreshHistory(session)
            }
        })
    }




    refreshHistory(session) {
        $('.nav-link-history-cv ul li').remove();

        if( session.history.access && session.history.access.length == 0) {
            $('.nav-link-history-cv ul').append(`<li>Aucun historique commencé</li>`)
        }

        for(let id in session.history.access) {
            // console.log('list history ', session.history.access[id].filename)
            let searchParam = this.getParamUrlSearch();

            $('.nav-link-history-cv ul')
                .append(`<li><i class="btn-del-item-history material-icons" data-index="${id}">close</i><a href="?${searchParam}filename=${session.history.access[id].filename}">${session.history.access[id].filename}</a></li>`)

        }
    }


}