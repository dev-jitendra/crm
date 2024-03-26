

define('views/email/fields/body', ['views/fields/wysiwyg'], function (Dep) {

    return Dep.extend({

        useIframe: true,

        getAttributeList: function () {
            return ['body', 'bodyPlain'];
        },

        setupToolbar: function () {
            Dep.prototype.setupToolbar.call(this);

            this.toolbar.unshift([
                'insert-field',
                ['insert-field']
            ]);

            this.buttons['insert-field'] = function (context) {
                var ui = $.summernote.ui;
                var button = ui.button({
                    contents: '<i class="fas fa-plus"></i>',
                    tooltip: this.translate('Insert Field', 'labels', 'Email'),
                    click: function () {
                        this.showInsertFieldModal();
                    }.bind(this)
                });
                return button.render();
            }.bind(this);

            this.listenTo(this.model, 'change', function (m) {
                if (!this.isRendered()) return;
                if (m.hasChanged('parentId') || m.hasChanged('to')) {
                    this.controInsertFieldButton();
                }
            }, this);
        },

        afterRender: function () {
            Dep.prototype.afterRender.call(this);

            this.controInsertFieldButton();
        },

        controInsertFieldButton: function () {
            var $b = this.$el.find('.note-insert-field > button');

            if (this.model.get('to') && this.model.get('to').length || this.model.get('parentId')) {
                $b.removeAttr('disabled').removeClass('disabled');
            } else {
                $b.attr('disabled', 'disabled').addClass('disabled');
            }
        },

        showInsertFieldModal: function () {
            var to = this.model.get('to');
            if (to) {
                to = to.split(';')[0].trim();
            }
            var parentId = this.model.get('parentId');
            var parentType = this.model.get('parentType');

            Espo.Ui.notify(' ... ');

            this.createView('insertFieldDialog', 'views/email/modals/insert-field', {
                parentId: parentId,
                parentType: parentType,
                to: to,
            }, function (view) {
                view.render();
                Espo.Ui.notify();

                this.listenToOnce(view, 'insert', function (string) {
                    if (this.$summernote) {
                        if (~string.indexOf('\n')) {
                            string = string.replace(/(?:\r\n|\r|\n)/g, '<br>');
                            var html = '<p>' + string + '</p>';
                            this.$summernote.summernote('editor.pasteHTML', html);
                        } else {
                            this.$summernote.summernote('editor.insertText', string);
                        }
                    }
                    this.clearView('insertFieldDialog');
                }, this);
            });
        },

    });
});
