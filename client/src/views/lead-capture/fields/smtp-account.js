

define('views/lead-capture/fields/smtp-account', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        dataUrl: 'LeadCapture/action/smtpAccountDataList',

        getAttributeList: function () {
            return [this.name, 'inboundEmailId'];
        },

        data: function () {
            var data = Dep.prototype.data.call(this);

            data.valueIsSet = true;
            data.isNotEmpty = true;

            return data;
        },

        setupOptions: function () {
            Dep.prototype.setupOptions.call(this);

            this.params.options = [];
            this.translatedOptions = {};

            this.params.options.push('system');

            if (!this.loadedOptionList) {
                if (this.model.get('inboundEmailId')) {
                    var item = 'inboundEmail:' + this.model.get('inboundEmailId');

                    this.params.options.push(item);

                    this.translatedOptions[item] =
                        (this.model.get('inboundEmailName') || this.model.get('inboundEmailId')) +
                        ' (' + this.translate('group', 'labels', 'MassEmail') + ')';
                }
            } else {
                this.loadedOptionList.forEach((item) => {
                    this.params.options.push(item);

                    this.translatedOptions[item] =
                        (this.loadedOptionTranslations[item] || item) +
                        ' (' + this.translate('group', 'labels', 'MassEmail') + ')';
                });
            }

            this.translatedOptions['system'] =
                this.getConfig().get('outboundEmailFromAddress') +
                ' (' + this.translate('system', 'labels', 'MassEmail') + ')';
        },

        getValueForDisplay: function () {
            if (!this.model.has(this.name) && this.isReadMode()) {
                if (this.model.has('inboundEmailId')) {
                    if (this.model.get('inboundEmailId')) {
                        return 'inboundEmail:' + this.model.get('inboundEmailId');
                    } else {
                        return 'system';
                    }
                } else {
                    return '...';
                }
            }

            return this.model.get(this.name);
        },

        setup: function () {
            Dep.prototype.setup.call(this);

            if (
                this.getAcl().checkScope('MassEmail', 'create') ||
                this.getAcl().checkScope('MassEmail', 'edit')
            ) {

                Espo.Ajax.getRequest(this.dataUrl).then(dataList => {
                    if (!dataList.length) {
                        return;
                    }

                    this.loadedOptionList = [];

                    this.loadedOptionTranslations = {};
                    this.loadedOptionAddresses = {};
                    this.loadedOptionFromNames = {};

                    dataList.forEach(item => {
                        this.loadedOptionList.push(item.key);

                        this.loadedOptionTranslations[item.key] = item.emailAddress;
                        this.loadedOptionAddresses[item.key] = item.emailAddress;
                        this.loadedOptionFromNames[item.key] = item.fromName || '';
                    });

                    this.setupOptions();
                    this.reRender();
                });
            }
        },

        fetch: function () {
            var data = {};
            var value = this.$element.val();

            data[this.name] = value;

            if (!value || value === 'system') {
                data.inboundEmailId = null;
                data.inboundEmailName = null;
            }
            else {
                var arr = value.split(':');

                if (arr.length > 1) {
                    data.inboundEmailId = arr[1];
                    data.inboundEmailName = this.translatedOptions[data.inboundEmailId] || data.inboundEmailId;
                }
            }

            return data;
        },
    });
});
