

define('views/admin/layouts/rows', ['views/admin/layouts/base'], function (Dep) {

    return Dep.extend({

        template: 'admin/layouts/rows',

        dataAttributeList: null,
        dataAttributesDefs: {},
        editable: false,

        data: function () {
            return {
                scope: this.scope,
                type: this.type,
                buttonList: this.buttonList,
                enabledFields: this.enabledFields,
                disabledFields: this.disabledFields,
                layout: this.rowLayout,
                dataAttributeList: this.dataAttributeList,
                dataAttributesDefs: this.dataAttributesDefs,
                editable: this.editable,
            };
        },

        setup: function () {
            this.itemsData = {};

            Dep.prototype.setup.call(this);

            this.events['click a[data-action="editItem"]'] = e => {
                const name = $(e.target).closest('li').data('name');

                this.editRow(name);
            };

            this.on('update-item', (name, attributes) => {
                this.itemsData[name] = Espo.Utils.cloneDeep(attributes);
            });

            Espo.loader.require('res!client/css/misc/layout-manager-rows.css', styleCss => {
                this.$style = $('<style>').html(styleCss).appendTo($('body'));
            });
        },

        onRemove: function () {
            if (this.$style) {
                this.$style.remove();
            }
        },

        editRow: function (name) {
            const attributes = Espo.Utils.cloneDeep(this.itemsData[name] || {});
            attributes.name = name;

            this.openEditDialog(attributes)
        },

        afterRender: function () {
            $('#layout ul.enabled, #layout ul.disabled').sortable({
                connectWith: '#layout ul.connected',
                update: e => {
                    if (!$(e.target).hasClass('disabled')) {
                        this.onDrop(e);
                        this.setIsChanged();
                    }
                },
            });

            this.$el.find('.enabled-well').focus();
        },

        onDrop: function (e) {},

        fetch: function () {
            const layout = [];

            $("#layout ul.enabled > li").each((i, el) => {
                const o = {};

                const name = $(el).data('name');

                const attributes = this.itemsData[name] || {};
                attributes.name = name;

                this.dataAttributeList.forEach(attribute => {
                    const defs = this.dataAttributesDefs[attribute] || {};

                    if (defs.notStorable) {
                        return;
                    }

                    const value = attributes[attribute] || null;

                    if (value) {
                        o[attribute] = value;
                    }
                });

                layout.push(o);
            });

            return layout;
        },

        validate: function (layout) {
            if (layout.length === 0) {
                this.notify('Layout cannot be empty', 'error');

                return false;
            }

            return true;
        }
    });
});
