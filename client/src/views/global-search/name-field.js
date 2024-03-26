

import BaseFieldView from 'views/fields/base';

class GlobalSearchNameFieldView extends BaseFieldView {

    listTemplate = 'global-search/name-field'

    data() {
        return {
            scope: this.model.get('_scope'),
            name: this.model.get('name') || this.translate('None'),
            id: this.model.id,
            iconHtml: this.getHelper().getScopeColorIconHtml(this.model.get('_scope')),
        };
    }
}

export default GlobalSearchNameFieldView;
