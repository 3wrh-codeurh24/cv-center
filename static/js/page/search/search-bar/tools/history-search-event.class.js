import Global from "../../lib/global.class.js";

export default class historySearchEvent extends Global{
    
    constructor() {
        super();
        let that = this;

        $('head').append('<link rel="stylesheet" href="/static/css/page/search/search-bar/tools/history-search.css" type="text/css" />');
        let session = this.session_start();
        this.refreshSearchHistory(session)
    
        let params = new URLSearchParams(location.search); 
        let urlSearchParam = params.getAll('search[]');
        
    
        // console.log('urlSearchParam: ', urlSearchParam)
        if (urlSearchParam !== null && urlSearchParam.length != 0) {
            let isSave= true;
            
            if (session.history.search.length && session.history.search[0].array !== undefined )
                isSave = (JSON.stringify(urlSearchParam).localeCompare(session.history.search[0].array) == 0) ? true : false;
            else
                isSave= false;
    
            // console.log("issave: ", isSave, session.history.search[0].array, JSON.stringify(urlSearchParam),' == ',session.history.search[0].array)
            if (!isSave) {
                let searchParam = this.getParamUrlSearch();
                console.log('searchParam: ',searchParam);
                let label = searchParam.replace(/search\[\]\=/gi, '')
                label = label.replace(/&/gi, ' ')
    
                
                session.history.search.unshift({'querySearch': searchParam, 'array': JSON.stringify(urlSearchParam), 'label': label, 'timestamp': Date.now(), 'date': this.frenchDateTimeString()})
    
                if (session.history.search.length > 25) session.history.search.pop();
                
                localStorage.setItem('cv-center', JSON.stringify(session));
                this.refreshSearchHistory(session)
            }
        }
    
    
        $('body').on('click', '.btn-del-item-history-search',function(){
            let id = $(this).data('index');
            if(session.history.search) {
                session.history.search.splice(id,1);
                localStorage.setItem('cv-center', JSON.stringify(session));
                that.refreshSearchHistory(session)
            }
        })
    }


    refreshSearchHistory(session) {
        $('.nav-link-history-search ul li').remove();

        if( session.history.search && session.history.search.length == 0) {
            $('.nav-link-history-search ul').append(`<li>Aucun historique de recherche commencé</li>`)
        }

        for(let id in session.history.search) {

            let querySearch = session.history.search[id].querySearch;
            $('.nav-link-history-search ul')
                .append(`<li><i class="btn-del-item-history-search material-icons" data-index="${id}">close</i><a href="?${querySearch}">${session.history.search[id].label}</a></li>`)

        }
    }

}