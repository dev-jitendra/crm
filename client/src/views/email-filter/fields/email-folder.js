

define('views/email-filter/fields/email-folder', ['views/fields/link'], function (Dep) {

    return Dep.extend({

        createDisabled: true,

        autocompleteDisabled: true,

        getSelectFilters: function () {
            if (this.getUser().isAdmin()) {
                if (this.model.get('parentType') === 'User' && this.model.get('parentId')) {
                    return {
                        assignedUser: {
                            type: 'equals',
                            attribute: 'assignedUserId',
                            value: this.model.get('parentId'),
                            data: {
                                nameValue: this.model.get('parentName'),
                            },
                        }
                    };
                }
            }
        },
    });
});
