

import RecordListDashletView from 'views/dashlets/abstract/record-list';

class RecordsDashletView extends RecordListDashletView {

    name = 'Records'

    rowActionsView = 'views/record/row-actions/view-and-edit'
    listView = 'views/email/record/list-expanded'

    init() {
        super.init();

        this.scope = this.getOption('entityType');
    }

    getSearchData() {
        const data = {
            primary: this.getOption('primaryFilter'),
        };

        if (data.primary === 'all') {
            delete data.primary;
        }

        const bool = {};

        (this.getOption('boolFilterList') || []).forEach(item => {
            bool[item] = true;
        });

        data.bool = bool;

        return data;
    }

    setupActionList() {
        const scope = this.getOption('entityType');

        if (scope && this.getAcl().checkScope(scope, 'create')) {
            this.actionList.unshift({
                name: 'create',
                text: this.translate('Create ' + scope, 'labels', scope),
                iconHtml: '<span class="fas fa-plus"></span>',
                url: '#' + scope + '/create',
            });
        }
    }
}

export default RecordsDashletView;
