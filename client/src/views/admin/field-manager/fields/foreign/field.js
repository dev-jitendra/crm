

define('views/admin/field-manager/fields/foreign/field', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            if (!this.model.isNew()) {
                this.setReadOnly(true);
            }

            this.listenTo(this.model, 'change:field', () => {
                this.manageField();
            });

            this.viewValue = this.model.get('view');
        },

        setupOptions: function () {
            this.listenTo(this.model, 'change:link', () => {
                this.setupOptionsByLink();
                this.reRender();
            });

            this.setupOptionsByLink();
        },

        setupOptionsByLink: function () {
            this.typeList = this.getMetadata().get(['fields', 'foreign', 'fieldTypeList']);

            var link = this.model.get('link');

            if (!link) {
                this.params.options = [''];

                return;
            }

            var scope = this.getMetadata().get(['entityDefs', this.options.scope, 'links', link, 'entity']);

            if (!scope) {
                this.params.options = [''];

                return;
            }

            var fields = this.getMetadata().get(['entityDefs', scope, 'fields']) || {};

            this.params.options = Object.keys(Espo.Utils.clone(fields)).filter(item => {
                var type = fields[item].type;

                if (!~this.typeList.indexOf(type)) {
                    return;
                }

                if (
                    fields[item].disabled ||
                    fields[item].utility ||
                    fields[item].directAccessDisabled ||
                    fields[item].notStorable
                ) {
                    return;
                }

                return true;
            });

            this.translatedOptions = {};

            this.params.options.forEach(item => {
                this.translatedOptions[item] = this.translate(item, 'fields', scope);
            });

            this.params.options = this.params.options.sort((v1, v2) => {
                return this.translate(v1, 'fields', scope).localeCompare(this.translate(v2, 'fields', scope));
            });

            this.params.options.unshift('');
        },

        manageField: function () {
            if (!this.model.isNew()) {
                return;
            }

            var link = this.model.get('link');
            var field = this.model.get('field');

            if (!link || !field) {
                return;
            }

            var scope = this.getMetadata().get(['entityDefs', this.options.scope, 'links', link, 'entity']);

            if (!scope) {
                return;
            }

            var type = this.getMetadata().get(['entityDefs', scope, 'fields', field, 'type']);

            this.viewValue = this.getMetadata().get(['fields', 'foreign', 'fieldTypeViewMap', type]);
        },

        fetch: function () {
            var data = Dep.prototype.fetch.call(this);

            if (this.model.isNew()) {
                if (this.viewValue) {
                    data['view'] = this.viewValue;
                }
            }

            return data;
        },
    });
});
