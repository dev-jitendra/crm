

define('views/admin/integrations/edit', ['view', 'model'], function (Dep, Model) {

    return Dep.extend({

        template: 'admin/integrations/edit',

        data: function () {
            return {
                integration: this.integration,
                dataFieldList: this.dataFieldList,
                helpText: this.helpText
            };
        },

        events: {
            'click button[data-action="cancel"]': function () {
                this.getRouter().navigate('#Admin/integrations', {trigger: true});
            },
            'click button[data-action="save"]': function () {
                this.save();
            },
        },

        setup: function () {
            this.integration = this.options.integration;

            this.helpText = false;

            if (this.getLanguage().has(this.integration, 'help', 'Integration')) {
                this.helpText = this.translate(this.integration, 'help', 'Integration');
            }

            this.fieldList = [];

            this.dataFieldList = [];

            this.model = new Model();
            this.model.id = this.integration;
            this.model.name = 'Integration';
            this.model.urlRoot = 'Integration';

            this.model.defs = {
                fields: {
                    enabled: {
                        required: true,
                        type: 'bool',
                    },
                }
            };

            this.wait(true);

            this.fields = this.getMetadata().get('integrations.' + this.integration + '.fields');

            Object.keys(this.fields).forEach(name => {
                this.model.defs.fields[name] = this.fields[name];
                this.dataFieldList.push(name);
            });

            this.model.populateDefaults();

            this.listenToOnce(this.model, 'sync', () => {
                this.createFieldView('bool', 'enabled');

                Object.keys(this.fields).forEach(name => {
                    this.createFieldView(this.fields[name]['type'], name, null, this.fields[name]);
                });

                this.wait(false);
            });

            this.model.fetch();
        },

        hideField: function (name) {
            this.$el.find('label[data-name="'+name+'"]').addClass('hide');
            this.$el.find('div.field[data-name="'+name+'"]').addClass('hide');

            var view = this.getView(name);

            if (view) {
                view.disabled = true;
            }
        },

        showField: function (name) {
            this.$el.find('label[data-name="'+name+'"]').removeClass('hide');
            this.$el.find('div.field[data-name="'+name+'"]').removeClass('hide');

            var view = this.getView(name);

            if (view) {
                view.disabled = false;
            }
        },

        afterRender: function () {
            if (!this.model.get('enabled')) {
                this.dataFieldList.forEach(name => {
                    this.hideField(name);
                });
            }

            this.listenTo(this.model, 'change:enabled', () => {
                if (this.model.get('enabled')) {
                    this.dataFieldList.forEach(name => {
                        this.showField(name);
                    });
                } else {
                    this.dataFieldList.forEach(name => {
                        this.hideField(name);
                    });
                }
            });
        },

        createFieldView: function (type, name, readOnly, params) {
            var viewName = this.model.getFieldParam(name, 'view') || this.getFieldManager().getViewName(type);

            this.createView(name, viewName, {
                model: this.model,
                selector: '.field[data-name="' + name + '"]',
                defs: {
                    name: name,
                    params: params
                },
                mode: readOnly ? 'detail' : 'edit',
                readOnly: readOnly,
            });

            this.fieldList.push(name);
        },

        save: function () {
            this.fieldList.forEach(field => {
                var view = this.getView(field);

                if (!view.readOnly) {
                    view.fetchToModel();
                }
            });

            var notValid = false;

            this.fieldList.forEach(field => {
                var fieldView = this.getView(field);

                if (fieldView && !fieldView.disabled) {
                    notValid = fieldView.validate() || notValid;
                }
            });

            if (notValid) {
                this.notify('Not valid', 'error');

                return;
            }

            this.listenToOnce(this.model, 'sync', () => {
                this.notify('Saved', 'success');
            });

            Espo.Ui.notify(this.translate('saving', 'messages'));

            this.model.save();
        },
    });
});
