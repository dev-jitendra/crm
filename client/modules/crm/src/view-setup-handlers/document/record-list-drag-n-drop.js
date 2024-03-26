

import _ from 'underscore';
import {Events} from 'bullbone';

let Handler = function (view) {
    this.view = view;
};

_.extend(Handler.prototype, {

    process: function () {
        this.listenTo(this.view, 'after:render', () => this.initDragDrop());
        this.listenTo(this.view, 'remove', () => this.disable());
    },

    disable: function () {
        let $el = this.view.$el.parent();
        
        let el = $el.get(0);

        $el.off('drop');

        if (!el) {
            return;
        }

        if (!this.onDragoverBind) {
            return;
        }

        el.removeEventListener('dragover', this.onDragoverBind);
        el.removeEventListener('dragenter', this.onDragenterBind);
        el.removeEventListener('dragleave', this.onDragleaveBind);
    },

    initDragDrop: function () {
        this.disable();

        let $el = this.view.$el.parent();
        let el = $el.get(0);

        $el.on('drop', e => {
            e.preventDefault();
            e.stopPropagation();

            e = e.originalEvent;

            if (
                e.dataTransfer &&
                e.dataTransfer.files &&
                e.dataTransfer.files.length === 1 &&
                this.dropEntered
            ) {
                this.removeDrop();

                this.create(e.dataTransfer.files[0]);

                return;
            }

            this.removeDrop($el);
        });


        this.dropEntered = false;

        this.onDragoverBind = this.onDragover.bind(this);
        this.onDragenterBind = this.onDragenter.bind(this);
        this.onDragleaveBind = this.onDragleave.bind(this);

        el.addEventListener('dragover', this.onDragoverBind);
        el.addEventListener('dragenter', this.onDragenterBind);
        el.addEventListener('dragleave', this.onDragleaveBind);
    },

    renderDrop: function () {
        this.dropEntered = true;

        let $backdrop =
            $('<div class="dd-backdrop">')
                .css('pointer-events', 'none')
                .append('<span class="fas fa-paperclip"></span>')
                .append(' ')
                .append(
                    $('<span>')
                        .text(this.view.getLanguage().translate('Create Document', 'labels', 'Document'))
                );

        this.view.$el.append($backdrop);
    },

    removeDrop: function () {
        this.view.$el.find('> .dd-backdrop').remove();

        this.dropEntered = false;
    },

    create: function (file) {
        this.view
            .actionQuickCreate()
            .then(view => {
                let fileView = view.getRecordView().getFieldView('file');

                if (!fileView) {
                    let msg = "No 'file' field on the layout.";

                    Espo.Ui.error(msg);
                    console.error(msg);

                    return;
                }

                if (fileView.isRendered()) {
                    fileView.uploadFile(file);

                    return;
                }

                this.listenToOnce(fileView, 'after:render', () => {
                    fileView.uploadFile(file);
                });
            });
    },

    
    onDragover: function (e) {
        e.preventDefault();
    },

    
    onDragenter: function (e) {
        e.preventDefault();

        if (!e.dataTransfer.types || !e.dataTransfer.types.length) {
            return;
        }

        if (!~e.dataTransfer.types.indexOf('Files')) {
            return;
        }

        if (!this.dropEntered) {
            this.renderDrop();
        }
    },

    
    onDragleave: function (e) {
        e.preventDefault();

        if (!this.dropEntered) {
            return;
        }

        let fromElement = e.fromElement || e.relatedTarget;

        if (
            fromElement &&
            $.contains(this.view.$el.parent().get(0), fromElement)
        ) {
            return;
        }

        if (
            fromElement &&
            fromElement.parentNode &&
            fromElement.parentNode.toString() === '[object ShadowRoot]'
        ) {
            return;
        }

        this.removeDrop();
    },
});

Object.assign(Handler.prototype, Events);


export default Handler;
