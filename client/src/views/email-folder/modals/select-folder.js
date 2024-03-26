

define('views/email-folder/modals/select-folder', ['views/modal'], function (Dep) {

    return Dep.extend({

        cssName: 'select-folder',

        template: 'email-folder/modals/select-folder',

        fitHeight: true,

        backdrop: true,

        data: function () {
            return {
                folderDataList: this.folderDataList,
            };
        },

        events: {
            'click a[data-action="selectFolder"]': function (e) {
                let $target = $(e.currentTarget);

                let id = $target.attr('data-id');
                let name = $target.attr('data-name');

                this.trigger('select', id, name);
                this.close();
            },
        },

        setup: function () {
            this.headerText = this.options.headerText || '';

            if (this.headerText === '') {
                this.buttonList.push({
                    name: 'cancel',
                    label: 'Cancel',
                });
            }

            Espo.Ui.notify(' ... ');

            this.wait(
                Espo.Ajax.getRequest('EmailFolder/action/listAll')
                    .then(data => {
                        Espo.Ui.notify(false);

                        this.folderDataList = data.list
                            .filter(item => {
                                return ['inbox', 'important', 'sent', 'drafts', 'trash'].indexOf(item.id) === -1;
                            })
                            .map(item => {
                                return {
                                    id: item.id,
                                    name: item.name,
                                    isGroup: item.id.indexOf('group:') === 0,
                                };
                            });

                        this.folderDataList.unshift({
                            id: 'inbox',
                            name: this.translate('inbox', 'presetFilters', 'Email'),
                        })
                    })
            );
        },
    });
});
