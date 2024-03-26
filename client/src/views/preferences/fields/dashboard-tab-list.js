

define('views/preferences/fields/dashboard-tab-list', ['views/fields/array'], function (Dep) {

    return Dep.extend({

        maxItemLength: 36,

        setup: function () {
            Dep.prototype.setup.call(this);

            this.translatedOptions = {};

            let list = this.model.get(this.name) || [];

            list.forEach(value => {
                this.translatedOptions[value] = value;
            });

            this.validations.push('uniqueLabel');
        },

        getItemHtml: function (value) {
            value = value.toString();

            let translatedValue = this.translatedOptions[value] || value;

            return $('<div>')
                .addClass('list-group-item link-with-role form-inline')
                .attr('data-value', value)
                .append(
                    $('<div>')
                        .addClass('pull-left')
                        .css('width', '92%')
                        .css('display', 'inline-block')
                        .append(
                            $('<input>')
                                .attr('maxLength', this.maxItemLength)
                                .attr('data-name', 'translatedValue')
                                .attr('data-value', value)
                                .addClass('role form-control input-sm')
                                .attr('value', translatedValue)
                                .css('width', '65%')
                        )
                )
                .append(
                    $('<div>')
                        .css('width', '8%')
                        .css('display', 'inline-block')
                        .css('vertical-align', 'top')
                        .append(
                            $('<a>')
                                .attr('role', 'button')
                                .attr('tabindex', '0')
                                .addClass('pull-right')
                                .attr('data-value', value)
                                .attr('data-action', 'removeValue')
                                .append(
                                    $('<span>').addClass('fas fa-times')
                                )
                        )
                )
                .append(
                    $('<br>').css('clear', 'both')
                )
                .get(0).outerHTML;
        },

        validateUniqueLabel: function () {
            let keyList = this.model.get(this.name) || [];
            let labels = this.model.get('translatedOptions') || {};
            let metLabelList = [];

            for (let key of keyList) {
                let label = labels[key];

                if (!label) {
                    return true;
                }

                if (metLabelList.indexOf(label) !== -1) {
                    return true;
                }

                metLabelList.push(label);
            }

            return false;
        },

        fetch: function () {
            let data = Dep.prototype.fetch.call(this);

            data.translatedOptions = {};

            (data[this.name] || []).forEach(value => {
                let valueInternal = value.replace(/"/g, '\\"');

                data.translatedOptions[value] = this.$el
                    .find('input[data-name="translatedValue"][data-value="'+valueInternal+'"]')
                    .val() || value;
            });

            return data;
        },
    });
});
