

import LinkMultipleFieldView from 'views/fields/link-multiple';

class CalendarUsersFieldView extends LinkMultipleFieldView {

    foreignScope = 'User'
    sortable = true

    getSelectBoolFilterList() {
        if (this.getAcl().getPermissionLevel('userPermission') === 'team') {
            return ['onlyMyTeam'];
        }
    }

    getSelectPrimaryFilterName() {
        return 'active';
    }
}

export default CalendarUsersFieldView;
