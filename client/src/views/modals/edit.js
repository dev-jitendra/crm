



import ModalView from 'views/modal';
import Backbone from 'backbone';


class EditModalView extends ModalView {

    template = 'modals/edit'

    cssName = 'edit-modal'
    saveDisabled = false
    fullFormDisabled = false
    editView = null
    escapeDisabled = true
    className = 'dialog dialog-record'
    sideDisabled = false
    bottomDisabled = false

    shortcutKeys = {
        
        'Control+Enter': function (e) {
            if (this.saveDisabled) {
                return;
            }

            if (this.buttonList.findIndex(item => item.name === 'save' && !item.hidden) === -1) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            this.actionSave();
        },
        
        'Control+KeyS': function (e) {
            if (this.saveDisabled) {
                return;
            }

            if (this.buttonList.findIndex(item => item.name === 'save' && !item.hidden) === -1) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            this.actionSaveAndContinueEditing();
        },
        
        'Escape': function (e) {
            if (this.saveDisabled) {
                return;
            }

            e.stopPropagation();
            e.preventDefault();

            const focusedFieldView = this.getRecordView().getFocusedFieldView();

            if (focusedFieldView) {
                this.model.set(focusedFieldView.fetch(), {skipReRender: true});
            }

            if (this.getRecordView().isChanged) {
                this.confirm(this.translate('confirmLeaveOutMessage', 'messages'))
                    .then(() => this.actionClose());

                return;
            }

            this.actionClose();
        },
        
        'Control+Backslash': function (e) {
            this.getRecordView().handleShortcutKeyControlBackslash(e);
        },
    }

    setup() {
        this.buttonList = [];

        if ('saveDisabled' in this.options) {
            this.saveDisabled = this.options.saveDisabled;
        }

        if (!this.saveDisabled) {
            this.buttonList.push({
                name: 'save',
                label: 'Save',
                style: 'primary',
                title: 'Ctrl+Enter',
            });
        }

        this.fullFormDisabled = this.options.fullFormDisabled || this.fullFormDisabled;

        this.layoutName = this.options.layoutName || this.layoutName;

        if (!this.fullFormDisabled) {
            this.buttonList.push({
                name: 'fullForm',
                label: 'Full Form',
            });
        }

        this.buttonList.push({
            name: 'cancel',
            label: 'Cancel',
            title: 'Esc',
        });

        this.scope = this.scope || this.options.scope || this.options.entityType;
        this.entityType = this.options.entityType || this.scope;
        this.id = this.options.id;

        this.headerHtml = this.composeHeaderHtml();

        this.sourceModel = this.model;

        this.waitForView('edit');

        this.getModelFactory().create(this.entityType, (model) => {
            if (this.id) {
                if (this.sourceModel) {
                    model = this.model = this.sourceModel.clone();
                }
                else {
                    this.model = model;

                    model.id = this.id;
                }

                model
                    .fetch()
                    .then(() => {
                        this.createRecordView(model);
                    });

                return;
            }

            this.model = model;

            if (this.options.relate) {
                model.setRelate(this.options.relate);
            }

            if (this.options.attributes) {
                model.set(this.options.attributes);
            }

            this.createRecordView(model);
        });
    }

    
    createRecordView(model, callback) {
        const viewName =
            this.editView ||
            this.getMetadata().get(['clientDefs', model.entityType, 'recordViews', 'editSmall']) ||
            this.getMetadata().get(['clientDefs', model.entityType, 'recordViews', 'editQuick']) ||
            'views/record/edit-small';

        const options = {
            model: model,
            fullSelector: this.containerSelector + ' .edit-container',
            type: 'editSmall',
            layoutName: this.layoutName || 'detailSmall',
            buttonsDisabled: true,
            sideDisabled: this.sideDisabled,
            bottomDisabled: this.bottomDisabled,
            focusForCreate: this.options.focusForCreate,
            exit: () => {},
        };

        this.handleRecordViewOptions(options);

        this.createView('edit', viewName, options, callback)
            .then(view => {
                this.listenTo(view, 'before:save', () => this.trigger('before:save', model));

                if (this.options.relate && ('link' in this.options.relate)) {
                    const link = this.options.relate.link;

                    if (
                        model.hasField(link) &&
                        ['link'].includes(model.getFieldType(link))
                    ) {
                        view.setFieldReadOnly(link);
                    }
                }
            });
    }

