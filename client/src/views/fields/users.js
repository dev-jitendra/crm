

import LinkMultipleFieldView from 'views/fields/link-multiple';

class UsersFieldView extends LinkMultipleFieldView {

    init() {
        this.assignmentPermission = this.getAcl().getPermissionLevel('assignmentPermission');

        if (this.assignmentPermission === 'no') {
            this.readOnly = true;
        }

        super.init();
    }

    getSelectBoolFilterList() {
        if (this.assignmentPermission === 'team' || this.assignmentPermission === 'no') {
            return ['onlyMyTeam'];
        }
    }

    getSelectPrimaryFilterName() {
        return 'active';
    }
}

export default UsersFieldView;


