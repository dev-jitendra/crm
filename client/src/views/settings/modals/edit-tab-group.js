

define('views/settings/modals/edit-tab-group', ['views/modal', 'model'], function (Dep, Model) {

    return Dep.extend({

        className: 'dialog dialog-record',

        templateContent: '<div class="record no-side-margin">{{{record}}}</div>',

        setup: function () {
            Dep.prototype.setup.call(this);

            this.headerText = this.translate('Group Tab', 'labels', 'Settings');

            this.buttonList.push({
                name: 'apply',
                label: 'Apply',
                style: 'danger',
            });

            this.buttonList.push({
                name: 'cancel',
                label: 'Cancel',
            });

            this.shortcutKeys = {
                'Control+Enter': () => this.actionApply(),
            };

            var detailLayout = [
                {
                    rows: [
                        [
                            {
                                name: 'text',
                                labelText: this.translate('label', 'fields', 'Admin'),
                            },
                            {
                                name: 'iconClass',
                                labelText: this.translate('iconClass', 'fields', 'EntityManager'),
                            },
                            {
                                name: 'color',
                                labelText: this.translate('color', 'fields', 'EntityManager'),
                            },
                        ],
                        [
                            {
                                name: 'itemList',
                                labelText: this.translate('tabList', 'fields', 'Settings'),
                            },
                            false
                        ]
                    ]
                }
            ];

            var model = this.model = new Model();

            model.name = 'GroupTab';

            model.set(this.options.itemData);

            model.setDefs({
                fields: {
                    text: {
                        type: 'varchar',
                    },
                    iconClass: {
                        type: 'base',
                        view: 'views/admin/entity-manager/fields/icon-class',
                    },
                    color: {
                        type: 'base',
                        view: 'views/fields/colorpicker',
                    },
                    itemList: {
                        type: 'array',
                        view: 'views/settings/fields/group-tab-list',
                    },
                },
            });

            this.createView('record', 'views/record/edit-for-modal', {
                detailLayout: detailLayout,
                model: model,
                selector: '.record',
            });
        },

        actionApply: function () {
            var recordView = this.getView('record');

            if (recordView.validate()) {
                return;
            }

            var data = recordView.fetch();

            this.trigger('apply', data);
        },

    });
});
