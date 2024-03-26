

import NoteStreamView from 'views/stream/note';

class EmailSentNoteStreamView extends NoteStreamView {

    template = 'stream/notes/email-sent'
    isRemovable = false

    data() {
        return {
            ...super.data(),
            emailId: this.emailId,
            emailName: this.emailName,
            hasPost: this.hasPost,
            hasAttachments: this.hasAttachments,
            emailIconClassName: this.getMetadata().get(['clientDefs', 'Email', 'iconClass']) || '',
        };
    }

    setup() {
        let data = this.model.get('data') || {};

        this.emailId = data.emailId;
        this.emailName = data.emailName;

        if (
            this.parentModel &&
            (
                this.model.get('parentType') === this.parentModel.entityType &&
                this.model.get('parentId') === this.parentModel.id
            )
        ) {
            if (this.model.get('post')) {
                this.createField('post', null, null, 'views/stream/fields/post');
                this.hasPost = true;
            }

            if ((this.model.get('attachmentsIds') || []).length) {
                this.createField('attachments', 'attachmentMultiple', {}, 'views/stream/fields/attachment-multiple');
                this.hasAttachments = true;
            }
        }

        this.messageData['email'] =
            $('<a>')
                .attr('href', '#Email/view/' + data.emailId)
                .text(data.emailName);

        this.messageName = 'emailSent';

        this.messageData['by'] =
            $('<a>')
                .attr('href', '#' + data.personEntityType + '/view/' + data.personEntityId)
                .text(data.personEntityName);

        if (this.isThis) {
            this.messageName += 'This';
        }

        this.createMessage();
    }
}

export default EmailSentNoteStreamView;
