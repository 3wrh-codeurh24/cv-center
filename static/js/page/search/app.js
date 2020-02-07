import getParamUrlSearch, {
    frenchDateTimeString,
    session_start,
    session_start_reset
}  from "./lib/global.js";
window.getParamUrlSearch = getParamUrlSearch;
window.frenchDateTimeString = frenchDateTimeString;
window.session_start = session_start;

window.system = {
    'resetLocalStorage' : false,
    'version' : '1.2.0',
    'developmentStage' : 'prototype'
}

import favoriteEvent from "./search-bar/tools/favorite-event.js";
import historyEvent from "./search-bar/tools/history-event.js";
import historySearchEvent from "./search-bar/tools/history-search-event.js";
import formEvent from "./search-bar/form/form-event.js";


let session = session_start();
session_start_reset(session);


favoriteEvent();
historyEvent();
historySearchEvent();
formEvent();