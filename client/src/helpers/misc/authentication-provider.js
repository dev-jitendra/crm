



export default class {
    
    constructor(view) {
        
        this.view = view;

        this.metadata = view.getMetadata();

        
        this.model = view.model;

        
        const defs = view.getMetadata().get(['authenticationMethods']) || {};

        
        this.methodList = Object.keys(defs).filter(item => {
            
            const data = defs[item].provider || {};

            return data.isAvailable;
        });

        
        this.authFields = {};

        
        this.dynamicLogicDefs = {
            fields: {},
            panels: {},
        };
    }

    
    setupPanelsVisibility(callback) {
        this.handlePanelsVisibility(callback);

        this.view.listenTo(this.model, 'change:method', () => this.handlePanelsVisibility(callback));
    }

    
    getFromMetadata(method, param) {
        return this.metadata
            .get(['authenticationMethods', method, 'provider', param]) ||
        this.metadata
            .get(['authenticationMethods', method, 'settings', param]);
    }

    
    setupMethods() {
        this.methodList.forEach(method => this.setupMethod(method));

        return this.dynamicLogicDefs;
    }

    
    setupMethod(method) {
        
        let fieldList = this.getFromMetadata(method, 'fieldList') || [];

        fieldList = fieldList.filter(item => this.model.hasField(item));

        this.authFields[method] = fieldList;

        const mDynamicLogicFieldsDefs = (this.getFromMetadata(method, 'dynamicLogic') || {}).fields || {};

        for (const f in mDynamicLogicFieldsDefs) {
            if (!fieldList.includes(f)) {
                continue;
            }

            const defs = this.modifyDynamicLogic(mDynamicLogicFieldsDefs[f]);

            this.dynamicLogicDefs.fields[f] = Espo.Utils.cloneDeep(defs);
        }
    }

    
    modifyDynamicLogic(defs) {
        defs = Espo.Utils.clone(defs);

        if (Array.isArray(defs)) {
            return defs.map(item => this.modifyDynamicLogic(item));
        }

        if (typeof defs === 'object') {
            const o = {};

            for (const property in defs) {
                let value = defs[property];

                if (property === 'attribute' && value === 'authenticationMethod') {
                    value = 'method';
                }

                o[property] = this.modifyDynamicLogic(value);
            }

            return o;
        }

        return defs;
    }

    modifyDetailLayout(layout) {
        this.methodList.forEach(method => {
            let mLayout = this.getFromMetadata(method, 'layout');

            if (!mLayout) {
                return;
            }

            mLayout = Espo.Utils.cloneDeep(mLayout);
            mLayout.name = method;

            this.prepareLayout(mLayout, method);

            layout.push(mLayout);
        });
    }

    prepareLayout(layout, method) {
        layout.rows.forEach(row => {
            row
                .filter(item => !item.noLabel && !item.labelText && item.name)
                .forEach(item => {
                    if (item === null) {
                        return;
                    }

                    const labelText = this.view.translate(item.name, 'fields', 'Settings');

                    item.options = item.options || {};

                    if (labelText && labelText.toLowerCase().indexOf(method.toLowerCase() + ' ') === 0) {
                        item.labelText = labelText.substring(method.length + 1);
                    }

                    item.options.tooltipText = this.view.translate(item.name, 'tooltips', 'Settings');
                });
        });

        layout.rows = layout.rows.map(row => {
            row = row.map(cell => {
                if (
                    cell &&
                    cell.name &&
                    !this.model.hasField(cell.name)
                ) {
                    return false;
                }

                return cell;
            })

            return row;
        });
    }

    
    handlePanelsVisibility(callback) {
        const authenticationMethod = this.model.get('method');

        this.methodList.forEach(method => {
            const fieldList = (this.authFields[method] || []);

            if (method !== authenticationMethod) {
                this.view.hidePanel(method);

                fieldList.forEach(field => {
                    this.view.hideField(field);
                });

                return;
            }

            this.view.showPanel(method);

            fieldList.forEach(field => this.view.showField(field));

            callback();
        });
    }
}
