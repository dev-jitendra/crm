




class ThemeManager {

    
    constructor(config, preferences, metadata, name) {
        
        this.config = config;

        
        this.preferences = preferences;

        
        this.metadata = metadata;

        
        this.name = name || null;
    }

    
    defaultParams = {
        screenWidthXs: 768,
        dashboardCellHeight: 155,
        dashboardCellMargin: 19,
    }

    
    getName() {
        if (this.name) {
            return this.name;
        }

        if (!this.config.get('userThemesDisabled')) {
            const name = this.preferences.get('theme');

            if (name && name !== '') {
                return name;
            }
        }

        return this.config.get('theme');
    }

    
    getAppliedName() {
        const name = window.getComputedStyle(document.body).getPropertyValue('--theme-name');

        if (!name) {
            return null;
        }

        return name.trim();
    }

    
    isApplied() {
        const appliedName = this.getAppliedName();

        if (!appliedName) {
            return true;
        }

        return this.getName() === appliedName;
    }

    
    getStylesheet() {
        let link = this.getParam('stylesheet') || 'client/css/espo/espo.css';

        if (this.config.get('cacheTimestamp')) {
            link += '?r=' + this.config.get('cacheTimestamp').toString();
        }

        return link;
    }

    
    getIframeStylesheet() {
        let link = this.getParam('stylesheetIframe') || 'client/css/espo/espo-iframe.css';

        if (this.config.get('cacheTimestamp')) {
            link += '?r=' + this.config.get('cacheTimestamp').toString();
        }

        return link;
    }

    
    getIframeFallbackStylesheet() {
        let link = this.getParam('stylesheetIframeFallback') || 'client/css/espo/espo-iframe.css'

        if (this.config.get('cacheTimestamp')) {
            link += '?r=' + this.config.get('cacheTimestamp').toString();
        }

        return link;
    }

    
    getParam(name) {
        if (name !== 'params' && name !== 'mappedParams') {
            const varValue = this.getVarParam(name);

            if (varValue !== null) {
                return varValue;
            }

            const mappedValue = this.getMappedParam(name);

            if (mappedValue !== null) {
                return mappedValue;
            }
        }

        let value = this.metadata.get(['themes', this.getName(), name]);

        if (value !== null) {
            return value;
        }

        value = this.metadata.get(['themes', this.getParentName(), name]);

        if (value !== null) {
            return value;
        }

        return this.defaultParams[name] || null;
    }

    
    getVarParam(name) {
        const params = this.getParam('params') || {};

        if (!(name in params)) {
            return null;
        }

        let values = null;

        if (!this.config.get('userThemesDisabled') && this.preferences.get('theme')) {
            values = this.preferences.get('themeParams');
        }

        if (!values) {
            values = this.config.get('themeParams');
        }

        if (values && (name in values)) {
            return values[name];
        }

        if ('default' in params[name]) {
            return params[name].default;
        }

        return null;
    }

    
    getMappedParam(name) {
        const mappedParams = this.getParam('mappedParams') || {};

        if (!(name in mappedParams)) {
            return null;
        }

        const mapped = mappedParams[name].param;
        const valueMap = mappedParams[name].valueMap;

        if (mapped && valueMap) {
            const key = this.getParam(mapped);

            return valueMap[key];
        }

        return null;
    }

    
    getParentName() {
        return this.metadata.get(['themes', this.getName(), 'parent']) || 'Espo';
    }

    
    isUserTheme() {
        if (this.config.get('userThemesDisabled')) {
            return false;
        }

        const name = this.preferences.get('theme');

        if (!name || name === '') {
            return false;
        }

        return name !== this.config.get('theme');
    }
}

export default ThemeManager;
