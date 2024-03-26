

import RecordListDashletView from 'views/dashlets/abstract/record-list';

class EmailsDashletView extends RecordListDashletView {

    name = 'Emails'
    scope ='Emails'

    rowActionsView = 'views/email/record/row-actions/dashlet'
    listView = 'views/email/record/list-expanded'

    setupActionList() {
        if (this.getAcl().checkScope(this.scope, 'create')) {
            this.actionList.unshift({
                name: 'compose',
                text: this.translate('Compose Email', 'labels', this.scope),
                iconHtml: '<span class="fas fa-plus"></span>',
            });
        }
    }

    
    actionCompose() {
        const attributes = this.getCreateAttributes() || {};

        Espo.Ui.notify(' ... ');

        const viewName = this.getMetadata().get('clientDefs.' + this.scope + '.modalViews.compose') ||
            'views/modals/compose-email';

        this.createView('modal', viewName, {
            scope: this.scope,
            attributes: attributes,
        }, view => {
            view.render();

            Espo.Ui.notify(false);

            this.listenToOnce(view, 'after:save', () => {
                this.actionRefresh();
            });
        });
    }

    
    getSearchData() {
        return {
            'advanced': [
                {
                    'attribute': 'folderId',
                    'type': 'inFolder',
                    'value': this.getOption('folder') || 'inbox',
                }
            ]
        };
    }
}

export default EmailsDashletView;
