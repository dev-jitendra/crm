

import BaseFieldView from 'views/fields/base';

class NotificationContainerFieldView extends BaseFieldView {

    type = 'notification'

    listTemplate = 'notification/fields/container'
    detailTemplate = 'notification/fields/container'

    setup() {
        switch (this.model.get('type')) {
            case 'Note':
                this.processNote(this.model.get('noteData'));

                break;

            case 'MentionInPost':
                this.processMentionInPost(this.model.get('noteData'));

                break;

            default:
                this.process();
        }
    }

    process() {
        let type = this.model.get('type');

        if (!type) {
            return;
        }

        type = type.replace(/ /g, '');

        let viewName = this.getMetadata()
            .get('clientDefs.Notification.itemViews.' + type) ||
            'views/notification/items/' + Espo.Utils.camelCaseToHyphen(type);

        this.createView('notification', viewName, {
            model: this.model,
            fullSelector: this.options.containerSelector  + ' li[data-id="' + this.model.id + '"]',
        });
    }

    processNote(data) {
        if (!data) {
            return;
        }

        this.wait(true);

        this.getModelFactory().create('Note', model => {
            model.set(data);

            let viewName = this.getMetadata().get('clientDefs.Note.itemViews.' + data.type) ||
                'views/stream/notes/' + Espo.Utils.camelCaseToHyphen(data.type);

            this.createView('notification', viewName, {
                model: model,
                isUserStream: true,
                fullSelector: this.options.containerSelector  + ' li[data-id="' + this.model.id + '"]',
                onlyContent: true,
                isNotification: true,
            });

            this.wait(false);
        });
    }

    processMentionInPost(data) {
        if (!data) {
            return;
        }

        this.wait(true);

        this.getModelFactory().create('Note', model => {
            model.set(data);

            let viewName = 'views/stream/notes/mention-in-post';

            this.createView('notification', viewName, {
                model: model,
                userId: this.model.get('userId'),
                isUserStream: true,
                fullSelector: this.options.containerSelector + ' li[data-id="' + this.model.id + '"]',
                onlyContent: true,
                isNotification: true,
            });

            this.wait(false);
        });
    }
}

export default NotificationContainerFieldView;
