

import EditView from 'views/edit';

class SettingsEditView extends EditView {

    scope = 'Settings'

    setupHeader() {
        this.createView('header', this.headerView, {
            model: this.model,
            fullSelector: '#main > .header',
            template: this.options.headerTemplate,
            label: this.options.label,
        });
    }
}

export default SettingsEditView;
