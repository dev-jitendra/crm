

define('views/external-account/index', ['view'], function (Dep) {

    return Dep.extend({

        template: 'external-account/index',

        data: function () {
            return {
                externalAccountList: this.externalAccountList,
                id: this.id,
                externalAccountListCount: this.externalAccountList.length
            };
        },

        events: {
            'click #external-account-menu a.external-account-link': function (e) {
                var id = $(e.currentTarget).data('id') + '__' + this.userId;
                this.openExternalAccount(id);
            },
        },

        setup: function () {
            this.externalAccountList = this.collection.models.map(model => model.getClonedAttributes());

            this.userId = this.getUser().id;
            this.id = this.options.id || null;

            if (this.id) {
                this.userId = this.id.split('__')[1];
            }

            this.on('after:render', function () {
                this.renderHeader();

                if (!this.id) {
                    this.renderDefaultPage();
                } else {
                    this.openExternalAccount(this.id);
                }
            });
        },

        openExternalAccount: function (id) {
            this.id = id;

            var integration = this.integration = id.split('__')[0];
            this.userId = id.split('__')[1];

            this.getRouter().navigate('#ExternalAccount/edit/' + id, {trigger: false});

            var authMethod = this.getMetadata().get(['integrations', integration, 'authMethod']);

            var viewName =
                    this.getMetadata().get(['integrations', integration, 'userView']) ||
                    'views/external-account/' + Espo.Utils.camelCaseToHyphen(authMethod);

            Espo.Ui.notify(' ... ');

            this.createView('content', viewName, {
                fullSelector: '#external-account-content',
                id: id,
                integration: integration
            }, view => {
                this.renderHeader();
                view.render();
                Espo.Ui.notify(false);

                $(window).scrollTop(0);
            });
        },

        renderDefaultPage: function () {
            $('#external-account-header').html('').hide();
            $('#external-account-content').html('');
        },

        renderHeader: function () {
            if (!this.id) {
                $('#external-account-header').html('');
                return;
            }

            $('#external-account-header').show().html(this.integration);
        },

        updatePageTitle: function () {
            this.setPageTitle(this.translate('ExternalAccount', 'scopeNamesPlural'));
        },
    });
});
