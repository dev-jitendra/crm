

define('views/admin/field-manager/fields/options-with-style', ['views/admin/field-manager/fields/options'],
function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            this.optionsStyleMap = this.model.get('style') || {};

            this.styleList = [
                'default',
                'success',
                'danger',
                'warning',
                'info',
                'primary',
            ];

            this.events['click [data-action="selectOptionItemStyle"]'] = (e) => {
                let $target = $(e.currentTarget);
                let style = $target.data('style');
                let value = $target.data('value').toString();

                this.changeStyle(value, style);
            };
        },

        changeStyle: function (value, style) {
            let valueInternal = value.replace(/"/g, '\\"');

            this.$el
                .find('[data-action="selectOptionItemStyle"][data-value="' + valueInternal + '"] .check-icon')
                .addClass('hidden');

            this.$el
                .find('[data-action="selectOptionItemStyle"][data-value="' + valueInternal + '"]' +
                    '[data-style="'+style+'"] .check-icon')
                .removeClass('hidden');

            let $item = this.$el.find('.list-group-item[data-value="' + valueInternal + '"]').find('.item-text');

            this.styleList.forEach(item => {
                $item.removeClass('text-' + item);
            });

            $item.addClass('text-' + style);

            if (style === 'default') {
                style = null;
            }

            this.optionsStyleMap[value] = style;
        },

        getItemHtml: function (value) {
            

            let html = Dep.prototype.getItemHtml.call(this, value);

            let styleList = this.styleList;
            let styleMap = this.optionsStyleMap;

            let style = 'default';
            let $liList = [];

            styleList.forEach(item => {
                let isHidden = true;

                if (styleMap[value] === item) {
                    style = item;
                    isHidden = false;
                }
                else {
                    if (item === 'default' && !styleMap[value]) {
                        isHidden = false;
                    }
                }

                let text = this.getLanguage().translateOption(item, 'style', 'LayoutManager');

                let $li = $('<li>')
                    .append(
                        $('<a>')
                            .attr('role', 'button')
                            .attr('tabindex', '0')
                            .attr('data-action', 'selectOptionItemStyle')
                            .attr('data-style', item)
                            .attr('data-value', value)
                            .append(
                                $('<span>')
                                    .addClass('check-icon fas fa-check pull-right')
                                    .addClass(isHidden ? 'hidden' : ''),
                                $('<div>')
                                    .addClass('text-' + item)
                                    .text(text)
                            )
                    );

                $liList.push($li);
            });

            let $dropdown = $('<div>')
                .addClass('btn-group pull-right')
                .append(
                    $('<button>')
                        .addClass('btn btn-link btn-sm dropdown-toggle')
                        .attr('type', 'button')
                        .attr('data-toggle', 'dropdown')
                        .append(
                            $('<span>').addClass('caret')
                        ),
                    $('<ul>')
                        .addClass('dropdown-menu pull-right')
                        .append($liList)
                );

            let $item = $(html);

            $item.find('.item-content > input').after($dropdown);
            $item.find('.item-text').addClass('text-' + style);
            $item.addClass('link-group-item-with-columns');

            return $item.get(0).outerHTML;
        },

        fetch: function () {
            let data = Dep.prototype.fetch.call(this);

            data.style = {};

            (data.options || []).forEach(item => {
                data.style[item] = this.optionsStyleMap[item] || null;
            });

            return data;
        },
    });
});
