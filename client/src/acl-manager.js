



import Acl from 'acl';
import Utils from 'utils';
import {View as BullView} from 'bullbone';




class AclManager {

    
    data = null
    fieldLevelList = ['yes', 'no']

    
    constructor(user, implementationClassMap, aclAllowDeleteCreated) {
        this.setEmpty();

        
        this.user = user || null;
        this.implementationClassMap = implementationClassMap || {};
        this.aclAllowDeleteCreated = aclAllowDeleteCreated;
    }

    
    setEmpty() {
        this.data = {
            table: {},
            fieldTable:  {},
            fieldTableQuickAccess: {},
        };

        this.implementationHash = {};
        this.forbiddenFieldsCache = {};
        this.implementationClassMap = {};
        this.forbiddenAttributesCache = {};
    }

    
    getImplementation(scope) {
        if (!(scope in this.implementationHash)) {
            let implementationClass = Acl;

            if (scope in this.implementationClassMap) {
                implementationClass = this.implementationClassMap[scope];
            }

            const forbiddenFieldList = this.getScopeForbiddenFieldList(scope);

            const params = {
                aclAllowDeleteCreated: this.aclAllowDeleteCreated,
                teamsFieldIsForbidden: !!~forbiddenFieldList.indexOf('teams'),
                forbiddenFieldList: forbiddenFieldList,
            };

            this.implementationHash[scope] = new implementationClass(this.getUser(), scope, params);
        }

        return this.implementationHash[scope];
    }

    
    getUser() {
        return this.user;
    }

    
    set(data) {
        data = data || {};

        this.data = data;
        this.data.table = this.data.table || {};
        this.data.fieldTable = this.data.fieldTable || {};
        this.data.attributeTable = this.data.attributeTable || {};
    }

    
    get(name) {
        return this.data[name] || null;
    }

    
    getPermissionLevel(permission) {
        let permissionKey = permission;

        if (permission.slice(-10) !== 'Permission') {
            permissionKey = permission + 'Permission';
        }

        return this.data[permissionKey] || 'no';
    }

    
    getLevel(scope, action) {
        if (!(scope in this.data.table)) {
            return null;
        }

        const scopeItem = this.data.table[scope];

        if (
            typeof scopeItem !== 'object' ||
            !(action in scopeItem)
        ) {
            return null;
        }

        return scopeItem[action];
    }

    
    clear() {
        this.setEmpty();
    }

    
    checkScopeHasAcl(scope) {
        const data = (this.data.table || {})[scope];

        if (typeof data === 'undefined') {
            return false;
        }

        return true;
    }

    
    checkScope(scope, action, precise) {
        let data = (this.data.table || {})[scope];

        if (typeof data === 'undefined') {
            data = null;
        }

        return this.getImplementation(scope).checkScope(data, action, precise);
    }

    
    checkModel(model, action, precise) {
        const scope = model.entityType;

        
        if (action === 'edit') {
            if (!model.isEditable()) {
                return false;
            }
        }

        if (action === 'delete') {
            if (!model.isRemovable()) {
                return false;
            }
        }

        let data = (this.data.table || {})[scope];

        if (typeof data === 'undefined') {
            data = null;
        }

        const impl = this.getImplementation(scope);

        if (action) {
            const methodName = 'checkModel' + Utils.upperCaseFirst(action);

            if (methodName in impl) {
                return impl[methodName](model, data, precise);
            }
        }

        return impl.checkModel(model, data, action, precise);
    }

    
    check(subject, action, precise) {
        if (typeof subject === 'string') {
            return this.checkScope(subject, action, precise);
        }

        return this.checkModel(subject, action, precise);
    }

    
    checkIsOwner(model) {
        return this.getImplementation(model.entityType).checkIsOwner(model);
    }

    
    
    checkInTeam(model) {
        return this.getImplementation(model.entityType).checkInTeam(model);
    }

    
    
    checkAssignmentPermission(user) {
        return this.checkPermission('assignmentPermission', user);
    }

    
    checkUserPermission(user) {
        return this.checkPermission('userPermission', user);
    }

    
    checkPermission(permission, user) {
        if (this.getUser().isAdmin()) {
            return true;
        }

        const level = this.getPermissionLevel(permission);

        if (level === 'no') {
            if (user.id === this.getUser().id) {
                return true;
            }

            return false;
        }

        if (level === 'team') {
            if (!user.has('teamsIds')) {
                return false;
            }

            let result = false;

            const teamsIds = user.get('teamsIds') || [];

            teamsIds.forEach(id => {
                if (~(this.getUser().get('teamsIds') || []).indexOf(id)) {
                    result = true;
                }
            });

            return result;
        }

        if (level === 'all') {
            return true;
        }

        if (level === 'yes') {
            return true;
        }

        return false;
    }

    
    getScopeForbiddenFieldList(scope, action, thresholdLevel) {
        action = action || 'read';
        thresholdLevel = thresholdLevel || 'no';

        const key = scope + '_' + action + '_' + thresholdLevel;

        if (key in this.forbiddenFieldsCache) {
            return Utils.clone(this.forbiddenFieldsCache[key]);
        }

        const levelList = this.fieldLevelList.slice(this.fieldLevelList.indexOf(thresholdLevel));

        const fieldTableQuickAccess = this.data.fieldTableQuickAccess || {};
        const scopeData = fieldTableQuickAccess[scope] || {};
        const fieldsData = scopeData.fields || {};
        const actionData = fieldsData[action] || {};

        const fieldList = [];

        levelList.forEach(level => {
            const list = actionData[level] || [];

            list.forEach(field => {
                if (~fieldList.indexOf(field)) {
                    return;
                }

                fieldList.push(field);
            });
        });

        this.forbiddenFieldsCache[key] = fieldList;

        return Utils.clone(fieldList);
    }

    
    getScopeForbiddenAttributeList(scope, action, thresholdLevel) {
        action = action || 'read';
        thresholdLevel = thresholdLevel || 'no';

        const key = scope + '_' + action + '_' + thresholdLevel;

        if (key in this.forbiddenAttributesCache) {
            return Utils.clone(this.forbiddenAttributesCache[key]);
        }

        const levelList = this.fieldLevelList.slice(this.fieldLevelList.indexOf(thresholdLevel));

        const fieldTableQuickAccess = this.data.fieldTableQuickAccess || {};
        const scopeData = fieldTableQuickAccess[scope] || {};

        const attributesData = scopeData.attributes || {};
        const actionData = attributesData[action] || {};

        const attributeList = [];

        levelList.forEach(level => {
            const list = actionData[level] || [];

            list.forEach(attribute => {
                if (~attributeList.indexOf(attribute)) {
                    return;
                }

                attributeList.push(attribute);
            });
        });

        this.forbiddenAttributesCache[key] = attributeList;

        return Utils.clone(attributeList);
    }

    
    checkTeamAssignmentPermission(teamId) {
        if (this.getPermissionLevel('assignmentPermission') === 'all') {
            return true;
        }

        return !!~this.getUser().getLinkMultipleIdList('teams').indexOf(teamId);
    }

    
    checkField(scope, field, action) {
        return !~this.getScopeForbiddenFieldList(scope, action).indexOf(field);
    }
}

AclManager.extend = BullView.extend;

export default AclManager;
