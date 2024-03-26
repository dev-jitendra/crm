

define('views/wysiwyg/modals/insert-link', ['views/modal'], function (Dep) {

    return Dep.extend({

        className: 'dialog dialog-record',

        template: 'wysiwyg/modals/insert-link',

        events: {
            'input [data-name="url"]': function () {
                this.controlInputs();
            },
            'paste [data-name="url"]': function () {
                this.controlInputs();
            },
        },

        shortcutKeys: {
            'Control+Enter': function () {
                if (this.hasAvailableActionItem('insert')) {
                    this.actionInsert();
                }
            },
        },

        data: function () {
            return {
                labels: this.options.labels || {},
            };
        },

        setup: function () {
            let labels = this.options.labels || {};

            this.headerText = labels.insert;

            this.buttonList = [
                {
                    name: 'insert',
                    text: this.translate('Insert'),
                    style: 'primary',
                    disabled: true,
                }
            ];

            this.linkInfo = this.options.linkInfo || {};

            if (this.linkInfo.url) {
                this.enableButton('insert');
            }
        },

        afterRender: function () {
            this.$url = this.$el.find('[data-name="url"]');
            this.$text = this.$el.find('[data-name="text"]');
            this.$openInNewWindow = this.$el.find('[data-name="openInNewWindow"]');

            let linkInfo = this.linkInfo;

            this.$url.val(linkInfo.url || '');
            this.$text.val(linkInfo.text || '');

            if ('isNewWindow' in linkInfo) {
                this.$openInNewWindow.get(0).checked = !!linkInfo.isNewWindow;
            }
        },

        controlInputs: function () {
            let url = this.$url.val().trim();

            if (url) {
                this.enableButton('insert');
            } else {
                this.disableButton('insert');
            }
        },

        actionInsert: function () {
            let url = this.$url.val().trim();
            let text = this.$text.val().trim();
            let openInNewWindow = this.$openInNewWindow.get(0).checked;

            let data = {
                url: url,
                text: text || url,
                isNewWindow: openInNewWindow,
                range: this.linkInfo.range,
            };

            this.trigger('insert', data);
            this.close();
        },
    });
});
