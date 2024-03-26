

import DetailBottomRecordView from 'views/record/detail-bottom';

class UserDetailBottomRecordView extends  DetailBottomRecordView {

    setupPanels() {
       super.setupPanels();

        let streamAllowed = this.getAcl().checkUserPermission(this.model);

        if (
            !streamAllowed &&
            this.getAcl().getPermissionLevel('userPermission') === 'team' &&
            !this.model.has('teamsIds')
        ) {
            this.listenToOnce(this.model, 'sync', () => {
                if (this.getAcl().checkUserPermission(this.model)) {
                    this.onPanelsReady(() => {
                        this.showPanel('stream', 'acl');
                    });
                }
            });
        }

        this.panelList.push({
            "name": "stream",
            "label": "Stream",
            "view": "views/user/record/panels/stream",
            "sticked": true,
            "hidden": !streamAllowed,
        });

        if (!streamAllowed) {
            this.recordHelper.setPanelStateParam('stream', 'hiddenAclLocked', true);
        }
    }
}

export default UserDetailBottomRecordView;
