

define('views/admin/field-manager/modals/add-field', ['views/modal'], function (Dep) {

    return Dep.extend({

        backdrop: true,

        template: 'admin/field-manager/modals/add-field',

        events: {
            'click a[data-action="addField"]': function (e) {
                let type = $(e.currentTarget).data('type');

                this.addField(type);
            },
            'keyup input[data-name="quick-search"]': function (e) {
                this.processQuickSearch(e.currentTarget.value);
            },
        },

        data: function () {
            return {
                typeList: this.typeList,
            };
        },

        setup: function () {
            this.headerText = this.translate('Add Field', 'labels', 'Admin');

            this.typeList = [];

            let fieldDefs = this.getMetadata().get('fields');

            Object.keys(this.getMetadata().get('fields')).forEach(type => {
                if (type in fieldDefs) {
                    if (!fieldDefs[type].notCreatable) {
                        this.typeList.push(type);
                    }
                }
            });

            this.typeDataList = this.typeList.map(type => {
                return {
                    type: type,
                    label: this.translate(type, 'fieldTypes', 'Admin'),
                };
            });

            this.typeList.sort((v1, v2) => {
                return this.translate(v1, 'fieldTypes', 'Admin')
                    .localeCompare(this.translate(v2, 'fieldTypes', 'Admin'));
            });
        },

        addField: function (type) {
            this.trigger('add-field', type);
            this.remove();
        },

        afterRender: function () {
            this.$noData = this.$el.find('.no-data');

            this.typeList.forEach(type => {
                let text = this.translate(type, 'fieldInfo', 'FieldManager');

                let $el = this.$el.find('a.info[data-name="'+type+'"]');

                if (text === type) {
                    $el.addClass('hidden');

                    return;
                }

                text = this.getHelper().transformMarkdownText(text, {linksInNewTab: true}).toString();

                Espo.Ui.popover($el, {
                    content: text,
                    placement: 'left',
                }, this);
            });

            setTimeout(() => this.$el.find('input[data-name="quick-search"]').focus(), 50);
        },

        processQuickSearch: function (text) {
            text = text.trim();

            let $noData = this.$noData;

            $noData.addClass('hidden');

            if (!text) {
                this.$el.find('ul .list-group-item').removeClass('hidden');

                return;
            }

            let matchedList = [];

            let lowerCaseText = text.toLowerCase();

            this.typeDataList.forEach(item => {
                let matched =
                    item.label.toLowerCase().indexOf(lowerCaseText) === 0 ||
                    item.type.toLowerCase().indexOf(lowerCaseText) === 0;

                if (matched) {
                    matchedList.push(item.type);
                }
            });

            if (matchedList.length === 0) {
                this.$el.find('ul .list-group-item').addClass('hidden');

                $noData.removeClass('hidden');

                return;
            }

            this.typeDataList.forEach(item => {
                let $row = this.$el.find(`ul .list-group-item[data-name="${item.type}"]`);

                if (!~matchedList.indexOf(item.type)) {
                    $row.addClass('hidden');

                    return;
                }

                $row.removeClass('hidden');
            });
        },
    });
});
