import getParamUrlSearch  from "./lib/global.js";
window.getParamUrlSearch = getParamUrlSearch;

import favoriteEvent from "./search-bar/tools/favorite-event.js";
import formEvent from "./search-bar/form/form-event.js";

favoriteEvent();
formEvent();