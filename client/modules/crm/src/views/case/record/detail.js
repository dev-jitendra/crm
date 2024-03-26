

define('crm:views/case/record/detail', ['views/record/detail'], function (Dep) {

    return Dep.extend({

        selfAssignAction: true,

        getSelfAssignAttributes: function () {
            if (this.model.get('status') === 'New') {
                if (~(this.getMetadata().get(['entityDefs', 'Case', 'fields', 'status', 'options']) || [])
                    .indexOf('Assigned')
                ) {
                    return {
                        'status': 'Assigned',
                    };
                }
            }
        },
    });
});
