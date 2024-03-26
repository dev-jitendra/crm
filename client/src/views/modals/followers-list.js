

import RelatedListModalView from 'views/modals/related-list';

class FollowersListModalView extends RelatedListModalView {

    massActionRemoveDisabled = true
    massActionMassUpdateDisabled = true
    mandatorySelectAttributeList = ['type']

    setup() {
        if (
            !this.getUser().isAdmin() &&
            this.getAcl().getPermissionLevel('followerManagementPermission') === 'no' &&
            this.getAcl().getPermissionLevel('portalPermission') === 'no'
        ) {
            this.unlinkDisabled = true;
        }

        super.setup();
    }

    actionSelectRelated() {
        let p = this.getParentView();

        let view = null;

        while (p) {
            
            if (p.actionSelectRelated) {
                view = p;

                break;
            }

            p = p.getParentView();
        }

        let filter = 'active';

        if (
            !this.getUser().isAdmin() &&
            this.getAcl().getPermissionLevel('followerManagementPermission') === 'no' &&
            this.getAcl().getPermissionLevel('portalPermission') === 'yes'
        ) {
            filter = 'activePortal';
        }

        
        p.actionSelectRelated({
            link: this.link,
            primaryFilterName: filter,
            massSelect: false,
            foreignEntityType: 'User',
            viewKey: 'selectFollowers',
        });
    }
}

export default FollowersListModalView;
