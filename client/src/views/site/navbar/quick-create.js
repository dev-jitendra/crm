

import View from 'view';

class QuickCreateNavbarView extends View {

    templateContent = `
        <a
            id="nav-quick-create-dropdown"
            class="dropdown-toggle"
            data-toggle="dropdown"
            role="button"
            tabindex="0"
            title="{{translate 'Create'}}"
        ><i class="fas fa-plus icon"></i></a>
        <ul class="dropdown-menu" role="menu" aria-labelledby="nav-quick-create-dropdown">
            <li class="dropdown-header">{{translate 'Create'}}</li>
            {{#each list}}
                <li><a
                    href="#{{./this}}/create"
                    data-name="{{./this}}"
                    data-action="quickCreate"
                >{{translate this category='scopeNames'}}</a></li>
            {{/each}}
        </ul>
    `

    data() {
        return {
            list: this.list,
        };
    }

    setup() {
        this.addActionHandler('quickCreate', (e, element) => {
            e.preventDefault();

            this.processCreate(element.dataset.name);
        });

        const scopes = this.getMetadata().get('scopes') || {};

        
        const list = this.getConfig().get('quickCreateList') || [];

        this.list = list.filter(scope => {
            if (!scopes[scope]) {
                return false;
            }

            if ((scopes[scope] || {}).disabled) {
                return;
            }

            if ((scopes[scope] || {}).acl) {
                return this.getAcl().check(scope, 'create');
            }

            return true;
        });
    }

    isAvailable() {
        return this.list.length > 0;
    }

    processCreate(scope) {
        Espo.Ui.notify(' ... ');

        const type = this.getMetadata().get(['clientDefs', scope, 'quickCreateModalType']) || 'edit';
        const viewName = this.getMetadata().get(['clientDefs', scope, 'modalViews', type]) || 'views/modals/edit';

        this.createView('dialog', viewName , {scope: scope})
            .then(view => view.render())
            .then(() => Espo.Ui.notify(false));
    }
}

export default QuickCreateNavbarView;
