

import ModalView from 'views/modal';
import Model from 'model';

class EditDashboardModalView extends ModalView {

    template = 'modals/edit-dashboard'

    className = 'dialog dialog-record'
    cssName = 'edit-dashboard'

    data() {
        return {
            hasLocked: this.hasLocked,
        };
    }

    events = {
        
        'click button.add': function (e) {
            const name = $(e.currentTarget).data('name');

            this.getParentDashboardView().addDashlet(name);
            this.close();
        },
    }

    shortcutKeys = {
        'Control+Enter': 'save',
    }

    
    getParentDashboardView() {
        return this.getParentView();
    }

    setup() {
        this.buttonList = [
            {
                name: 'save',
                label: this.options.fromDashboard ? 'Save': 'Apply',
                style: 'primary',
                title: 'Ctrl+Enter',
            },
            {
                name: 'cancel',
                label: 'Cancel',
                title: 'Esc',
            }
        ];

        const dashboardLayout = this.options.dashboardLayout || [];

        const dashboardTabList = [];

        dashboardLayout.forEach(item => {
            if (item.name) {
                dashboardTabList.push(item.name);
            }
        });

        const model = this.model = new Model({}, {entityType: 'Preferences'});

        model.set('dashboardTabList', dashboardTabList);

        this.hasLocked = 'dashboardLocked' in this.options;

        if (this.hasLocked) {
            model.set('dashboardLocked', this.options.dashboardLocked || false);
        }

        this.createView('dashboardTabList', 'views/preferences/fields/dashboard-tab-list', {
            selector: '.field[data-name="dashboardTabList"]',
            defs: {
                name: 'dashboardTabList',
                params: {
                    required: true,
                    noEmptyString: true,
                }
            },
            mode: 'edit',
            model: model,
        });

        if (this.hasLocked) {
            this.createView('dashboardLocked', 'views/fields/bool', {
                selector: '.field[data-name="dashboardLocked"]',
                mode: 'edit',
                model: model,
                defs: {
                    name: 'dashboardLocked',
                },
            })
        }

        this.headerText = this.translate('Edit Dashboard');

        this.dashboardLayout = this.options.dashboardLayout;
    }

    
    getFieldView(field) {
        return this.getView(field);
    }

    actionSave() {
        const dashboardTabListView = this.getFieldView('dashboardTabList');

        dashboardTabListView.fetchToModel();

        if (this.hasLocked) {
            const dashboardLockedView = this.getFieldView('dashboardLocked');

            dashboardLockedView.fetchToModel();
        }

        if (dashboardTabListView.validate()) {
            return;
        }

        const attributes = {};

        attributes.dashboardTabList = this.model.get('dashboardTabList');

        if (this.hasLocked) {
            attributes.dashboardLocked = this.model.get('dashboardLocked');
        }

        const names = this.model.get('translatedOptions');

        const renameMap = {};

        for (const name in names) {
            if (name !== names[name]) {
                renameMap[name] = names[name];
            }
        }

        attributes.renameMap = renameMap;

        this.trigger('after:save', attributes);

        this.dialog.close();
    }
}

export default EditDashboardModalView;
