/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import './styles/global.scss';

// start the Stimulus application
import './bootstrap';

console.log("App started");

const eventSource = new EventSource('http://172.31.177.230:3000/.well-known/mercure?topic=' + encodeURIComponent('http://example.com/books/1'));
eventSource.onmessage = event => {

    console.log(JSON.parse(event.data));
}