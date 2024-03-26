

define('views/dashlets/fields/records/primary-filter', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            this.listenTo(this.model, 'change:entityType', () => {
                this.setupOptions();
                this.reRender();
            });
        },

        setupOptions: function () {
            var entityType = this.model.get('entityType');

            if (!entityType) {
                this.params.options = [];

                return;
            }

            var filterList = this.getMetadata().get(['clientDefs', entityType, 'filterList']) || [];
            this.params.options = [];

            filterList.forEach(item => {
                if (typeof item === 'object' && item.name) {
                    if (item.accessDataList) {
                        if (
                            !Espo.Utils
                                .checkAccessDataList(item.accessDataList, this.getAcl(), this.getUser(), null, true)
                        ) {
                            return false;
                        }
                    }

                    this.params.options.push(item.name);

                    return;
                }

                this.params.options.push(item);
            });

            this.params.options.unshift('all');

            this.translatedOptions = {};

            this.params.options.forEach(item => {
                this.translatedOptions[item] = this.translate(item, 'presetFilters', entityType);
            });
        },
    });
});
