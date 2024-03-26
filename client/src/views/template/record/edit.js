

define('views/template/record/edit', ['views/record/edit'], function (Dep) {

    return Dep.extend({

        saveAndContinueEditingAction: true,

        setup: function () {
            Dep.prototype.setup.call(this);

            if (!this.model.isNew()) {
                this.setFieldReadOnly('entityType');
            }

            if (this.model.get('entityType')) {
                this.showField('variables');
            }
            else {
                this.hideField('variables');
            }

            if (this.model.isNew()) {
                var storedData = {};

                this.listenTo(this.model, 'change:entityType', function (model) {
                    var entityType = this.model.get('entityType');

                    if (!entityType) {
                        this.model.set('header', '');
                        this.model.set('body', '');
                        this.model.set('footer', '');

                        this.hideField('variables');

                        return;
                    }
                    this.showField('variables');

                    if (entityType in storedData) {
                        this.model.set('header', storedData[entityType].header);
                        this.model.set('body', storedData[entityType].body);
                        this.model.set('footer', storedData[entityType].footer);

                        return;
                    }

                    var header, body, footer;

                    var sourceType = null;

                    if (
                        this.getMetadata().get(['entityDefs', 'Template', 'defaultTemplates', entityType])
                    ) {
                        var sourceType = entityType;
                    }
                    else {
                        var scopeType = this.getMetadata().get(['scopes', entityType, 'type']);

                        if (
                            scopeType &&
                            this.getMetadata().get(['entityDefs', 'Template', 'defaultTemplates', scopeType])
                        ) {

                            var sourceType = scopeType;
                        }
                    }

                    if (sourceType) {
                        header = this.getMetadata().get(
                            ['entityDefs', 'Template', 'defaultTemplates', sourceType, 'header']
                        );

                        body = this.getMetadata().get(
                            ['entityDefs', 'Template', 'defaultTemplates', sourceType, 'body']
                        );

                        footer = this.getMetadata().get(
                            ['entityDefs', 'Template', 'defaultTemplates', sourceType, 'footer']
                        );
                    }

                    body = body || '';
                    header = header || null;
                    footer = footer || null;

                    this.model.set('body', body);
                    this.model.set('header', header);
                    this.model.set('footer', footer);
                }, this);

                this.listenTo(this.model, 'change', function (e, o) {
                    if (!o.ui) {
                        return;
                    }

                    if (
                        !this.model.hasChanged('header') &&
                        !this.model.hasChanged('body') &&
                        !this.model.hasChanged('footer')
                    ) {
                        return;
                    }

                    var entityType = this.model.get('entityType');

                    if (!entityType) {
                        return;
                    }

                    storedData[entityType] = {
                        header: this.model.get('header'),
                        body: this.model.get('body'),
                        footer: this.model.get('footer'),
                    };
                }, this);
            }
        },

    });
});
