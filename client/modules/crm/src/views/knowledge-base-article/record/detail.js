

import KnowledgeBaseHelper from 'modules/crm/knowledge-base-helper';
import Dep from 'views/record/detail';


export default Dep.extend({

    saveAndContinueEditingAction: true,

    setup: function () {
        Dep.prototype.setup.call(this);

        if (this.getUser().isPortal()) {
            this.sideDisabled = true;
        }

        if (this.getAcl().checkScope('Email', 'create')) {
            this.dropdownItemList.push({
                'label': 'Send in Email',
                'name': 'sendInEmail',
            });
        }

        if (this.getUser().isPortal()) {
            if (!this.getAcl().checkScope(this.scope, 'edit')) {
                if (!this.model.getLinkMultipleIdList('attachments').length) {
                    this.hideField('attachments');

                    this.listenToOnce(this.model, 'sync', () => {
                        if (this.model.getLinkMultipleIdList('attachments').length) {
                            this.showField('attachments');
                        }
                    });
                }
            }
        }
    },

    actionSendInEmail: function () {
        Espo.Ui.notify(this.translate('pleaseWait', 'messages'));

        let helper = new KnowledgeBaseHelper(this.getLanguage());

        helper.getAttributesForEmail(this.model, {}, attributes => {
            let viewName = this.getMetadata().get('clientDefs.Email.modalViews.compose') ||
                'views/modals/compose-email';

            this.createView('composeEmail', viewName, {
                attributes: attributes,
                selectTemplateDisabled: true,
                signatureDisabled: true,
            }, view => {
                Espo.Ui.notify(false);

                view.render();
            });
        });
    },

    afterRender: function () {
        Dep.prototype.afterRender.call(this);

        if (this.getUser().isPortal()) {
            this.$el.find('.field[data-name="body"]').css('minHeight', '400px');
        }
    },
});
