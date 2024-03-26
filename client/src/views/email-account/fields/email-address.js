

define('views/email-account/fields/email-address', ['views/fields/email-address'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            this.on('change', () => {
                var emailAddress = this.model.get('emailAddress');
                this.model.set('name', emailAddress);
            });

            var userId = this.model.get('assignedUserId');

            if (this.getUser().isAdmin() && userId !== this.getUser().id) {
                Espo.Ajax.getRequest('User/' + userId).then((data) => {
                    var list = [];

                    if (data.emailAddress) {
                        list.push(data.emailAddress);

                        this.params.options = list;

                        if (data.emailAddressData) {
                            data.emailAddressData.forEach(item => {
                                if (item.emailAddress === data.emailAddress) {
                                    return;
                                }

                                list.push(item.emailAddress);
                            });
                        }

                        this.reRender();
                    }
                });
            }
        },

        setupOptions: function () {
            if (this.model.get('assignedUserId') === this.getUser().id) {
                this.params.options = this.getUser().get('userEmailAddressList');
            }
        },

    });
});