    handleRecordViewOptions(options) {}

    
    getRecordView() {
        return this.getView('edit');
    }

    onBackdropClick() {
        if (this.getRecordView().isChanged) {
            return;
        }

        this.close();
    }

    
    composeHeaderHtml() {
        let html;

        if (!this.id) {
            html = $('<span>')
                .text(this.getLanguage().translate('Create ' + this.scope, 'labels', this.scope))
                .get(0).outerHTML;
        }
        else {
            const text = this.getLanguage().translate('Edit') + ' · ' +
                this.getLanguage().translate(this.scope, 'scopeNames');

            html = $('<span>')
                .text(text)
                .get(0).outerHTML;
        }

        if (!this.fullFormDisabled) {
            const url = this.id ?
                '#' + this.scope + '/edit/' + this.id :
                '#' + this.scope + '/create';

            html =
                $('<a>')
                    .attr('href', url)
                    .addClass('action')
                    .attr('title', this.translate('Full Form'))
                    .attr('data-action', 'fullForm')
                    .append(html)
                    .get(0).outerHTML;
        }

        html = this.getHelper().getScopeColorIconHtml(this.scope) + html;

        return html;
    }

    actionSave(data) {
        data = data || {};

        const editView = this.getRecordView();

        const model = editView.model;

        const $buttons = this.dialog.$el.find('.modal-footer button');

        $buttons.addClass('disabled').attr('disabled', 'disabled');

        editView
            .save()
            .then(() => {
                const wasNew = !this.id;

                if (wasNew) {
                    this.id = model.id;
                }

                this.trigger('after:save', model, {bypassClose: data.bypassClose});

                if (!data.bypassClose) {
                    this.dialog.close();

                    if (wasNew) {
                        const url = '#' + this.scope + '/view/' + model.id;
                        const name = model.get('name') || this.model.id;

                        const msg = this.translate('Created') + '\n' +
                            `[${name}](${url})`;

                        Espo.Ui.notify(msg, 'success', 4000, {suppress: true});
                    }

                    return;
                }

                this.$el.find('.modal-header .modal-title-text')
                    .html(this.composeHeaderHtml());

                $buttons.removeClass('disabled').removeAttr('disabled');
            })
            .catch(() => {
                $buttons.removeClass('disabled').removeAttr('disabled');
            })
    }

    actionSaveAndContinueEditing() {
        this.actionSave({bypassClose: true});
    }

    
    actionFullForm() {
        let url;
        const router = this.getRouter();

        let attributes;
        let model;
        let options;

        if (!this.id) {
            url = '#' + this.scope + '/create';

            attributes = this.getRecordView().fetch();
            model = this.getRecordView().model;

            attributes = {...attributes, ...model.getClonedAttributes()};

            options = {
                attributes: attributes,
                relate: this.options.relate,
                returnUrl: this.options.returnUrl || Backbone.history.fragment,
                returnDispatchParams: this.options.returnDispatchParams || null,
            };

            if (this.options.rootUrl) {
                options.rootUrl = this.options.rootUrl;
            }

            setTimeout(() => {
                router.dispatch(this.scope, 'create', options);
                router.navigate(url, {trigger: false});
            }, 10);
        }
        else {
            url = '#' + this.scope + '/edit/' + this.id;

            attributes = this.getRecordView().fetch();
            model = this.getRecordView().model;

            attributes = {...attributes, ...model.getClonedAttributes()};

            options = {
                attributes: attributes,
                returnUrl: this.options.returnUrl || Backbone.history.fragment,
                returnDispatchParams: this.options.returnDispatchParams || null,
                model: this.sourceModel,
                id: this.id,
            };

            if (this.options.rootUrl) {
                options.rootUrl = this.options.rootUrl;
            }

            setTimeout(() => {
                router.dispatch(this.scope, 'edit', options);
                router.navigate(url, {trigger: false});
            }, 10);
        }

        this.trigger('leave');
        this.dialog.close();
    }
}

export default EditModalView;
