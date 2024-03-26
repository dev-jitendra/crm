

define('views/admin/integrations/index', ['view'], function (Dep) {

    return Dep.extend({

        template: 'admin/integrations/index',

        integrationList: null,
        integration: null,

        data: function () {
            return {
                integrationList: this.integrationList,
                integration: this.integration,
            };
        },

        events: {
            'click #integrations-menu a.integration-link': function (e) {
                let name = $(e.currentTarget).data('name');

                this.openIntegration(name);
            },
        },

        setup: function () {
            this.integrationList = Object
                .keys(this.getMetadata().get('integrations') || {})
                .sort((v1, v2) => this.translate(v1, 'titles', 'Integration')
                    .localeCompare(this.translate(v2, 'titles', 'Integration'))
                );

            this.integration = this.options.integration || null;

            this.on('after:render', () => {
                this.renderHeader();

                if (!this.integration) {
                    this.renderDefaultPage();
                } else {
                    this.openIntegration(this.integration);
                }
            });
        },

        openIntegration: function (integration) {
            this.integration = integration;

            this.getRouter().navigate('#Admin/integrations/name=' + integration, {trigger: false});

            var viewName = this.getMetadata().get('integrations.' + integration + '.view') ||
                'views/admin/integrations/' +
                Espo.Utils.camelCaseToHyphen(this.getMetadata().get('integrations.' + integration + '.authMethod'));

            Espo.Ui.notify(' ... ');

            this.createView('content', viewName, {
                fullSelector: '#integration-content',
                integration: integration,
            }, view => {
                this.renderHeader();

                view.render();

                Espo.Ui.notify(false);

                $(window).scrollTop(0);
            });
        },

        renderDefaultPage: function () {
            $('#integration-header').html('').hide();

            let msg;

            if (this.integrationList.length) {
                msg = this.translate('selectIntegration', 'messages', 'Integration');
            } else {
                msg = '<p class="lead">' + this.translate('noIntegrations', 'messages', 'Integration') + '</p>';
            }

            $('#integration-content').html(msg);
        },

        renderHeader: function () {
            if (!this.integration) {
                $('#integration-header').html('');

                return;
            }

            $('#integration-header').show().html(this.translate(this.integration, 'titles', 'Integration'));
        },

        updatePageTitle: function () {
            this.setPageTitle(this.getLanguage().translate('Integrations', 'labels', 'Admin'));
        },
    });
});
