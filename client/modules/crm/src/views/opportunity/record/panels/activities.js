

define('crm:views/opportunity/record/panels/activities', ['crm:views/record/panels/activities'], function (Dep) {

    return Dep.extend({

        getComposeEmailAttributes: function (scope, data, callback) {
            data = data || {};

            Espo.Ui.notify(' ... ');

            Dep.prototype.getComposeEmailAttributes.call(this, scope, data, (attributes) => {
                Espo.Ajax.getRequest('Opportunity/action/emailAddressList?id=' + this.model.id).then(list => {
                    attributes.to = '';
                    attributes.cc = '';
                    attributes.nameHash = {};

                    list.forEach((item, i) => {
                        attributes.to += item.emailAddress + ';';
                        attributes.nameHash[item.emailAddress] = item.name;
                    });

                    Espo.Ui.notify(false);

                    callback.call(this, attributes);

                });
            })
        },
    });
});
