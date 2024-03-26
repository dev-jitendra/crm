

define('crm:views/call/fields/leads', ['crm:views/meeting/fields/attendees', 'crm:views/call/fields/contacts'],
function (Dep, Contacts) {

    return Dep.extend({

        getAttributeList: function () {
            let list = Dep.prototype.getAttributeList.call(this);

            list.push('phoneNumbersMap');

            return list;
        },

        getDetailLinkHtml: function (id, name) {
            return Contacts.prototype.getDetailLinkHtml.call(this, id, name);
        },

        getDetailLinkHtml1: function (id, name) {
            var html = Dep.prototype.getDetailLinkHtml.call(this, id, name);

            var key = this.foreignScope + '_' + id;
            var number = null;
            var phoneNumbersMap = this.model.get('phoneNumbersMap') || {};
            if (key in phoneNumbersMap) {
                number = phoneNumbersMap[key];
                var innerHtml = $(html).html();

                innerHtml += (
                    ' <span class="text-muted chevron-right"></span> ' +
                    '<a href="tel:' + number + '" class="small" data-phone-number="' + number + '" data-action="dial">' +
                    number + '</a>'
                );

                html = '<div>' + innerHtml + '</div>';
            }

            return html;
        },
    });
});
