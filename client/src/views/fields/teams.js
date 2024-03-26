

import LinkMultipleFieldView from 'views/fields/link-multiple';

class TeamsFieldView extends LinkMultipleFieldView {

    init() {
        this.assignmentPermission = this.getAcl().getPermissionLevel('assignmentPermission');

        super.init();
    }

    getSelectBoolFilterList() {
        if (this.assignmentPermission === 'team' || this.assignmentPermission === 'no') {
            return ['onlyMy'];
        }
    }
}

export default TeamsFieldView;
