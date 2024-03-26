



import View from 'view';

class LayoutBaseView extends View {

    
    scope
    
    type

    events = {
        
        'click button[data-action="save"]': function () {
            this.actionSave();
        },
        
        'click button[data-action="cancel"]': function () {
            this.cancel();
        },
        
        'click button[data-action="resetToDefault"]': function () {
            this.confirm(this.translate('confirmation', 'messages'), () => {
                this.resetToDefault();
            });
        },
        
        'click button[data-action="remove"]': function () {
            this.actionDelete();
        },
    }

    buttonList = [
        {
            name: 'save',
            label: 'Save',
            style: 'primary',
        },
        {
            name: 'cancel',
            label: 'Cancel',
        },
    ]

    
    dataAttributes = null
    dataAttributesDefs = null
    dataAttributesDynamicLogicDefs = null

    setup() {
        this.buttonList = _.clone(this.buttonList);
        this.events = _.clone(this.events);
        this.scope = this.options.scope;
        this.type = this.options.type;
        this.realType = this.options.realType;
        this.setId = this.options.setId;
        this.em = this.options.em;

        const defs = this.getMetadata()
            .get(['clientDefs', this.scope, 'additionalLayouts', this.type]) ?? {};

        this.typeDefs = defs;

        this.dataAttributeList = Espo.Utils.clone(defs.dataAttributeList || this.dataAttributeList);

        this.isCustom = !!defs.isCustom;

        if (this.isCustom && this.em) {
            this.buttonList.push({
                name: 'remove',
                label: 'Remove',
            })
        }

        if (!this.isCustom) {
            this.buttonList.push({
                name: 'resetToDefault',
                label: 'Reset to Default',
            });
        }
    }

    actionSave() {
        this.disableButtons();
        Espo.Ui.notify(this.translate('saving', 'messages'));

        this.save(this.enableButtons.bind(this));
    }

    disableButtons() {
        this.$el.find('.button-container button').attr('disabled', 'disabled');
    }

    enableButtons() {
        this.$el.find('.button-container button').removeAttr('disabled');
    }

    setConfirmLeaveOut(value) {
        this.getRouter().confirmLeaveOut = value;
    }

    setIsChanged() {
        this.isChanged = true;
        this.setConfirmLeaveOut(true);
    }

    setIsNotChanged() {
        this.isChanged = false;
        this.setConfirmLeaveOut(false);
    }

    save(callback) {
        const layout = this.fetch();

        if (!this.validate(layout)) {
            this.enableButtons();

            return false;
        }

        this.getHelper()
            .layoutManager
            .set(this.scope, this.type, layout, () => {
                Espo.Ui.success(this.translate('Saved'));

                this.setIsNotChanged();

                if (typeof callback === 'function') {
                    callback();
                }

                this.getHelper().broadcastChannel.postMessage('update:layout');
            }, this.setId)
            .catch(() => this.enableButtons());
    }

    resetToDefault() {
        this.getHelper().layoutManager.resetToDefault(this.scope, this.type, () => {
            this.loadLayout(() => {
                this.setIsNotChanged();

                this.prepareLayout().then(() => this.reRender());
            });

        }, this.options.setId);
    }

    prepareLayout() {
        return Promise.resolve();
    }

    reset() {
        this.render();
    }

    fetch() {}

    unescape(string) {
        if (string === null) {
            return '';
        }

        const map = {
            '&amp;': '&',
            '&lt;': '<',
            '&gt;': '>',
            '&quot;': '"',
            '&#x27;': "'",
        };

        const reg = new RegExp('(' + _.keys(map).join('|') + ')', 'g');

        return ('' + string).replace(reg, match => {
            return map[match];
        });
    }

    getEditAttributesModalViewOptions(attributes) {
        return {
            name: attributes.name,
            scope: this.scope,
            attributeList: this.dataAttributeList,
            attributeDefs: this.dataAttributesDefs,
            dynamicLogicDefs: this.dataAttributesDynamicLogicDefs,
            attributes: attributes,
            languageCategory: this.languageCategory,
            headerText: ' ',
        };
    }

    openEditDialog(attributes) {
        const name = attributes.name;

        const viewOptions = this.getEditAttributesModalViewOptions(attributes);

        this.createView('editModal', 'views/admin/layouts/modals/edit-attributes', viewOptions, view => {
            view.render();

            this.listenToOnce(view, 'after:save', attributes => {
                this.trigger('update-item', name, attributes);

                const $li = $("#layout ul > li[data-name='" + name + "']");

                for (const key in attributes) {
                    $li.attr('data-' + key, attributes[key]);
                    $li.data(key, attributes[key]);
                    $li.find('.' + key + '-value').text(attributes[key]);
                }

                view.close();

                this.setIsChanged();
            });
        });
    }

    cancel() {
        this.loadLayout(() => {
            this.setIsNotChanged();

            if (this.em) {
                this.trigger('cancel');

                return;
            }

            this.prepareLayout().then(() => this.reRender());
        });
    }

    
    validate(layout) {
        return true;
    }

    actionDelete() {
        this.confirm(this.translate('confirmation', 'messages'))
            .then(() => {
                this.disableButtons();

                Espo.Ui.notify(' ... ');

                Espo.Ajax
                    .postRequest('Layout/action/delete', {
                        scope: this.scope,
                        name: this.type,
                    })
                    .then(() => {
                        Espo.Ui.success(this.translate('Removed'), {suppress: true});

                        this.trigger('after-delete');
                    })
                    .catch(() => {
                        this.enableButtons();
                    });
            });
    }
}

export default LayoutBaseView;
