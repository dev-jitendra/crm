

define('views/user/record/row-actions/relationship-followers', ['views/record/row-actions/relationship'], function (Dep) {

    return Dep.extend({

        getActionList: function () {
            var list = [{
                action: 'quickView',
                label: 'View',
                data: {
                    id: this.model.id
                },
                link: '#' + this.model.entityType + '/view/' + this.model.id
            }];

            if (
                this.getUser().isAdmin() ||
                this.getAcl().get('followerManagementPermission') !== 'no' ||
                this.model.isPortal() && this.getAcl().get('portalPermission') === 'yes' ||
                this.model.id === this.getUser().id
            ) {
                list.push({
                    action: 'unlinkRelated',
                    label: 'Unlink',
                    data: {
                        id: this.model.id
                    }
                });
            }

            return list;
        }
    });
});
