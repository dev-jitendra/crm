

define('views/admin/formula/fields/attribute', ['views/fields/multi-enum', 'ui/multi-select'],
function (Dep, MultiSelect) {

    return Dep.extend({

        setupOptions: function () {
            Dep.prototype.setupOptions.call(this);

            if (this.options.attributeList) {
                this.params.options = this.options.attributeList;

                return;
            }

            const attributeList = this.getFieldManager()
                .getEntityTypeAttributeList(this.options.scope)
                .concat(['id'])
                .sort();

            const links = this.getMetadata().get(['entityDefs', this.options.scope, 'links']) || {};

            const linkList = [];

            Object.keys(links).forEach(link => {
                const type = links[link].type;
                const scope = links[link].entity;

                if (!type) {
                    return;
                }

                if (!scope) {
                    return;
                }

                if (
                    links[link].disabled ||
                    links[link].utility
                ) {
                    return;
                }

                if (~['belongsToParent', 'hasOne', 'belongsTo'].indexOf(type)) {
                    linkList.push(link);
                }
            });

            linkList.sort();

            linkList.forEach(link => {
                const scope = links[link].entity;

                let linkAttributeList = this.getFieldManager()
                    .getEntityTypeAttributeList(scope)
                    .sort();

                linkAttributeList.forEach(item => {
                    attributeList.push(link + '.' + item);
                });
            });

            this.params.options = attributeList;
        },

        afterRender: function () {
            Dep.prototype.afterRender.call(this);

            if (this.$element) {
                MultiSelect.focus(this.$element);
            }
        },
    });
});
