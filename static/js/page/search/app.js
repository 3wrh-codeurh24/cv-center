import Global from './lib/global.class.js';
import favoriteEvent from "./search-bar/tools/favorite-event.class.js";
import historyEvent from "./search-bar/tools/history-event.class.js";
import historySearchEvent from "./search-bar/tools/history-search-event.class.js";
import formEvent from "./search-bar/form/form-event.class.js";

class Main extends Global {

    constructor() {
        super() 
        
        // remise à zéro de la sessions localStorage en cas de changement
        // important entre 2 versions du logiciel
        let session = this.session_start();
        this.session_start_reset(session);

    //*** BARRE DE RECHERCHE ***/

        // Outils
        new favoriteEvent();
        new historyEvent();
        new historySearchEvent();
        // Formulaire
        new formEvent();
    }
}

new Main();