

define('views/extension/record/row-actions', ['views/record/row-actions/default'], function (Dep) {

    return Dep.extend({

        getActionList: function () {
            if (!this.options.acl.edit) {
                return [];
            }

            if (this.model.get('isInstalled')) {
                return [
                    {
                        action: 'uninstall',
                        label: 'Uninstall',
                        data: {
                            id: this.model.id,
                        },
                    },
                ];
            }

            return [
                {
                    action: 'install',
                    label: 'Install',
                    data: {
                        id: this.model.id,
                    },
                },
                {
                    action: 'quickRemove',
                    label: 'Remove',
                    data: {
                        id: this.model.id,
                    },
                },
            ];
        },
    });
});
