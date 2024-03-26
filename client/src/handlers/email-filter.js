

import DynamicHandler from 'dynamic-handler';

class EmailFilterHandler extends DynamicHandler {

    init() {
        if (this.model.isNew()) {
            if (!this.recordView.getUser().isAdmin()) {
                this.recordView.hideField('isGlobal');
            }
        }

        if (
            !this.model.isNew() &&
            !this.recordView.getUser().isAdmin() &&
            !this.model.get('isGlobal')
        ) {
            this.recordView.hideField('isGlobal');
        }

        if (this.model.isNew() && !this.model.get('parentId')) {
            this.model.set('parentType', 'User');
            this.model.set('parentId', this.recordView.getUser().id);
            this.model.set('parentName', this.recordView.getUser().get('name'));

            if (!this.recordView.getUser().isAdmin()) {
                this.recordView.setFieldReadOnly('parent');
            }
        }
        else if (
            this.model.get('parentType') &&
            !this.recordView.options.duplicateSourceId
        ) {
            this.recordView.setFieldReadOnly('parent');
            this.recordView.setFieldReadOnly('isGlobal');
        }

        this.recordView.listenTo(this.model, 'change:isGlobal', (model, value, o) => {
            if (!o.ui) {
                return;
            }

            if (value) {
                this.model.set({
                    action: 'Skip',
                    parentName: null,
                    parentType: null,
                    parentId: null,
                    emailFolderId: null,
                    groupEmailFolderId: null,
                    markAsRead: false,
                });
            }
        });

        this.recordView.listenTo(this.model, 'change:parentType', (model, value, o) => {
            if (!o.ui) {
                return;
            }

            
            setTimeout(() => {
                if (value !== 'User') {
                    this.model.set('markAsRead', false);
                }

                if (value === 'EmailAccount') {
                    this.model.set('action', 'Skip');
                    this.model.set('emailFolderId', null);
                    this.model.set('groupEmailFolderId', null);
                    this.model.set('markAsRead', false);

                    return;
                }

                if (value !== 'InboundEmail') {
                    if (this.model.get('action') === 'Move to Group Folder') {
                        this.model.set('action', 'Skip');
                    }

                    this.model.set('groupEmailFolderId', null);

                    return;
                }

                if (value !== 'User') {
                    if (this.model.get('action') === 'Move to Folder') {
                        this.model.set('action', 'Skip');
                    }

                    this.model.set('groupFolderId', null);
                }
            }, 40);
        });
    }
}

export default EmailFilterHandler;

