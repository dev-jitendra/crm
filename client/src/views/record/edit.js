



import DetailRecordView from 'views/record/detail';


class EditRecordView extends DetailRecordView {

    
    template = 'record/edit'

    
    type = 'edit'
    
    fieldsMode = 'edit'
    
    mode = 'edit'
    
    buttonList = [
        {
            name: 'save',
            label: 'Save',
            style: 'primary',
            title: 'Ctrl+Enter',
        },
        {
            name: 'cancel',
            label: 'Cancel',
            title: 'Esc',
        }
    ]
    
    dropdownItemList = []
    
    sideView = 'views/record/edit-side'
    
    bottomView = 'views/record/edit-bottom'
    
    duplicateAction = false
    
    saveAndContinueEditingAction = true
    
    saveAndNewAction = true
    
    setupHandlerType = 'record/edit'

    
    constructor(options) {
        super(options);
    }

    
    actionSave(data) {
        data = data || {};

        const isNew = this.isNew;

        return this.save(data.options)
            .then(() => {
                if (this.options.duplicateSourceId) {
                    this.returnUrl = null;
                }

                this.exit(isNew ? 'create' : 'save');
            })
            .catch(reason => Promise.reject(reason));
    }

    
    actionCancel() {
        this.cancel();
    }

    
    cancel() {
        if (this.isChanged) {
            this.resetModelChanges();
        }

        this.setIsNotChanged();
        this.exit('cancel');
    }

    
    setupBeforeFinal() {
        if (this.model.isNew()) {
            this.populateDefaults();
        }

        super.setupBeforeFinal();

        if (this.model.isNew()) {
            this.once('after:render', () => {
                this.model.set(this.fetch(), {silent: true});
            })
        }

        if (this.options.focusForCreate) {
            this.once('after:render', () => {
                if (this.$el.closest('.modal').length) {
                    setTimeout(() => this.focusForCreate(), 50);

                    return;
                }

                this.focusForCreate();
            });
        }
    }

    
    setupActionItems() {
        super.setupActionItems();

        if (
            this.saveAndContinueEditingAction &&
            this.getAcl().checkScope(this.entityType, 'edit')
        ) {
            this.dropdownItemList.push({
                name: 'saveAndContinueEditing',
                label: 'Save & Continue Editing',
                title: 'Ctrl+S',
            });
        }

        if (
            this.isNew &&
            this.saveAndNewAction &&
            this.getAcl().checkScope(this.entityType, 'create')
        ) {
            this.dropdownItemList.push({
                name: 'saveAndNew',
                label: 'Save & New',
                title: 'Ctrl+Alt+Enter',
            });
        }
    }

    
    actionSaveAndNew(data) {
        data = data || {};

        const proceedCallback = () => {
            Espo.Ui.success(this.translate('Created'));

            this.getRouter().dispatch(this.scope, 'create', {
                rootUrl: this.options.rootUrl,
                focusForCreate: !!data.focusForCreate,
            });

            this.getRouter().navigate('#' + this.scope + '/create', {trigger: false});
        };

        this.save(data.options)
            .then(proceedCallback)
            .catch(() => {});

        if (this.lastSaveCancelReason === 'notModified') {
             proceedCallback();
        }
    }

    
    handleShortcutKeyEscape(e) {
        if (this.buttonsDisabled) {
            return;
        }

        if (this.buttonList.findIndex(item => item.name === 'cancel' && !item.hidden) === -1) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        const focusedFieldView = this.getFocusedFieldView();

        if (focusedFieldView) {
            this.model.set(focusedFieldView.fetch());
        }

        if (this.isChanged) {
            this.confirm(this.translate('confirmLeaveOutMessage', 'messages'))
                .then(() => this.actionCancel());

            return;
        }

        this.actionCancel();
    }

    
    handleShortcutKeyCtrlAltEnter(e) {
        if (this.buttonsDisabled) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        if (!this.saveAndNewAction) {
            return;
        }

        if (!this.hasAvailableActionItem('saveAndNew')) {
            return;
        }

        this.actionSaveAndNew({focusForCreate: true});
    }
}

export default EditRecordView;
