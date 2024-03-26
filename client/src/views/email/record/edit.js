



import EditRecordView from 'views/record/edit';
import EmailDetailRecordView from 'views/email/record/detail';

class EmailEditRecordView extends EditRecordView {

    shortcutKeyCtrlEnterAction = 'send'

    init() {
        super.init();

        EmailDetailRecordView.prototype.layoutNameConfigure.call(this);
    }

    setup() {
        super.setup();

        if (['Archived', 'Sent'].includes(this.model.get('status'))) {
            this.shortcutKeyCtrlEnterAction = 'save';
        }

        this.addButton({
            name: 'send',
            label: 'Send',
            style: 'primary',
            title: 'Ctrl+Enter',
        }, true);

        this.addButton({
            name: 'saveDraft',
            label: 'Save Draft',
            title: 'Ctrl+S',
        }, true);

        this.controlSendButton();

        if (this.model.get('status') === 'Draft') {
            this.setFieldReadOnly('dateSent');

            
            this.hideField('selectTemplate');
        }

        this.handleAttachmentField();
        this.handleCcField();
        this.handleBccField();

        this.listenTo(this.model, 'change:attachmentsIds', () => this.handleAttachmentField());
        this.listenTo(this.model, 'change:cc', () => this.handleCcField());
        this.listenTo(this.model, 'change:bcc', () => this.handleBccField());
    }

    handleAttachmentField() {
        if (
            (this.model.get('attachmentsIds') || []).length === 0 &&
            !this.isNew &&
            this.model.get('status') !== 'Draft'
        ) {
            this.hideField('attachments');

            return;
        }

        this.showField('attachments');
    }

    handleCcField() {
        if (!this.model.get('cc') && this.model.get('status') !== 'Draft') {
            this.hideField('cc');
        } else {
            this.showField('cc');
        }
    }

    handleBccField() {
        if (!this.model.get('bcc') && this.model.get('status') !== 'Draft') {
            this.hideField('bcc');
        } else {
            this.showField('bcc');
        }
    }

    controlSendButton()  {
        const status = this.model.get('status');

        if (status === 'Draft') {
            this.showActionItem('send');
            this.showActionItem('saveDraft');
            this.hideActionItem('save');
            this.hideActionItem('saveAndContinueEditing');

            return;
        }

        this.hideActionItem('send');
        this.hideActionItem('saveDraft');
        this.showActionItem('save');
        this.showActionItem('saveAndContinueEditing');
    }

    
    actionSaveDraft() {
        this.actionSaveAndContinueEditing();
    }

    
    actionSend() {
        EmailDetailRecordView.prototype.send.call(this)
            .then(() => this.exit())
            .catch(() => {});
    }

    
    handleShortcutKeyCtrlS(e) {
        if (this.inlineEditModeIsOn || this.buttonsDisabled) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        if (this.mode !== this.MODE_EDIT) {
            return;
        }

        if (!this.saveAndContinueEditingAction) {
            return;
        }

        if (
            !this.hasAvailableActionItem('saveDraft') &&
            !this.hasAvailableActionItem('saveAndContinueEditing')
        ) {
            return;
        }

        this.actionSaveAndContinueEditing();
    }
}

export default EmailEditRecordView;
