
import Popper from "@popperjs/core/dist/umd/popper";
// Replace these with the individual JavaScript components
// you need or, if optimising size is not a priority for you,
// simply replace them all with import "bootstrap";
import "bootstrap";

try {
    window.Popper = Popper;
    window.$ = window.jQuery = require("jquery");
    window.bootstrap = require("bootstrap"); // Modify this line
} catch (e) {}

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

