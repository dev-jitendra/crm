



import {View as BullView} from 'bullbone';


class Acl {

    
    constructor(user, scope, params) {
        
        this.user = user || null;
        this.scope = scope;

        params = params || {};

        this.aclAllowDeleteCreated = params.aclAllowDeleteCreated;
        this.teamsFieldIsForbidden = params.teamsFieldIsForbidden;
        this.forbiddenFieldList = params.forbiddenFieldList;
    }

    
    getUser() {
        return this.user;
    }

    
    checkScope(data, action, precise, entityAccessData) {
        entityAccessData = entityAccessData || {};

        const inTeam = entityAccessData.inTeam;
        const isOwner = entityAccessData.isOwner;

        if (this.getUser().isAdmin()) {
            if (data === false) {
                return false;
            }

            return true;
        }

        if (data === false) {
            return false;
        }

        if (data === true) {
            return true;
        }

        if (typeof data === 'string') {
            return true;
        }

        if (data === null) {
            return false;
        }

        action = action || null;

        if (action === null) {
            return true;
        }
        if (!(action in data)) {
            return false;
        }

        const value = data[action];

        if (value === 'all') {
            return true;
        }

        if (value === 'yes') {
            return true;
        }

        if (value === 'no') {
            return false;
        }

        if (typeof isOwner === 'undefined') {
            return true;
        }

        if (isOwner) {
            if (value === 'own' || value === 'team') {
                return true;
            }
        }

        let result = false;

        if (value === 'team') {
            result = inTeam;

            if (inTeam === null) {
                if (precise) {
                    result = null;
                }
                else {
                    return true;
                }
            }
            else if (inTeam) {
                return true;
            }
        }

        if (isOwner === null) {
            if (precise) {
                result = null;
            }
            else {
                return true;
            }
        }

        return result;
    }

    
    checkModel(model, data, action, precise) {
        if (this.getUser().isAdmin()) {
            return true;
        }

        const entityAccessData = {
            isOwner: this.checkIsOwner(model),
            inTeam: this.checkInTeam(model),
        };

        return this.checkScope(data, action, precise, entityAccessData);
    }

    
    
    checkModelDelete(model, data, precise) {
        const result = this.checkModel(model, data, 'delete', precise);

        if (result) {
            return true;
        }

        if (data === false) {
            return false;
        }

        const d = data || {};

        if (d.read === 'no') {
            return false;
        }

        if (model.has('createdById')) {
            if (model.get('createdById') === this.getUser().id && this.aclAllowDeleteCreated) {
                if (!model.has('assignedUserId')) {
                    return true;
                }

                if (!model.get('assignedUserId')) {
                    return true;
                }

                if (model.get('assignedUserId') === this.getUser().id) {
                    return true;
                }

            }
        }

        return result;
    }

    
    checkIsOwner(model) {
        let result = false;

        if (model.hasField('assignedUser')) {
            if (this.getUser().id === model.get('assignedUserId')) {
                return true;
            }

            if (!model.has('assignedUserId')) {
                result = null;
            }
        }
        else {
            if (model.hasField('createdBy')) {
                if (this.getUser().id === model.get('createdById')) {
                    return true;
                }

                if (!model.has('createdById')) {
                    result = null;
                }
            }
        }

        if (model.hasField('assignedUsers')) {
            if (!model.has('assignedUsersIds')) {
                return null;
            }

            if (~(model.get('assignedUsersIds') || []).indexOf(this.getUser().id)) {
                return true;
            }

            result = false;
        }

        return result;
    }

    
    checkInTeam(model) {
        const userTeamIdList = this.getUser().getTeamIdList();

        if (!model.has('teamsIds')) {
            if (this.teamsFieldIsForbidden) {
                return true;
            }

            return null;
        }

        const teamIdList = model.getTeamIdList();

        let inTeam = false;

        userTeamIdList.forEach(id => {
            if (~teamIdList.indexOf(id)) {
                inTeam = true;
            }
        });

        return inTeam;
    }
}

Acl.extend = BullView.extend;

export default Acl;
