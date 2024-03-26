

import View from 'view';


class DefaultRowActionsView extends View {

    template = 'record/row-actions/default'

    setup() {
        this.options.acl = this.options.acl || {};
        this.scope = this.options.scope || this.model.entityType;

        
        this.handlers = this.options.rowActionHandlers || {};

        
        this.additionalActionDataList = [];

        this.setupAdditionalActions();
    }

    afterRender() {
        const $dd = this.$el.find('button[data-toggle="dropdown"]').parent();

        let isChecked = false;

        $dd.on('show.bs.dropdown', () => {
            const $el = this.$el.closest('.list-row');

            isChecked = false;

            if ($el.hasClass('active')) {
                isChecked = true;
            }

            $el.addClass('active');
        });

        $dd.on('hide.bs.dropdown', () => {
            if (!isChecked) {
                this.$el.closest('.list-row').removeClass('active');
            }
        });
    }

    
    getActionList() {
        
        const list = [{
            action: 'quickView',
            label: 'View',
            data: {
                id: this.model.id
            },
            link: '#' + this.model.entityType + '/view/' + this.model.id,
        }];

        if (this.options.acl.edit) {
            list.push({
                action: 'quickEdit',
                label: 'Edit',
                data: {
                    id: this.model.id
                },
                link: '#' + this.model.entityType + '/edit/' + this.model.id,
            });
        }

        this.getAdditionalActionList().forEach(item => list.push(item));

        if (this.options.acl.delete) {
            list.push({
                action: 'quickRemove',
                label: 'Remove',
                data: {
                    id: this.model.id,
                },
            });
        }

        return list;
    }

    getAdditionalActionList() {
        const list = [];

        this.additionalActionDataList.forEach(item => {
            const handler = this.handlers[item.name];

            if (handler && !handler.isAvailable(this.model, item.name)) {
                return;
            }

            if (item.acl && item.acl !== 'read' && !this.options.acl[item.acl]) {
                return;
            }

            list.push({
                action: 'rowAction',
                text: item.text,
                data: {
                    id: this.model.id,
                    actualAction: item.name,
                },
            });
        });

        return list;
    }

    data() {
        return {
            acl: this.options.acl,
            actionList: this.getActionList(),
            scope: this.model.entityType,
        };
    }

    setupAdditionalActions() {
        
        const list = this.options.additionalActionList;

        if (!list) {
            return;
        }

        const defs = this.getMetadata().get(`clientDefs.${this.scope}.rowActionDefs`) || {};

        list.forEach(action => {
            
            const itemDefs = defs[action] || {};

            const text = itemDefs.labelTranslation ?
                this.getLanguage().translatePath(itemDefs.labelTranslation) :
                this.getLanguage().translate(itemDefs.label, 'labels', this.model.entityType);

            this.additionalActionDataList.push({
                name: action,
                acl:  itemDefs.acl,
                text: text,
            });
        });
    }
}

export default DefaultRowActionsView;
