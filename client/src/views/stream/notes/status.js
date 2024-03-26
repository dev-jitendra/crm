

import NoteStreamView from 'views/stream/note';

class StatusNoteStreamView extends NoteStreamView {

    template = 'stream/notes/status'
    messageName = 'status'

    data() {
        return {
            ...super.data(),
            style: this.style,
            statusText: this.statusText,
        };
    }

    init() {
        if (this.getUser().isAdmin()) {
            this.isRemovable = true;
        }

        super.init();
    }

    setup() {
        let data = this.model.get('data');

        let field = data.field;
        let value = data.value;

        this.style = data.style || 'default';
        this.statusText = this.getLanguage().translateOption(value, field, this.model.get('parentType'));

        this.messageData['field'] = this.translate(field, 'fields', this.model.get('parentType')).toLowerCase();

        this.createMessage();
    }
}

export default StatusNoteStreamView;
