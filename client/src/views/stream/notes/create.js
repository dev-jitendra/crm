

import NoteStreamView from 'views/stream/note';

class CreateNoteStreamView extends NoteStreamView {

    template = 'stream/notes/create'
    assigned = false
    messageName = 'create'
    isRemovable = false

    data() {
        return {
            ...super.data(),
            statusText: this.statusText,
            statusStyle: this.statusStyle,
        };
    }

    setup() {
        if (this.model.get('data')) {
            this.setupData();
        }

        this.createMessage();
    }

    setupData() {
        let data = this.model.get('data');

        this.assignedUserId = data.assignedUserId || null;
        this.assignedUserName = data.assignedUserName || null;

        this.messageData['assignee'] =
            $('<a>')
                .attr('href', '#User/view/' + this.assignedUserId)
                .text(this.assignedUserName);

        let isYou = false;

        if (this.isUserStream) {
            if (this.assignedUserId === this.getUser().id) {
                isYou = true;
            }
        }

        if (this.assignedUserId) {
            this.messageName = 'createAssigned';

            if (this.isThis) {
                this.messageName += 'This';

                if (this.assignedUserId === this.model.get('createdById')) {
                    this.messageName += 'Self';
                }
            } else {
                if (this.assignedUserId === this.model.get('createdById')) {
                    this.messageName += 'Self';
                }
                else if (isYou) {
                    this.messageName += 'You';
                }
            }
        }

        if (data.statusField) {
            let statusField = this.statusField = data.statusField;
            let statusValue = data.statusValue;

            this.statusStyle = data.statusStyle || 'default';
            this.statusText = this.getLanguage()
                .translateOption(statusValue, statusField, this.model.get('parentType'));
        }
    }
}

export default CreateNoteStreamView;

