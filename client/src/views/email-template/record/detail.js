

define('views/email-template/record/detail', ['views/record/detail'], function (Dep) {

    return Dep.extend({

        duplicateAction: true,

        saveAndContinueEditingAction: true,

        setup: function () {
            Dep.prototype.setup.call(this);
            this.listenToInsertField();


            this.hideField('insertField');

            this.on('before:set-edit-mode', function () {
                this.showField('insertField');
            }, this);

            this.on('before:set-detail-mode', function () {
                this.hideField('insertField');
            }, this);
        },

        listenToInsertField: function () {
            this.listenTo(this.model, 'insert-field', function (o) {
                var tag = '{' + o.entityType + '.' + o.field + '}';

                var bodyView = this.getFieldView('body');
                if (!bodyView) return;

                if (this.model.get('isHtml')) {
                    var $anchor = $(window.getSelection().anchorNode);
                    if (!$anchor.closest('.note-editing-area').length) return;
                    bodyView.$summernote.summernote('insertText', tag);
                } else {
                    var $body = bodyView.$element;
                    var text = $body.val();
                    text += tag;
                    $body.val(text);
                }
            }, this);
        },
    });
});
