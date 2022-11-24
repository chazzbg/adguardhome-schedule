/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';
require('bootstrap/js/src/dropdown')
import './js/bootstrap';


import {useConfigStore} from "./js/store/config";
import {each} from "lodash";
import {capitalize} from "lodash/string";

let config = useConfigStore();

each(document.querySelectorAll('meta[name^="config"]'), (meta,k) => {
    let [store , prop] = meta.name.split(':')
    if(store === 'config'){
        config[`set${capitalize(prop)}`](meta.content);
    }
})