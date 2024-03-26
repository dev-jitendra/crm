

define('views/admin/field-manager/list', ['view'], function (Dep) {

    return Dep.extend({

        template: 'admin/field-manager/list',

        data: function () {
            return {
                scope: this.scope,
                fieldDefsArray: this.fieldDefsArray,
                typeList: this.typeList,
                hasAddField: this.hasAddField,
            };
        },

        events: {
            'click [data-action="removeField"]': function (e) {
                var field = $(e.currentTarget).data('name');

                this.removeField(field);
            },
            'keyup input[data-name="quick-search"]': function (e) {
                this.processQuickSearch(e.currentTarget.value);
            },
        },

        setup: function () {
            this.scope = this.options.scope;

            this.isCustomizable =
                !!this.getMetadata().get(`scopes.${this.scope}.customizable`) &&
                this.getMetadata().get(`scopes.${this.scope}.entityManager.fields`) !== false;

            this.hasAddField = true;

            let entityManagerData = this.getMetadata().get(['scopes', this.scope, 'entityManager']) || {};

            if ('addField' in entityManagerData) {
                this.hasAddField = entityManagerData.addField;
            }

            this.wait(
                this.buildFieldDefs()
            );
        },

        afterRender: function () {
            this.$noData = this.$el.find('.no-data');

            this.$el.find('input[data-name="quick-search"]').focus();
        },

        buildFieldDefs: function () {
            return this.getModelFactory().create(this.scope).then(model => {
                this.fields = model.defs.fields;

                this.fieldList = Object.keys(this.fields).sort();
                this.fieldDefsArray = [];

                this.fieldList.forEach(field => {
                    let defs = this.fields[field];

                    this.fieldDefsArray.push({
                        name: field,
                        isCustom: defs.isCustom || false,
                        type: defs.type,
                        label: this.translate(field, 'fields', this.scope),
                        isEditable: !defs.customizationDisabled && this.isCustomizable,
                    });
                });
            });
        },

        removeField: function (field) {
            this.confirm(this.translate('confirmation', 'messages'), () => {
                Espo.Ui.notify(' ... ');

                Espo.Ajax.request('Admin/fieldManager/' + this.scope + '/' + field, 'delete').then(() => {
                    Espo.Ui.success(this.translate('Removed'));

                    this.$el.find('tr[data-name="'+field+'"]').remove();
                    var data = this.getMetadata().data;

                    delete data['entityDefs'][this.scope]['fields'][field];

                    this.getMetadata().loadSkipCache().then(() =>
                        this.buildFieldDefs()
                            .then(() => {
                                this.broadcastUpdate();

                                return this.reRender();
                            })
                            .then(() =>
                                Espo.Ui.success(this.translate('Removed'))
                            )
                    );
                });
            });
        },

        broadcastUpdate: function () {
            this.getHelper().broadcastChannel.postMessage('update:metadata');
            this.getHelper().broadcastChannel.postMessage('update:language');
        },

        processQuickSearch: function (text) {
            text = text.trim();

            let $noData = this.$noData;

            $noData.addClass('hidden');

            if (!text) {
                this.$el.find('table tr.field-row').removeClass('hidden');

                return;
            }

            let matchedList = [];

            let lowerCaseText = text.toLowerCase();

            this.fieldDefsArray.forEach(item => {
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
                this.$el.find('table tr.field-row').addClass('hidden');

                $noData.removeClass('hidden');

                return;
            }

            this.fieldDefsArray
                .map(item => item.name)
                .forEach(field => {
                    let $row = this.$el.find(`table tr.field-row[data-name="${field}"]`);

                    if (!~matchedList.indexOf(field)) {
                        $row.addClass('hidden');

                        return;
                    }

                    $row.removeClass('hidden');
                });
        },
    });
});
