



import View from 'view';


class DetailMiddleRecordView extends View {

    init() {
        this.recordHelper = this.options.recordHelper;
        this.scope = this.model.entityType;
    }

    data() {
        return {
            hiddenPanels: this.recordHelper.getHiddenPanels(),
            hiddenFields: this.recordHelper.getHiddenFields(),
        };
    }

    
    showPanel(name) {
        if (this.recordHelper.getPanelStateParam(name, 'hiddenLocked')) {
            return;
        }

        this.showPanelInternal(name);

        this.recordHelper.setPanelStateParam(name, 'hidden', false);
    }

    
    showPanelInternal(name) {
        if (this.isRendered()) {
            this.$el.find('.panel[data-name="'+name+'"]').removeClass('hidden');
        }

        const wasShown = !this.recordHelper.getPanelStateParam(name, 'hidden');

        if (
            !wasShown &&
            this.options.panelFieldListMap &&
            this.options.panelFieldListMap[name]
        ) {
            this.options.panelFieldListMap[name].forEach(field => {
                const view = this.getFieldView(field);

                if (!view) {
                    return;
                }

                view.reRender();
            });
        }
    }

    
    hidePanel(name) {
        this.hidePanelInternal(name);

        this.recordHelper.setPanelStateParam(name, 'hidden', true);
    }

    
    hidePanelInternal(name) {
        if (this.isRendered()) {
            this.$el.find('.panel[data-name="'+name+'"]').addClass('hidden');
        }
    }

    
    hideField(name) {
        this.recordHelper.setFieldStateParam(name, 'hidden', true);

        const processHtml = () => {
            const fieldView = this.getFieldView(name);

            if (fieldView) {
                const $field = fieldView.$el;
                const $cell = $field.closest('.cell[data-name="' + name + '"]');
                const $label = $cell.find('label.control-label[data-name="' + name + '"]');

                $field.addClass('hidden');
                $label.addClass('hidden');
                $cell.addClass('hidden-cell');
            }
            else {
                this.$el.find('.cell[data-name="' + name + '"]').addClass('hidden-cell');
                this.$el.find('.field[data-name="' + name + '"]').addClass('hidden');
                this.$el.find('label.control-label[data-name="' + name + '"]').addClass('hidden');
            }
        };

        if (this.isRendered()) {
            processHtml();
        }
        else {
            this.once('after:render', () => {
                processHtml();
            });
        }

        const view = this.getFieldView(name);

        if (view) {
            view.setDisabled();
        }
    }

    
    showField(name) {
        if (this.recordHelper.getFieldStateParam(name, 'hiddenLocked')) {
            return;
        }

        this.recordHelper.setFieldStateParam(name, 'hidden', false);

        const processHtml = () => {
            const fieldView = this.getFieldView(name);

            if (fieldView) {
                const $field = fieldView.$el;
                const $cell = $field.closest('.cell[data-name="' + name + '"]');
                const $label = $cell.find('label.control-label[data-name="' + name + '"]');

                $field.removeClass('hidden');
                $label.removeClass('hidden');
                $cell.removeClass('hidden-cell');
            }
            else {
                this.$el.find('.cell[data-name="' + name + '"]').removeClass('hidden-cell');
                this.$el.find('.field[data-name="' + name + '"]').removeClass('hidden');
                this.$el.find('label.control-label[data-name="' + name + '"]').removeClass('hidden');
            }
        };

        if (this.isRendered()) {
            processHtml();
        }
        else {
            this.once('after:render', () => {
                processHtml();
            });
        }

        const view = this.getFieldView(name);

        if (view) {
            if (!view.disabledLocked) {
                view.setNotDisabled();
            }
        }
    }

    
    getFields() {
        return this.getFieldViews();
    }

    
    getFieldViews() {
        const fieldViews = {};

        for (const viewKey in this.nestedViews) {
            
            const name = this.nestedViews[viewKey].name;

            fieldViews[name] = this.nestedViews[viewKey];
        }

        return fieldViews;
    }

    
    getFieldView(name) {
        return (this.getFieldViews() || {})[name];
    }

    
    getView(name) {
        let view = super.getView(name);

        if (!view) {
            view = this.getFieldView(name);
        }

        return view;
    }
}


export default DetailMiddleRecordView;
