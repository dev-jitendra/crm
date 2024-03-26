

import SelectRelatedHandler from 'handlers/select-related';

class SameAccountSelectRelatedHandler extends SelectRelatedHandler {

    
    getFilters(model) {
        const advanced = {};

        let accountId = null;
        let accountName = null;

        if (model.get('accountId')) {
            accountId = model.get('accountId');
            accountName = model.get('accountName');
        }

        if (!accountId && model.get('parentType') === 'Account' && model.get('parentId')) {
            accountId = model.get('parentId');
            accountName = model.get('parentName');
        }

        if (accountId) {
            advanced.account = {
                attribute: 'accountId',
                type: 'equals',
                value: accountId,
                data: {
                    type: 'is',
                    nameValue: accountName,
                },
            };
        }

        return Promise.resolve({
            advanced: advanced,
        });
    }
}


export default SameAccountSelectRelatedHandler;
