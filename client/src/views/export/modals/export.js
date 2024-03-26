

define('views/export/modals/export', ['views/modal', 'model'], function (Dep, Model) {

    return Dep.extend({

        cssName: 'export-modal',

        className: 'dialog dialog-record',

        template: 'export/modals/export',

        shortcutKeys: {
            'Control+Enter': 'export',
        },

        data: function () {
            return {};
        },

        setup: function () {
            this.buttonList = [
                {
                    name: 'export',
                    label: 'Export',
                    style: 'danger',
                    title: 'Ctrl+Enter',
                },
                {
                    name: 'cancel',
                    label: 'Cancel',
                }
            ];

            this.model = new Model();
            this.model.name = 'Export';

            this.scope = this.options.scope;

            if (this.options.fieldList) {
                const fieldList = this.options.fieldList
                    .filter(field => {
                        return !this.getMetadata()
                            .get(`entityDefs.${this.scope}.fields.${field}.exportDisabled`);
                    });

                this.model.set('fieldList', fieldList);
                this.model.set('exportAllFields', false);
            } else {
                this.model.set('exportAllFields', true);
            }

            let formatList =
                this.getMetadata().get(['scopes', this.scope, 'exportFormatList']) ||
                this.getMetadata().get('app.export.formatList');

            this.model.set('format', formatList[0]);

            this.createView('record', 'views/export/record/record', {
                scope: this.scope,
                model: this.model,
                selector: '.record',
                formatList: formatList,
            });
        },

        getRecordView: function () {
            return this.getView('record');
        },

        actionExport: function () {
            let recordView = this.getRecordView();

            let data = recordView.fetch();

            this.model.set(data);

            if (recordView.validate()) {
                return;
            }

            let returnData = {
                exportAllFields: data.exportAllFields,
                format: data.format,
            };

            if (!data.exportAllFields) {
                let attributeList = [];

                data.fieldList.forEach(item => {
                    if (item === 'id') {
                        attributeList.push('id');

                        return;
                    }

                    let type = this.getMetadata().get(['entityDefs', this.scope, 'fields', item, 'type']);

                    if (type) {
                        this.getFieldManager().getAttributeList(type, item)
                            .forEach(attribute => {
                                attributeList.push(attribute);
                            });
                    }

                    if (~item.indexOf('_')) {
                        attributeList.push(item);
                    }
                });

                returnData.attributeList = attributeList;
                returnData.fieldList = data.fieldList;
            }

            returnData.params = {};

            recordView.getFormatParamList(data.format).forEach(param => {
                let name = recordView.modifyParamName(data.format, param);

                let fieldView = recordView.getFieldView(name);

                if (!fieldView || fieldView.disabled) {
                    return;
                }

                this.getFieldManager()
                    .getActualAttributeList(fieldView.type, param)
                    .forEach(subParam => {
                        let name = recordView.modifyParamName(data.format, subParam);

                        returnData.params[subParam] = data[name];
                    });
            });

            this.trigger('proceed', returnData);
            this.close();
        },
    });
});
