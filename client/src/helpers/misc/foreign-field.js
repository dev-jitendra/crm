



export default class {

    
    constructor(view) {
        
        this.view = view;

        const metadata = view.getMetadata();
        const model = view.model;
        const field = view.params.field;
        const link = view.params.link;

        const entityType = metadata.get(['entityDefs', model.entityType, 'links', link, 'entity']) ||
            model.entityType;

        const fieldDefs = metadata.get(['entityDefs', entityType, 'fields', field]) || {};
        const type = fieldDefs.type;

        const ignoreList = [
            'default',
            'audited',
            'readOnly',
            'required',
        ];

        
        this.foreignParams = {};

        view.getFieldManager().getParamList(type).forEach(defs => {
            const name = defs.name;

            if (ignoreList.includes(name)) {
                return;
            }

            this.foreignParams[name] = fieldDefs[name] || null;
        });
    }

    
    getForeignParams() {
        return Espo.Utils.cloneDeep(this.foreignParams);
    }
}
