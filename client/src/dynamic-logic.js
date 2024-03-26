




class DynamicLogic {

    
    constructor(defs, recordView) {

        
        this.defs = defs || {};

        
        this.recordView = recordView;

        
        this.fieldTypeList = ['visible', 'required', 'readOnly'];

        
        this.panelTypeList = ['visible', 'styled'];
    }

    
    process() {
        const fields = this.defs.fields || {};

        Object.keys(fields).forEach(field => {
            const item = (fields[field] || {});

            this.fieldTypeList.forEach(type => {
                if (!(type in item)) {
                    return;
                }

                if (!item[type]) {
                    return;
                }

                const typeItem = (item[type] || {});

                if (!typeItem.conditionGroup) {
                    return;
                }

                const result = this.checkConditionGroup(typeItem.conditionGroup);

                let methodName;

                if (result) {
                    methodName = 'makeField' + Espo.Utils.upperCaseFirst(type) + 'True';
                }
                else {
                    methodName = 'makeField' + Espo.Utils.upperCaseFirst(type) + 'False';
                }

                this[methodName](field);
            });
        });

        const panels = this.defs.panels || {};

        Object.keys(panels).forEach(panel => {
            this.panelTypeList.forEach(type => {
                this.processPanel(panel, type);
            });
        });

        const options = this.defs.options || {};

        Object.keys(options).forEach(field => {
            const itemList = options[field];

            if (!options[field]) {
                return;
            }

            let isMet = false;

            for (const i in itemList) {
                const item = itemList[i];

                if (this.checkConditionGroup(item.conditionGroup)) {
                    this.setOptionList(field, item.optionList || []);

                    isMet = true;

                    break;
                }
            }

            if (!isMet) {
                this.resetOptionList(field);
            }
        });
    }

    
    processPanel(panel, type) {
        const panels = this.defs.panels || {};
        const item = (panels[panel] || {});

        if (!(type in item)) {
            return;
        }

        const typeItem = (item[type] || {});

        if (!typeItem.conditionGroup) {
            return;
        }

        const result = this.checkConditionGroup(typeItem.conditionGroup);

        let methodName;

        if (result) {
            methodName = 'makePanel' + Espo.Utils.upperCaseFirst(type) + 'True';
        }
        else {
            methodName = 'makePanel' + Espo.Utils.upperCaseFirst(type) + 'False';
        }

        this[methodName](panel);
    }

    
    checkConditionGroup(data, type) {
        type = type || 'and';

        let list;
        let result = false;

        if (type === 'and') {
            list =  data || [];

            result = true;

            for (const i in list) {
                if (!this.checkCondition(list[i])) {
                    result = false;

                    break;
                }
            }
        }
        else if (type === 'or') {
            list =  data || [];

            for (const i in list) {
                if (this.checkCondition(list[i])) {
                    result = true;

                    break;
                }
            }
        }
        else if (type === 'not') {
            if (data) {
                result = !this.checkCondition(data);
            }
        }

        return result;
    }

    
    getAttributeValue(attribute) {
        if (attribute.startsWith('$')) {
            if (attribute === '$user.id') {
                return this.recordView.getUser().id;
            }

            if (attribute === '$user.teamsIds') {
                return this.recordView.getUser().getTeamIdList();
            }
        }

        return this.recordView.model.get(attribute);
    }

    
    checkCondition(defs) {
        defs = defs || {};

        const type = defs.type || 'equals';

        if (['or', 'and', 'not'].includes(type)) {
            return this.checkConditionGroup(defs.value,  type);
        }

        const attribute = defs.attribute;
        const value = defs.value;

        if (!attribute) {
            return false;
        }

        const setValue = this.getAttributeValue(attribute);

        if (type === 'equals') {
            if (!value) {
                return false;
            }

            return setValue === value;
        }

        if (type === 'notEquals') {
            if (!value) {
                return false;
            }

            return setValue !== value;
        }

        if (type === 'isEmpty') {
            if (Array.isArray(setValue)) {
                return !setValue.length;
            }

            return setValue === null || (setValue === '') || typeof setValue === 'undefined';
        }

        if (type === 'isNotEmpty') {
            if (Array.isArray(setValue)) {
                return !!setValue.length;
            }

            return setValue !== null && (setValue !== '') && typeof setValue !== 'undefined';
        }

        if (type === 'isTrue') {
            return !!setValue;
        }

        if (type === 'isFalse') {
            return !setValue;
        }

        if (type === 'contains' || type === 'has') {
            if (!setValue) {
                return false;
            }

            return !!~setValue.indexOf(value);
        }

        if (type === 'notContains' || type === 'notHas') {
            if (!setValue) {
                return true;
            }

            return !~setValue.indexOf(value);
        }

        if (type === 'startsWith') {
            if (!setValue) {
                return false;
            }

            return setValue.indexOf(value) === 0;
        }

        if (type === 'endsWith') {
            if (!setValue) {
                return false;
            }

            return setValue.indexOf(value) === setValue.length - value.length;
        }

        if (type === 'matches') {
            if (!setValue) {
                return false;
            }

            const match = /^\/(.*)\/([a-z]*)$/.exec(value);

            if (!match || match.length < 2) {
                return false;
            }

            return (new RegExp(match[1], match[2])).test(setValue);
        }

        if (type === 'greaterThan') {
            return setValue > value;
        }

        if (type === 'lessThan') {
            return setValue < value;
        }

        if (type === 'greaterThanOrEquals') {
            return setValue >= value;
        }

        if (type === 'lessThanOrEquals') {
            return setValue <= value;
        }

        if (type === 'in') {
            return !!~value.indexOf(setValue);
        }

        if (type === 'notIn') {
            return !~value.indexOf(setValue);
        }

        if (type === 'isToday') {
            const dateTime = this.recordView.getDateTime();

            if (!setValue) {
                return false;
            }

            if (setValue.length > 10) {
                return dateTime.toMoment(setValue).isSame(dateTime.getNowMoment(), 'day');
            }

            return dateTime.toMomentDate(setValue).isSame(dateTime.getNowMoment(), 'day');
        }

        if (type === 'inFuture') {
            const dateTime = this.recordView.getDateTime();

            if (!setValue) {
                return false;
            }

            if (setValue.length > 10) {
                return dateTime.toMoment(setValue).isAfter(dateTime.getNowMoment(), 'day');
            }

            return dateTime.toMomentDate(setValue).isAfter(dateTime.getNowMoment(), 'day');
        }

        if (type === 'inPast') {
            const dateTime = this.recordView.getDateTime();

            if (!setValue) {
                return false;
            }


            if (setValue.length > 10) {
                return dateTime.toMoment(setValue).isBefore(dateTime.getNowMoment(), 'day');
            }

            return dateTime.toMomentDate(setValue).isBefore(dateTime.getNowMoment(), 'day');
        }

        return false;
    }

    
    setOptionList(field, optionList) {
        this.recordView.setFieldOptionList(field, optionList);
    }

    
    resetOptionList(field) {
        this.recordView.resetFieldOptionList(field);
    }

    
    
