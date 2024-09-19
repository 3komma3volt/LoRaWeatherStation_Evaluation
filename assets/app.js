import './bootstrap.js';

import './styles/app.scss';
const $ = require('jquery');
global.$ = global.jQuery = $;

import 'chartjs-adapter-date-fns';
require('bootstrap');

// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');



