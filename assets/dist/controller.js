import { Controller} from "@hotwired/stimulus";
import { getComponent } from '@symfony/ux-live-component';

export default class extends Controller {
    async initialize() {
        this.component = getComponent(this.element);

        // TODO replace this simple click capturing by something useful
        this.element.querySelector('.twig-doc-viewport').querySelectorAll('a, button').forEach((node) => {

                node.addEventListener('click', (e) => {
                    let msg = 'Clicks on links and buttons are disabled for security.';
                    e.preventDefault()
                    if (node.hasAttribute('href')) {
                        msg += '\nLink: ' + e.target.getAttribute('href')
                    }

                    alert(msg);
                })
        })
    }
}
