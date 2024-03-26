

import ArrayFieldAddModalView from 'views/modals/array-field-add';

class TabListFieldAddSettingsModalView extends ArrayFieldAddModalView {

    setup() {
        super.setup();

        if (!this.options.noGroups) {
            this.buttonList.push({
                name: 'addGroup',
                text: this.translate('Group Tab', 'labels', 'Settings'),
            });
        }

        this.buttonList.push({
            name: 'addDivider',
            text: this.translate('Divider', 'labels', 'Settings'),
        });
    }

    actionAddGroup() {
        this.trigger('add', {
            type: 'group',
            text: this.translate('Group Tab', 'labels', 'Settings'),
            iconClass: null,
            color: null,
        });
    }

    actionAddDivider() {
        this.trigger('add', {
            type: 'divider',
            text: null,
        });
    }
}


export default TabListFieldAddSettingsModalView;
