export default function getParamUrlSearch(){
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