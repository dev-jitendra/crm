

import ModalView from 'views/modal';

class CreatePostModalView extends ModalView {

    templateContent = '<div class="record">{{{record}}}</div>'

    shortcutKeys = {
        'Control+Enter': 'post',
    }

    setup() {
        this.headerText = this.translate('Create Post');

        this.buttonList = [
            {
                name: 'post',
                label: 'Post',
                style: 'primary',
                title: 'Ctrl+Enter',
                onClick: () => this.post(),
            },
            {
                name: 'cancel',
                label: 'Cancel',
                title: 'Esc',
                onClick: dialog => {
                    dialog.close();
                },
            }
        ];

        this.wait(true);

        this.getModelFactory().create('Note', model => {
            this.createView('record', 'views/stream/record/edit', {
                model: model,
                selector: '.record',
            }, view => {
                this.listenTo(view, 'after:save', () => {
                    this.trigger('after:save');
                });

                this.listenTo(view, 'disable-post-button', () => this.disableButton('post'));
                this.listenTo(view, 'enable-post-button', () => this.enableButton('post'));
            });

            this.wait(false);
        });
    }

    
    getRecordView() {
        return this.getView('record');
    }

    post() {
        this.getRecordView().save();
    }
}

export default CreatePostModalView;
