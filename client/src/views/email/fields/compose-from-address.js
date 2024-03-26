

define('views/email/fields/compose-from-address', ['views/fields/base', 'ui/select'],
function (Dep, Select) {

    return Dep.extend({

        detailTemplate: 'email/fields/email-address-varchar/detail',
        editTemplate: 'email/fields/compose-from-address/edit',

        data: function () {
            let noSmtpMessage = this.translate('noSmtpSetup', 'messages', 'Email');

            let linkHtml = $('<a>')
                    .attr('href', '#EmailAccount')
                    .text(this.translate('EmailAccount', 'scopeNamesPlural'))
                    .get(0).outerHTML;

            noSmtpMessage = noSmtpMessage.replace('{link}', linkHtml);

            return {
                list: this.list,
                noSmtpMessage: noSmtpMessage,
                ...Dep.prototype.data.call(this),
            };
        },

        setup: function () {
            Dep.prototype.setup.call(this);

            this.nameHash = {...(this.model.get('nameHash') || {})};
            this.typeHash = this.model.get('typeHash') || {};
            this.idHash = this.model.get('idHash') || {};

            this.list = this.getUser().get('emailAddressList') || [];
        },

        afterRenderEdit: function () {
            if (this.$element.length) {
                Select.init(this.$element);
            }
        },

        getValueForDisplay: function () {
            if (this.isDetailMode()) {
                let address = this.model.get(this.name);

                return this.getDetailAddressHtml(address);
            }

            return Dep.prototype.getValueForDisplay.call(this);
        },

        getDetailAddressHtml: function (address) {
            if (!address) {
                return '';
            }

            let name = this.nameHash[address] || null;

            let entityType = this.typeHash[address] || null;
            let id = this.idHash[address] || null;

            if (id && name) {
                return $('<div>')
                    .append(
                        $('<a>')
                            .attr('href', `#${entityType}/view/${id}`)
                            .attr('data-scope', entityType)
                            .attr('data-id', id)
                            .text(name),
                        ' ',
                        $('<span>').addClass('text-muted chevron-right'),
                        ' ',
                        $('<span>').text(address)
                    )
                    .get(0).outerHTML;
            }

            let $div = $('<div>');

            if (name) {
                $div.append(
                    $('<span>')
                        .addClass('email-address-line')
                        .text(name)
                        .append(
                            ' ',
                            $('<span>').addClass('text-muted chevron-right'),
                            ' ',
                            $('<span>').text(address)
                        )
                );

                return $div.get(0).outerHTML;
            }

            $div.append(
                $('<span>')
                    .addClass('email-address-line')
                    .text(address)
            )

            return $div.get(0).outerHTML;
        },
    });
});
