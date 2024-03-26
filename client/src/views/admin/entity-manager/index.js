

import View from 'view';
import EntityManagerExportModalView from 'views/admin/entity-manager/modals/export';

class EntityManagerIndexView extends View {

    template = 'admin/entity-manager/index'
    scopeDataList = null
    scope = null

    data() {
        return {
            scopeDataList: this.scopeDataList,
        };
    }

    events = {
        
        'click button[data-action="createEntity"]': function () {
            this.getRouter().navigate('#Admin/entityManager/create&', {trigger: true});
        },
        
        'keyup input[data-name="quick-search"]': function (e) {
            this.processQuickSearch(e.currentTarget.value);
        },
    }

    setupScopeData() {
        this.scopeDataList = [];

        let scopeList = Object.keys(this.getMetadata().get('scopes'))
            .sort((v1, v2) => {
                return v1.localeCompare(v2);
            });

        let scopeListSorted = [];

        scopeList.forEach(scope => {
            var d = this.getMetadata().get('scopes.' + scope);

            if (d.entity && d.customizable) {
                scopeListSorted.push(scope);
            }
        });

        scopeList.forEach(scope => {
            var d = this.getMetadata().get('scopes.' + scope);

            if (d.entity && !d.customizable) {
                scopeListSorted.push(scope);
            }
        });

        scopeList = scopeListSorted;

        scopeList.forEach(scope => {
            let d = this.getMetadata().get('scopes.' + scope);

            let isRemovable = !!d.isCustom;

            if (d.isNotRemovable) {
                isRemovable = false;
            }

            let hasView = d.customizable;

            this.scopeDataList.push({
                name: scope,
                isCustom: d.isCustom,
                isRemovable: isRemovable,
                hasView: hasView,
                type: d.type,
                label: this.getLanguage().translate(scope, 'scopeNames'),
                layouts: d.layouts,
            });
        });
    }

    setup() {
        this.setupScopeData();

        this.addActionHandler('export', () => this.actionExport());
    }

    afterRender() {
        this.$noData = this.$el.find('.no-data');

        this.$el.find('input[data-name="quick-search"]').focus();
    }

    updatePageTitle() {
        this.setPageTitle(this.getLanguage().translate('Entity Manager', 'labels', 'Admin'));
    }

    processQuickSearch(text) {
        text = text.trim();

        let $noData = this.$noData;

        $noData.addClass('hidden');

        if (!text) {
            this.$el.find('table tr.scope-row').removeClass('hidden');

            return;
        }

        let matchedList = [];

        let lowerCaseText = text.toLowerCase();

        this.scopeDataList.forEach(item => {
            let matched = false;

            if (
                item.label.toLowerCase().indexOf(lowerCaseText) === 0 ||
                item.name.toLowerCase().indexOf(lowerCaseText) === 0
            ) {
                matched = true;
            }

            if (!matched) {
                let wordList = item.label.split(' ')
                    .concat(
                        item.label.split(' ')
                    );

                wordList.forEach((word) => {
                    if (word.toLowerCase().indexOf(lowerCaseText) === 0) {
                        matched = true;
                    }
                });
            }

            if (matched) {
                matchedList.push(item.name);
            }
        });

        if (matchedList.length === 0) {
            this.$el.find('table tr.scope-row').addClass('hidden');

            $noData.removeClass('hidden');

            return;
        }

        this.scopeDataList
            .map(item => item.name)
            .forEach(scope => {
                if (!~matchedList.indexOf(scope)) {
                    this.$el.find('table tr.scope-row[data-scope="'+scope+'"]').addClass('hidden');

                    return;
                }

                this.$el.find('table tr.scope-row[data-scope="'+scope+'"]').removeClass('hidden');
            });
    }

    actionExport() {
        const view = new EntityManagerExportModalView();

        this.assignView('dialog', view)
            .then(() => {
                view.render();
            })
    }
}

export default EntityManagerIndexView;