    makeFieldVisibleTrue(field) {
        this.recordView.showField(field);
    }

    
    
    makeFieldVisibleFalse(field) {
        this.recordView.hideField(field);
    }

    
    
    makeFieldRequiredTrue(field) {
        this.recordView.setFieldRequired(field);
    }

    
    
    makeFieldRequiredFalse(field) {
        this.recordView.setFieldNotRequired(field);
    }

    
    
    makeFieldReadOnlyTrue(field) {
        this.recordView.setFieldReadOnly(field);
    }

    
    
    makeFieldReadOnlyFalse(field) {
        this.recordView.setFieldNotReadOnly(field);
    }

    
    
    makePanelVisibleTrue(panel) {
        this.recordView.showPanel(panel, 'dynamicLogic');
    }

    
    
    makePanelVisibleFalse(panel) {
        this.recordView.hidePanel(panel, false, 'dynamicLogic');
    }

    
    
    makePanelStyledTrue(panel) {
        this.recordView.stylePanel(panel);
    }

    
    
    makePanelStyledFalse(panel) {
        this.recordView.unstylePanel(panel);
    }

    
    addPanelVisibleCondition(name, item) {
        this.defs.panels = this.defs.panels || {};
        this.defs.panels[name] = this.defs.panels[name] || {};

        this.defs.panels[name].visible = item;

        this.processPanel(name, 'visible');
    }

    
    addPanelStyledCondition(name, item) {
        this.defs.panels = this.defs.panels || {};
        this.defs.panels[name] = this.defs.panels[name] || {};

        this.defs.panels[name].styled = item;

        this.processPanel(name, 'styled');
    }
}

export default DynamicLogic;
