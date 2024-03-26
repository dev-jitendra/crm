

define('views/admin/entity-manager/scope', ['view'], function (Dep) {

    return Dep.extend({

        template: 'admin/entity-manager/scope',

        scope: null,

        data: function () {
            return {
                scope: this.scope,
                isEditable: this.isEditable,
                isRemovable: this.isRemovable,
                isCustomizable: this.isCustomizable,
                type: this.type,
                hasLayouts: this.hasLayouts,
                label: this.label,
                hasFormula: this.hasFormula,
                hasFields: this.hasFields,
                hasRelationships: this.hasRelationships,
            };
        },

        events: {
            'click [data-action="editEntity"]': function () {
                this.getRouter().navigate('#Admin/entityManager/edit&scope=' + this.scope, {trigger: true});
            },
            'click [data-action="removeEntity"]': function () {
                this.removeEntity();
            },
            'click [data-action="editFormula"]': function () {
                this.editFormula();
            },
        },

        setup: function () {
            this.scope = this.options.scope;

            this.setupScopeData();
        },

        setupScopeData: function () {
            let scopeData = this.getMetadata().get(['scopes', this.scope]);
            let entityManagerData = this.getMetadata().get(['scopes', this.scope, 'entityManager']) || {};

            if (!scopeData) {
                throw new Espo.Exceptions.NotFound();
            }

            this.isRemovable = !!scopeData.isCustom;

            if (scopeData.isNotRemovable) {
                this.isRemovable = false;
            }

            this.isCustomizable = !!scopeData.customizable;
            this.type = scopeData.type;
            this.isEditable = true;
            this.hasLayouts = scopeData.layouts;
            this.hasFormula = this.isCustomizable;
            this.hasFields = this.isCustomizable;
            this.hasRelationships = this.isCustomizable;

            if (!scopeData.customizable) {
                this.isEditable = false;
            }

            if ('edit' in entityManagerData) {
                this.isEditable = entityManagerData.edit;
            }

            if ('layouts' in entityManagerData) {
                this.hasLayouts = entityManagerData.layouts;
            }

            if ('formula' in entityManagerData) {
                this.hasFormula = entityManagerData.formula;
            }

            if ('fields' in entityManagerData) {
                this.hasFields = entityManagerData.fields;
            }

            if ('relationships' in entityManagerData) {
                this.hasRelationships = entityManagerData.relationships;
            }

            this.label = this.getLanguage().translate(this.scope, 'scopeNames');
        },

        editFormula: function () {
            Espo.Ui.notify(' ... ');

            Espo.loader.requirePromise('views/admin/entity-manager/modals/select-formula')
                .then(View => {
                    
                    let view = new View({
                        scope: this.scope,
                    });

                    this.assignView('dialog', view).then(() => {
                        Espo.Ui.notify(false);

                        view.render();
                    });
                });
        },

        removeEntity: function () {
            var scope = this.scope;

            this.confirm(this.translate('confirmRemove', 'messages', 'EntityManager'), () => {
                Espo.Ui.notify(
                    this.translate('pleaseWait', 'messages')
                );

                this.disableButtons();

                Espo.Ajax.postRequest('EntityManager/action/removeEntity', {
                    name: scope,
                })
                .then(() => {
                    this.getMetadata()
                        .loadSkipCache()
                        .then(() => {
                            this.getConfig().load().then(() => {
                                Espo.Ui.notify(false);

                                this.broadcastUpdate();

                                this.getRouter().navigate('#Admin/entityManager', {trigger: true});
                            });
                        });
                })
                .catch(() => this.enableButtons());
            });
        },

        updatePageTitle: function () {
            this.setPageTitle(
                this.getLanguage().translate('Entity Manager', 'labels', 'Admin')
            );
        },

        disableButtons: function () {
            this.$el.find('.btn.action').addClass('disabled').attr('disabled', 'disabled');
            this.$el.find('.item-dropdown-button').addClass('disabled').attr('disabled', 'disabled');
        },

        enableButtons: function () {
            this.$el.find('.btn.action').removeClass('disabled').removeAttr('disabled');
            this.$el.find('.item-dropdown-button"]').removeClass('disabled').removeAttr('disabled');
        },

        broadcastUpdate: function () {
            this.getHelper().broadcastChannel.postMessage('update:metadata');
            this.getHelper().broadcastChannel.postMessage('update:settings');
        },
    });
});
