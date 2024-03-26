

define('crm:views/call/fields/contacts', ['crm:views/meeting/fields/contacts'], function (Dep) {

    return Dep.extend({

        getAttributeList: function () {
            let list = Dep.prototype.getAttributeList.call(this);

            list.push('phoneNumbersMap');

            return list;
        },

        getDetailLinkHtml: function (id, name) {
            let html = Dep.prototype.getDetailLinkHtml.call(this, id, name);

            let key = this.foreignScope + '_' + id;
            let phoneNumbersMap = this.model.get('phoneNumbersMap') || {};

            if (!(key in phoneNumbersMap)) {
                return html;
            }

            let number = phoneNumbersMap[key];

            let $item = $(html);

            $item
                .append(
                    ' ',
                    $('<span>').addClass('text-muted chevron-right'),
                    ' ',
                    $('<a>')
                        .attr('href', 'tel:' + number)
                        .attr('data-phone-number', number)
                        .attr('data-action', 'dial')
                        .addClass('small')
                        .text(number)
                )

            return $('<div>')
                .append($item)
                .get(0).outerHTML;
        },
    });
});
