

import DefaultRowActionsView from 'views/record/row-actions/default';

class EmptyRowActionsView extends DefaultRowActionsView {

    getActionList() {
        return [];
    }
}

export default EmptyRowActionsView;
