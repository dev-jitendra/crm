



import {Events} from 'bullbone';


class ViewRecordHelper {

    
    constructor(defaultFieldStates, defaultPanelStates) {

        
        this.defaultFieldStates = defaultFieldStates || {};
        
        this.defaultPanelStates = defaultPanelStates || {};
        
        this.fieldStateMap = {};
        
        this.panelStateMap = {};
        
        this.hiddenFields = {};
        
        this.hiddenPanels = {};
        
        this.fieldOptionListMap = {};
    }

    
    getHiddenFields() {
        return this.hiddenFields;
    }

    
    getHiddenPanels() {
        return this.hiddenPanels;
    }

    
    setFieldStateParam(field, name, value) {
        switch (name) {
            case 'hidden':
                if (value) {
                    this.hiddenFields[field] = true;
                }
                else {
                    delete this.hiddenFields[field];
                }

                break;
        }

        this.fieldStateMap[field] = this.fieldStateMap[field] || {};
        this.fieldStateMap[field][name] = value;

        this.trigger('field-change');
    }

    
    getFieldStateParam(field, name) {
        if (field in this.fieldStateMap) {
            if (name in this.fieldStateMap[field]) {
                return this.fieldStateMap[field][name];
            }
        }

        if (name in this.defaultFieldStates) {
            return this.defaultFieldStates[name];
        }

        return null;
    }

    
    setPanelStateParam(panel, name, value) {
        switch (name) {
            case 'hidden':
                if (value) {
                    this.hiddenPanels[panel] = true;
                } else {
                    delete this.hiddenPanels[panel];
                }
                break;
        }

        this.panelStateMap[panel] = this.panelStateMap[panel] || {};
        this.panelStateMap[panel][name] = value;
    }

    
    getPanelStateParam(panel, name) {
        if (panel in this.panelStateMap) {
            if (name in this.panelStateMap[panel]) {
                return this.panelStateMap[panel][name];
            }
        }

        if (name in this.defaultPanelStates) {
            return this.defaultPanelStates[name];
        }

        return null;
    }

    
    setFieldOptionList(field, list) {
        this.fieldOptionListMap[field] = list;
    }

    
    clearFieldOptionList(field) {
        delete this.fieldOptionListMap[field];
    }

    
    getFieldOptionList(field) {
        return this.fieldOptionListMap[field] || null;
    }

    
    hasFieldOptionList(field) {
        return (field in this.fieldOptionListMap);
    }
}

Object.assign(ViewRecordHelper.prototype, Events);

export default ViewRecordHelper;
