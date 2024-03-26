

define('crm:views/opportunity/admin/field-manager/fields/probability-map', ['views/fields/base'], function (Dep) {

    return Dep.extend({

        editTemplate: 'crm:opportunity/admin/field-manager/fields/probability-map/edit',

        setup: function () {
            Dep.prototype.setup.call(this);

            this.listenTo(this.model, 'change:options', function (m, v, o) {
                let probabilityMap = this.model.get('probabilityMap') || {}

                if (o.ui) {
                    (this.model.get('options') || []).forEach(item => {
                        if (!(item in probabilityMap)) {
                            probabilityMap[item] = 50;
                        }
                    });

                    this.model.set('probabilityMap', probabilityMap);
                }

                this.reRender();
            });
        },

        data: function () {
            let data = {};

            let values = this.model.get('probabilityMap') || {};

            data.stageList = this.model.get('options') || [];
            data.values = values;

            return data;
        },

        fetch: function () {
            let data = {
                probabilityMap: {},
            };

            (this.model.get('options') || []).forEach(item => {
                data.probabilityMap[item] = parseInt(this.$el.find('input[data-name="'+item+'"]').val());
            });

            return data;
        },

        afterRender: function () {
            this.$el.find('input').on('change', () => {
                this.trigger('change')
            });
        },
    });
});
