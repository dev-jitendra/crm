

define('views/admin/field-manager/fields/dynamic-logic-conditions', ['views/fields/base'], function (Dep) {

    return Dep.extend({

        detailTemplate: 'admin/field-manager/fields/dynamic-logic-conditions/detail',
        editTemplate: 'admin/field-manager/fields/dynamic-logic-conditions/edit',

        events: {
            'click [data-action="editConditions"]': function () {
                this.edit();
            }
        },

        data: function () {
        },

        setup: function () {
            this.conditionGroup = Espo.Utils.cloneDeep((this.model.get(this.name) || {}).conditionGroup || []);

            this.scope = this.params.scope || this.options.scope;

            this.createStringView();
        },

        createStringView: function () {
            this.createView('conditionGroup', 'views/admin/dynamic-logic/conditions-string/group-base', {
                selector: '.top-group-string-container',
                itemData: {
                    value: this.conditionGroup
                },
                operator: 'and',
                scope: this.scope,
            }, (view) => {
                if (this.isRendered()) {
                    view.render();
                }
            });
        },

        edit: function () {
            this.createView('modal', 'views/admin/dynamic-logic/modals/edit', {
                conditionGroup: this.conditionGroup,
                scope: this.scope,
            }, (view) => {
                view.render();

                this.listenTo(view, 'apply', (conditionGroup) => {
                    this.conditionGroup = conditionGroup;

                    this.trigger('change');

                    this.createStringView();
                });
            });
        },

        fetch: function () {
            var data = {};

            data[this.name] = {
                conditionGroup: this.conditionGroup,
            };

            if (this.conditionGroup.length === 0) {
                data[this.name] = null;
            }

            return data;
        },
    });
});
