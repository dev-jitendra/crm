

import SelectRelatedHandler from 'handlers/select-related';

class SameAccountManySelectRelatedHandler extends SelectRelatedHandler {

    
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
            const nameHash = {};
            nameHash[accountId] = accountName;

            advanced.accounts = {
                field: 'accounts',
                type: 'linkedWith',
                value: [accountId],
                data: {nameHash: nameHash},
            };
        }

        return Promise.resolve({
            advanced: advanced,
        });
    }
}


export default SameAccountManySelectRelatedHandler;
