



import View from 'view'


class DashletView extends View {

    
    template = 'dashlet'

    
    name

    
    id

    
    optionsView = null

    
    data() {
        return {
            name: this.name,
            id: this.id,
            title: this.getTitle(),
            actionList: (this.getBodyView() || {}).actionList || [],
            buttonList: (this.getBodyView() || {}).buttonList || [],
            noPadding: (this.getBodyView() || {}).noPadding,
        };
    }

    
    events = {
        
        'click .action': function (e) {
            const isHandled = Espo.Utils.handleAction(this, e.originalEvent, e.currentTarget);

            if (isHandled) {
                return;
            }

            this.getBodyView().handleAction(e.originalEvent, e.currentTarget);
        },
        
        'mousedown .panel-heading .dropdown-menu': function (e) {
            
            e.stopPropagation();
        },
        
        'shown.bs.dropdown .panel-heading .btn-group': function (e) {
            this.controlDropdownShown($(e.currentTarget).parent());
        },
        
        'hide.bs.dropdown .panel-heading .btn-group': function () {
            this.controlDropdownHide();
        },
    }

    controlDropdownShown($dropdownContainer) {
        const $panel = this.$el.children().first();

        const dropdownBottom = $dropdownContainer.find('.dropdown-menu')
            .get(0).getBoundingClientRect().bottom;

        const panelBottom = $panel.get(0).getBoundingClientRect().bottom;

        if (dropdownBottom < panelBottom) {
            return;
        }

        $panel.addClass('has-dropdown-opened');
    }

    controlDropdownHide() {
        this.$el.children().first().removeClass('has-dropdown-opened');
    }

    
    setup() {
        this.name = this.options.name;
        this.id = this.options.id;

        this.on('resize', () => {
            const bodyView = this.getView('body');

            if (!bodyView) {
                return;
            }

            bodyView.trigger('resize');
        });

        const viewName = this.getMetadata().get(['dashlets', this.name, 'view']) ||
            'views/dashlets/' + Espo.Utils.camelCaseToHyphen(this.name);

        this.createView('body', viewName, {
            selector: '.dashlet-body',
            id: this.id,
            name: this.name,
            readOnly: this.options.readOnly,
            locked: this.options.locked,
        });
    }

    
    refresh() {
        this.getBodyView().actionRefresh();
    }

    actionRefresh() {
        this.refresh();
    }

    actionOptions() {
        const optionsView =
            this.getMetadata().get(['dashlets', this.name, 'options', 'view']) ||
            this.optionsView ||
            'views/dashlets/options/base';

        Espo.Ui.notify(' ... ');

        this.createView('options', optionsView, {
            name: this.name,
            optionsData: this.getOptionsData(),
            fields: this.getBodyView().optionsFields,
        }, view => {
            view.render();

            Espo.Ui.notify(false);

            this.listenToOnce(view, 'save', (attributes) => {
                const id = this.id;

                Espo.Ui.notify(this.translate('saving', 'messages'));

                this.getPreferences().once('sync', () => {
                    this.getPreferences().trigger('update');

                    Espo.Ui.notify(false);

                    view.close();
                    this.trigger('change');
                });

                const o = this.getPreferences().get('dashletsOptions') || {};

                o[id] = attributes;

                this.getPreferences().save({dashletsOptions: o}, {patch: true});
            });
        });
    }

    
    getOptionsData() {
        return this.getBodyView().optionsData;
    }

    
    getOption(key) {
        return this.getBodyView().getOption(key);
    }

    
    getTitle() {
        return this.getBodyView().getTitle();
    }

    
    getBodyView() {
        return this.getView('body');
    }

    
    actionRemove() {
        this.confirm(this.translate('confirmation', 'messages'), () => {
            this.trigger('remove-dashlet');
            this.$el.remove();
            this.remove();
        });
    }
}

export default DashletView;
