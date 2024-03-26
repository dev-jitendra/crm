

define('views/portal-user/list', ['views/list'], function (Dep) {

    return Dep.extend({

        defaultOrderBy: 'createdAt',

        defaultOrder: 'desc',

        setup: function () {
            Dep.prototype.setup.call(this);
        },

        actionCreate: function () {
            var viewName = 'crm:views/contact/modals/select-for-portal-user';

            this.createView('modal', viewName, {
                scope: 'Contact',
                primaryFilterName: 'notPortalUsers',
                createButton: false,
                mandatorySelectAttributeList: [
                    'salutationName',
                    'firstName',
                    'lastName',
                    'accountName',
                    'accountId',
                    'emailAddress',
                    'emailAddressData',
                    'phoneNumber',
                    'phoneNumberData',
                ]
            }, view => {
                view.render();

                this.listenToOnce(view, 'select', model => {
                    var attributes = {};

                    attributes.contactId = model.id;
                    attributes.contactName = model.get('name');

                    if (model.get('accountId')) {
                        var names = {};
                        names[model.get('accountId')] = model.get('accountName');

                        attributes.accountsIds = [model.get('accountId')];
                        attributes.accountsNames = names;
                    }

                    attributes.firstName = model.get('firstName');
                    attributes.lastName = model.get('lastName');
                    attributes.salutationName = model.get('salutationName');

                    attributes.emailAddress = model.get('emailAddress');
                    attributes.emailAddressData = model.get('emailAddressData');

                    attributes.phoneNumber = model.get('phoneNumber');
                    attributes.phoneNumberData = model.get('phoneNumberData');

                    attributes.userName = attributes.emailAddress;

                    attributes.type = 'portal';

                    var router = this.getRouter();

                    var url = '#' + this.scope + '/create';

                    router.dispatch(this.scope, 'create', {
                        attributes: attributes
                    });

                    router.navigate(url, {trigger: false});
                });

                this.listenToOnce(view, 'skip', () => {
                    var attributes = {
                        type: 'portal',
                    };

                    var router = this.getRouter();
                    var url = '#' + this.scope + '/create';

                    router.dispatch(this.scope, 'create', {
                        attributes: attributes
                    });

                    router.navigate(url, {trigger: false});
                });
            });
        },
    });
});
