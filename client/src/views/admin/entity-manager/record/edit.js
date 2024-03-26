

define('views/admin/entity-manager/record/edit', ['views/record/edit'], function (Dep) {

    return Dep.extend({

        bottomView: null,
        sideView: null,

        dropdownItemList: [],

        accessControlDisabled: true,
        saveAndContinueEditingAction: false,
        saveAndNewAction: false,

        shortcutKeys: {
            'Control+Enter': 'save',
            'Control+KeyS': 'save',
        },

        setup: function () {
            this.isCreate = this.options.isNew;

            this.scope = 'EntityManager';

            this.subjectEntityType = this.options.subjectEntityType;

            if (!this.isCreate) {
                this.buttonList = [
                    {
                        name: 'save',
                        style: 'danger',
                        label: 'Save',
                    },
                    {
                        name: 'cancel',
                        label: 'Cancel',
                    },
                ];
            }
            else {
                this.buttonList = [
                    {
                        name: 'save',
                        style: 'danger',
                        label: 'Create',
                    },
                    {
                        name: 'cancel',
                        label: 'Cancel',
                    },
                ];
            }

            if (!this.isCreate && !this.options.isCustom) {
                this.buttonList.push({
                    name: 'resetToDefault',
                    text: this.translate('Reset to Default', 'labels', 'Admin'),
                });
            }

            Dep.prototype.setup.call(this);

            if (this.isCreate) {
                this.hideField('sortBy');
                this.hideField('sortDirection');
                this.hideField('textFilterFields');
                this.hideField('statusField');
                this.hideField('fullTextSearch');
                this.hideField('countDisabled');
                this.hideField('kanbanViewMode');
                this.hideField('kanbanStatusIgnoreList');
                this.hideField('disabled');
            }

            if (!this.options.hasColorField) {
                this.hideField('color');
            }

            if (!this.options.hasStreamField) {
                this.hideField('stream');
            }

            if (!this.isCreate) {
                this.manageKanbanFields({});

                this.listenTo(this.model, 'change:statusField', (m, v, o) => {
                    this.manageKanbanFields(o);
                });

                this.manageKanbanViewModeField();

                this.listenTo(this.model, 'change:kanbanViewMode', () => {
                    this.manageKanbanViewModeField();
                });
            }
        },

        actionSave: function () {
            this.trigger('save');
        },

        actionCancel: function () {
            this.trigger('cancel');
        },

        actionResetToDefault: function () {
            this.trigger('reset-to-default');
        },

        manageKanbanViewModeField: function () {
            if (this.model.get('kanbanViewMode')) {
                this.showField('kanbanStatusIgnoreList');
            } else {
                this.hideField('kanbanStatusIgnoreList');
            }
        },

        manageKanbanFields: function (o) {
            if (o.ui) {
                this.model.set('kanbanStatusIgnoreList', []);
            }

            if (this.model.get('statusField')) {
                this.setKanbanStatusIgnoreListOptions();

                this.showField('kanbanViewMode');

                if (this.model.get('kanbanViewMode')) {
                    this.showField('kanbanStatusIgnoreList');
                } else {
                    this.hideField('kanbanStatusIgnoreList');
                }
            }
            else {
                this.hideField('kanbanViewMode');
                this.hideField('kanbanStatusIgnoreList');
            }
        },

        setKanbanStatusIgnoreListOptions: function () {
            let statusField = this.model.get('statusField');

            var optionList = this.getMetadata()
                .get(['entityDefs', this.subjectEntityType, 'fields', statusField, 'options']) || [];

            this.setFieldOptionList('kanbanStatusIgnoreList', optionList);

            let fieldView = this.getFieldView('kanbanStatusIgnoreList');

            if (!fieldView) {
                this.once('after:render', () => this.setKanbanStatusIgnoreListTranslation());

                return;
            }

            this.setKanbanStatusIgnoreListTranslation();
        },

        setKanbanStatusIgnoreListTranslation: function () {
            var fieldView = this.getFieldView('kanbanStatusIgnoreList');

            var statusField = this.model.get('statusField');

            var translation = this.getMetadata()
                .get(['entityDefs', this.subjectEntityType, 'fields', statusField, 'translation']) ||
                this.subjectEntityType + '.options.' + statusField;

            fieldView.params.translation = translation;
            fieldView.setupTranslation();
        },
    });
});
