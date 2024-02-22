import { Controller} from "@hotwired/stimulus";
import { getComponent } from '@symfony/ux-live-component';

export default class extends Controller {
    async initialize() {
        this.component = await getComponent(this.element);

        this.initBreakpoints();
        this.initResize()
    }
    initResize() {
        const observer = new ResizeObserver((entries) => {
            for (const entry of entries) {
                if (entry.borderBoxSize) {
                    const size = entry.borderBoxSize[0].inlineSize;

                    this.updateBreakpointSize(size);
                } else {
                    this.updateBreakpointSize(entry.contentRect.width);
                }
            }
        })

        observer.observe(this.element.querySelector('.twig-doc-viewport'));
    }
    initBreakpoints() {
        this.element.querySelectorAll('.twig-doc-breakpoint-btn').forEach((node) => {
            node.addEventListener('click', (e) => {
                const size = +e.target.dataset.width ;
                this.element.querySelector('.twig-doc-viewport').style.width = size + 'px';
                this.updateBreakpointSize(size);
            })
        });
    }
    updateBreakpointSize(size) {
        this.element.querySelector('.twig-doc-preview-breakpoint-size').innerText = size + 'px';
    }
}
