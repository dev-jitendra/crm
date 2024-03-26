

define('views/wysiwyg/modals/insert-image', ['views/modal'], function (Dep) {

    return Dep.extend({

        className: 'dialog dialog-record',

        template: 'wysiwyg/modals/insert-image',

        events: {
            'click [data-action="insert"]': function () {
                this.actionInsert();
            },
            'input [data-name="url"]': function () {
                this.controlInsertButton();
            },
            'paste [data-name="url"]': function () {
                this.controlInsertButton();
            },
        },

        shortcutKeys: {
            'Control+Enter': function () {
                if (!this.$el.find('[data-name="insert"]').hasClass('disabled')) {
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

            this.buttonList = [];
        },

        afterRender: function () {
            let $files = this.$el.find('[data-name="files"]');

            $files.replaceWith(
                $files.clone()
                    .on('change', (e) => {
                      this.trigger('upload', e.target.files || e.target.value);
                      this.close();
                    })
                    .val('')
            );
        },

        controlInsertButton: function () {
            let value = this.$el.find('[data-name="url"]').val().trim();

            let $button = this.$el.find('[data-name="insert"]');

            if (value) {
                $button.removeClass('disabled').removeAttr('disabled');
            } else {
                $button.addClass('disabled').attr('disabled', 'disabled');
            }
        },

        actionInsert: function () {
            let url = this.$el.find('[data-name="url"]').val().trim();

            this.trigger('insert', url);
            this.close();
        },
    });
});
