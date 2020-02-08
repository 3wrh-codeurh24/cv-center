export default class Global {
    constructor() {
        this.system = {
            'resetLocalStorage' : false,
            'version' : '1.2.0',
            'developmentStage' : 'prototype'
        }
        
    }

    getParamUrlSearch(){
        // parseur d'url
        let params = new URLSearchParams(location.search); 
        
        // chaine qui complete l'url pour rester dans la recherche actuelle
        // cette chaine ré-écrit la variable get search[] dans l'url
        let searchParam = '';
    
        // variable qui simplifie la symtaxe de params.getAll('search[]'); en urlSearchParam
        // params.getAll est utilisé à la place de params.get pour récuperer une variable get de type tableau
        let urlSearchParam = params.getAll('search[]');
        // si il existe une recherche
        if (params.getAll('search[]') !== null) {
            // pour chaque recherche combiné (le ou)
            for (let idSearch in params.getAll('search[]')){
                searchParam += `search[]=${urlSearchParam[idSearch]}&`
            }
        }
        return searchParam
    }
    
    frenchDateTimeString() {
        let datePart1 = (new Intl.DateTimeFormat('fr-FR', {
            year: 'numeric',
            month: 'numeric',
            day: 'numeric',
            hour12: false,
            formatMatcher: 'basic'
        }).format(Date.now()))
    
        let datePart2 = (new Intl.DateTimeFormat('fr-FR', {
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric',
            hour12: false,
            formatMatcher: 'basic'
        }).format(Date.now()))
        return datePart1+' '+datePart2
    }
    
    session_start(){
        let session = {};
        if (localStorage.getItem('cv-center') === null) {
            console.log('nouveau utilisateur')
            session = {
                'userId': ((new Date()).getMilliseconds() + Math.floor(Math.random() * Math.floor(999999))),
                'version': this.system.version,
                'favorite': [],
                'history': {
                    'access': [],
                    'search': []
                }
            }
            localStorage.setItem('cv-center', JSON.stringify(session));
        } else {
            session = JSON.parse(localStorage.getItem('cv-center'));
            console.log('utilisateur ', session.userId)
            console.log('session ', session)
        }
        return session;
    }
    
    session_start_reset(session){
        if (session.version !== this.system.version && this.system.resetLocalStorage) {
            localStorage.removeItem('cv-center');
            document.location.reload();
        }
    }
}