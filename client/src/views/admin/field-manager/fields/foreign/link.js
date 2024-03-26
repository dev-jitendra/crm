

define('views/admin/field-manager/fields/foreign/link', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            if (!this.model.isNew()) {
                this.setReadOnly(true);
            }
        },

        setupOptions: function () {
            var links = this.getMetadata().get(['entityDefs', this.options.scope, 'links']) || {};

            this.params.options = Object.keys(Espo.Utils.clone(links)).filter((item) => {
                if (links[item].type !== 'belongsTo' && links[item].type !== 'hasOne') {
                    return;
                }

                if (links[item].noJoin) {
                    return;
                }

                if (links[item].disabled) {
                    return;
                }

                return true;
            });

            var scope = this.options.scope;

            this.translatedOptions = {};

            this.params.options.forEach((item) => {
                this.translatedOptions[item] = this.translate(item, 'links', scope);
            });

            this.params.options = this.params.options.sort((v1, v2) => {
                return this.translate(v1, 'links', scope).localeCompare(this.translate(v2, 'links', scope));
            });

            this.params.options.unshift('');
        },
    });
});
