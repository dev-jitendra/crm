

import UserWithAvatarFieldView from 'views/fields/user-with-avatar';

class AssignedUserFieldView extends UserWithAvatarFieldView {

    init() {
        this.assignmentPermission = this.getAcl().getPermissionLevel('assignmentPermission');

        if (this.assignmentPermission === 'no') {
            this.setReadOnly(true);
        }

        super.init();
    }

    getSelectBoolFilterList() {
        if (this.assignmentPermission === 'team') {
            return ['onlyMyTeam'];
        }
    }

    getSelectPrimaryFilterName() {
        return 'active';
    }

    getEmptyAutocompleteResult() {
        return {
            list: [
                {
                    id: this.getUser().id,
                    name: this.getUser().get('name'),
                }
            ]
        };
    }
}

export default AssignedUserFieldView;
