

import DetailView from 'views/detail';

class ImportDetailView extends DetailView {

    getHeader() {
        let name = this.getDateTime().toDisplay(this.model.get('createdAt'));

        return this.buildHeaderHtml([
            $('<a>')
                .attr('href', '#' + this.model.entityType + '/list')
                .text(this.getLanguage().translate(this.model.entityType, 'scopeNamesPlural')),
            $('<span>')
                .text(name)
        ]);
    }

    setup() {
        super.setup();

        this.setupMenu();

        this.listenTo(this.model, 'change', () => {
            this.setupMenu();

            if (this.isRendered()) {
                this.getView('header').reRender();
            }
        });

        this.listenTo(this.model, 'sync', (m) => {
            this.controlButtons(m);
        });
    }

    setupMenu() {
        this.addMenuItem('buttons', {
            label: "Remove Import Log",
            action: "removeImportLog",
            name: 'removeImportLog',
            style: "default",
            acl: "delete",
            title: this.translate('removeImportLog', 'messages', 'Import'),
        }, true);

        this.addMenuItem('buttons', {
            label: "Revert Import",
            name: 'revert',
            action: "revert",
            style: "danger",
            acl: "edit",
            title: this.translate('revert', 'messages', 'Import'),
            hidden: !this.model.get('importedCount'),
        }, true);

        this.addMenuItem('buttons', {
            label: "Remove Duplicates",
            name: 'removeDuplicates',
            action: "removeDuplicates",
            style: "default",
            acl: "edit",
            title: this.translate('removeDuplicates', 'messages', 'Import'),
            hidden: !this.model.get('duplicateCount'),
        }, true);

        this.addMenuItem('dropdown', {
            label: 'New import with same params',
            name: 'createWithSameParams',
            action: 'createWithSameParams',
        });
    }

    controlButtons(model) {
        if (!model || model.hasChanged('importedCount')) {
            if (this.model.get('importedCount')) {
                this.showHeaderActionItem('revert');
            } else {
                this.hideHeaderActionItem('revert');
            }
        }

        if (!model || model.hasChanged('duplicateCount')) {
            if (this.model.get('duplicateCount')) {
                this.showHeaderActionItem('removeDuplicates');
            } else {
                this.hideHeaderActionItem('removeDuplicates');
            }
        }
    }

    
    actionRemoveImportLog() {
        this.confirm(this.translate('confirmRemoveImportLog', 'messages', 'Import'), () => {
            this.disableMenuItem('removeImportLog');

            Espo.Ui.notify(this.translate('pleaseWait', 'messages'));

            this.model.destroy({
                wait: true,
            }).then(() => {
                Espo.Ui.notify(false);

                var collection = this.model.collection;

                if (collection) {
                    if (collection.total > 0) {
                        collection.total--;
                    }
                }

                this.getRouter().navigate('#Import/list', {trigger: true});

                this.removeMenuItem('removeImportLog', true);
            });
        });
    }

    
    actionRevert() {
        this.confirm(this.translate('confirmRevert', 'messages', 'Import'), () => {
            this.disableMenuItem('revert');

            Espo.Ui.notify(this.translate('pleaseWait', 'messages'));

            Espo.Ajax
                .postRequest(`Import/${this.model.id}/revert`)
                .then(() => {
                    this.getRouter().navigate('#Import/list', {trigger: true});
                });
        });
    }

    
    actionRemoveDuplicates() {
        this.confirm(this.translate('confirmRemoveDuplicates', 'messages', 'Import'), () => {
            this.disableMenuItem('removeDuplicates');

            Espo.Ui.notify(this.translate('pleaseWait', 'messages'));

            Espo.Ajax
                .postRequest(`Import/${this.model.id}/removeDuplicates`)
                .then(() => {
                    this.removeMenuItem('removeDuplicates', true);

                    this.model.fetch();
                    this.model.trigger('update-all');

                    Espo.Ui.success(this.translate('duplicatesRemoved', 'messages', 'Import'));
                });
            });
    }

    
    actionCreateWithSameParams() {
        let formData = this.model.get('params') || {};

        formData.entityType = this.model.get('entityType');
        formData.attributeList = this.model.get('attributeList') || [];

        formData = Espo.Utils.cloneDeep(formData);

        this.getRouter().navigate('#Import', {trigger: false});

        this.getRouter().dispatch('Import', 'index', {
            formData: formData,
        });
    }
}

export default ImportDetailView;
