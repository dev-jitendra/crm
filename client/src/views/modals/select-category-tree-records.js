

import SelectRecordsModalView from 'views/modals/select-records';

class SelectCategoryTreeRecordsModalView extends SelectRecordsModalView {

    setup() {
        
        this.filters = this.options.filters || {};
        
        this.boolFilterList = this.options.boolFilterList || {};
        this.primaryFilterName = this.options.primaryFilterName || null;

        if ('multiple' in this.options) {
            this.multiple = this.options.multiple;
        }

        this.createButton = false;
        this.massRelateEnabled = this.options.massRelateEnabled;

        this.buttonList = [
            {
                name: 'cancel',
                label: 'Cancel'
            }
        ];

        if (this.multiple) {
            this.buttonList.unshift({
                name: 'select',
                style: 'danger',
                label: 'Select',
                onClick: dialog => {
                    const listView = this.getRecordView();

                    if (listView.allResultIsChecked) {
                        this.trigger('select', {
                            massRelate: true,
                            where: this.collection.getWhere(),
                            searchParams: this.collection.data,
                        });
                    }
                    else {
                        const list = listView.getSelected();

                        if (list.length) {
                            this.trigger('select', list);
                        }
                    }

                    dialog.close();
                },
            });
        }

        this.scope = this.options.scope;

        this.$header = $('<span>');

        this.$header.append(
            $('<span>').text(
                this.translate('Select') + ': ' +
                this.getLanguage().translate(this.scope, 'scopeNamesPlural')
            )
        );

        this.$header.prepend(
            this.getHelper().getScopeColorIconHtml(this.scope)
        );

        this.waitForView('list');

        Espo.loader.require('search-manager', SearchManager => {
            this.getCollectionFactory().create(this.scope, collection => {
                collection.maxSize = this.getConfig().get('recordsPerPageSelect') || 5;

                this.collection = collection;

                const searchManager = new SearchManager(collection, 'listSelect', null, this.getDateTime());

                searchManager.emptyOnReset = true;

                if (this.filters) {
                    searchManager.setAdvanced(this.filters);
                }

                if (this.boolFilterList) {
                    searchManager.setBool(this.boolFilterList);
                }

                if (this.primaryFilterName) {
                    searchManager.setPrimary(this.primaryFilterName);
                }

                collection.where = searchManager.getWhere();
                collection.url = collection.entityType + '/action/listTree';

                const viewName =
                    this.getMetadata().get('clientDefs.' + this.scope + '.recordViews.listSelectCategoryTree') ||
                    'views/record/list-tree';

                this.listenToOnce(collection, 'sync', () => {
                    this.createView('list', viewName, {
                        collection: collection,
                        fullSelector: this.containerSelector + ' .list-container',
                        readOnly: true,
                        selectable: true,
                        checkboxes: this.multiple,
                        massActionsDisabled: true,
                        searchManager: searchManager,
                        checkAllResultDisabled: true,
                        buttonsDisabled: true,
                    }, listView => {
                        listView.once('select', model => {
                            this.trigger('select', model);
                            this.close();
                        });
                    });
                });

                collection.fetch();
            });
        });
    }
}


export default SelectCategoryTreeRecordsModalView;
