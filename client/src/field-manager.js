




class FieldManager {

    
    constructor(defs, metadata, acl) {

        


        
        this.defs = defs ||  {};

        
        this.metadata = metadata;

        
        this.acl = acl;
    }

    
    getParamList(fieldType) {
        if (fieldType in this.defs) {
            return this.defs[fieldType].params || [];
        }

        return [];
    }

    
    checkFilter(fieldType) {
        if (fieldType in this.defs) {
            if ('filter' in this.defs[fieldType]) {
                return this.defs[fieldType].filter;
            }

            return false;
        }

        return false;
    }

    
    isMergeable(fieldType) {
        if (fieldType in this.defs) {
            return !this.defs[fieldType].notMergeable;
        }

        return false;
    }

    
    getEntityTypeAttributeList(entityType) {
        const list = [];

        const defs = this.metadata.get('entityDefs.' + entityType + '.fields') || {};

        Object.keys(defs).forEach(field => {
            this.getAttributeList(defs[field]['type'], field).forEach(attr => {
                if (!~list.indexOf(attr)) {
                    list.push(attr);
                }
            });
        });

        return list;
    }

    
    getActualAttributeList(fieldType, fieldName) {
        const fieldNames = [];

        if (fieldType in this.defs) {
            if ('actualFields' in this.defs[fieldType]) {
                const actualFields = this.defs[fieldType].actualFields;

                let naming = 'suffix';

                if ('naming' in this.defs[fieldType]) {
                    naming = this.defs[fieldType].naming;
                }

                if (naming === 'prefix') {
                    actualFields.forEach(f => {
                        fieldNames.push(f + Espo.Utils.upperCaseFirst(fieldName));
                    });
                }
                else {
                    actualFields.forEach(f => {
                        fieldNames.push(fieldName + Espo.Utils.upperCaseFirst(f));
                    });
                }
            }
            else {
                fieldNames.push(fieldName);
            }
        }

        return fieldNames;
    }

    
    getNotActualAttributeList(fieldType, fieldName) {
        const fieldNames = [];

        if (fieldType in this.defs) {
            if ('notActualFields' in this.defs[fieldType]) {
                const notActualFields = this.defs[fieldType].notActualFields;

                let naming = 'suffix';

                if ('naming' in this.defs[fieldType]) {
                    naming = this.defs[fieldType].naming;
                }

                if (naming === 'prefix') {
                    notActualFields.forEach(f => {
                        if (f === '') {
                            fieldNames.push(fieldName);
                        }
                        else {
                            fieldNames.push(f + Espo.Utils.upperCaseFirst(fieldName));
                        }
                    });
                }
                else {
                    notActualFields.forEach(f => {
                        fieldNames.push(fieldName + Espo.Utils.upperCaseFirst(f));
                    });
                }
            }
        }

        return fieldNames;
    }

    
    getEntityTypeFieldAttributeList(entityType, field) {
        const type = this.metadata.get(['entityDefs', entityType, 'fields', field, 'type']);

        if (!type) {
            return [];
        }

        return _.union(
            this.getAttributeList(type, field),
            this._getEntityTypeFieldAdditionalAttributeList(entityType, field)
        );
    }

    
    getEntityTypeFieldActualAttributeList(entityType, field) {
        const type = this.metadata.get(['entityDefs', entityType, 'fields', field, 'type']);

        if (!type) {
            return [];
        }

        return _.union(
            this.getActualAttributeList(type, field),
            this._getEntityTypeFieldAdditionalAttributeList(entityType, field)
        );
    }

    
    _getEntityTypeFieldAdditionalAttributeList(entityType, field) {
        const type = this.metadata.get(['entityDefs', entityType, 'fields', field, 'type']);

        if (!type) {
            return [];
        }

        const partList = this.metadata
            .get(['entityDefs', entityType, 'fields', field, 'additionalAttributeList']) || [];

        if (partList.length === 0) {
            return [];
        }

        const isPrefix = (this.defs[type] || {}).naming === 'prefix';

        const list = [];

        partList.forEach(item => {
            if (isPrefix) {
                list.push(item + Espo.Utils.upperCaseFirst(field));

                return;
            }

            list.push(field + Espo.Utils.upperCaseFirst(item));
        });

        return list;
    }

    
    getAttributeList(fieldType, fieldName) {
        return _.union(
            this.getActualAttributeList(fieldType, fieldName),
            this.getNotActualAttributeList(fieldType, fieldName)
        );
    }

    

    
    getEntityTypeFieldList(entityType, o) {
        let list = Object.keys(this.metadata.get(['entityDefs', entityType, 'fields']) || {});

        o = o || {};

        let typeList = o.typeList;

        if (!typeList && o.type) {
            typeList = [o.type];
        }

        if (typeList) {
            list = list.filter(item => {
                const type = this.metadata.get(['entityDefs', entityType, 'fields', item, 'type']);

                return ~typeList.indexOf(type);
            });
        }

        if (o.onlyAvailable || o.acl) {
            list = list.filter(item => {
                return this.isEntityTypeFieldAvailable(entityType, item);
            });
        }

        if (o.acl) {
            const level = o.acl || 'read';

            const forbiddenEditFieldList = this.acl.getScopeForbiddenFieldList(entityType, level);

            list = list.filter(item => {
                return !~forbiddenEditFieldList.indexOf(item);
            });
        }

        return list;
    }

    
    getScopeFieldList(entityType) {
        return this.getEntityTypeFieldList(entityType);
    }

    
    getEntityTypeFieldParam(entityType, field, param) {
        return this.metadata.get(['entityDefs', entityType, 'fields', field, param]);
    }

    
    getViewName(fieldType) {
        if (fieldType in this.defs) {
            if ('view' in this.defs[fieldType]) {
                return this.defs[fieldType].view;
            }
        }

        return 'views/fields/' + Espo.Utils.camelCaseToHyphen(fieldType);
    }

    
    getParams(fieldType) {
        return this.getParamList(fieldType);
    }

    
    getAttributes(fieldType, fieldName) {
        return this.getAttributeList(fieldType, fieldName);
    }

    
    getActualAttributes(fieldType, fieldName) {
        return this.getActualAttributeList(fieldType, fieldName);
    }

    
    getNotActualAttributes(fieldType, fieldName) {
        return this.getNotActualAttributeList(fieldType, fieldName);
    }

    
    isEntityTypeFieldAvailable(entityType, field) {
        const defs = this.metadata.get(['entityDefs', entityType, 'fields', field]) || {};

        if (
            defs.disabled ||
            defs.utility
        ) {
            return false;
        }

        const aclDefs = this.metadata.get(['entityAcl', entityType, 'fields', field]) || {};

        if (
            aclDefs.onlyAdmin ||
            aclDefs.forbidden ||
            aclDefs.internal
        ) {
            return false;
        }

        return true;
    }

    
    isScopeFieldAvailable(entityType, field) {
        return this.isEntityTypeFieldAvailable(entityType, field);
    }
}

export default FieldManager;
