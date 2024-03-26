

import BaseDashletView from 'views/dashlets/abstract/base';

class MemoDashletView extends BaseDashletView {

    name = 'Memo'

    templateContent = `
        {{#if text}}
        <div class="complex-text complex-text-memo">{{complexText text}}</div>
        {{/if}}
    `

    data() {
        return {
            text: this.getOption('text'),
        };
    }

    afterAdding() {
        this.getContainerView().actionOptions();
    }
}

export default MemoDashletView;
