import { Controller} from "@hotwired/stimulus";
import { getComponent } from '@symfony/ux-live-component';

export default class extends Controller {
    async initialize() {
        this.component = getComponent(this.element);
        console.log('twig-doc-bundle controller');
    }
}
