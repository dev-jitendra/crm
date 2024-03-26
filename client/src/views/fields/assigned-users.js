

import LinkMultipleFieldView from 'views/fields/link-multiple';

class AssignedUsersFieldView extends LinkMultipleFieldView {

    init() {
        this.assignmentPermission = this.getAcl().getPermissionLevel('assignmentPermission');

        if (this.assignmentPermission === 'no') {
            this.readOnly = true;
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

    getDetailLinkHtml(id, name) {
        let html = super.getDetailLinkHtml(id);

        let avatarHtml = this.isDetailMode() ?
            this.getHelper().getAvatarHtml(id, 'small', 14, 'avatar-link') : '';

        if (!avatarHtml) {
            return html;
        }

        return avatarHtml + ' ' + html;
    }
}

export default AssignedUsersFieldView;
