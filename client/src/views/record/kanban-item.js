

import View from 'view';

class KanbanRecordItem extends View {

    template = 'record/kanban-item'

    data() {
        return {
            layoutDataList: this.layoutDataList,
            rowActionsDisabled: this.rowActionsDisabled,
        };
    }

    events = {}

    setup() {
        this.itemLayout = this.options.itemLayout;
        this.rowActionsView = this.options.rowActionsView;
        this.rowActionsDisabled = this.options.rowActionsDisabled;

        this.layoutDataList = [];

        this.itemLayout.forEach((item, i) => {
            const name = item.name;
            const key = name + 'Field';

            const o = {
                name: name,
                isAlignRight: item.align === 'right',
                isLarge: item.isLarge,
                isFirst: i === 0,
                key: key,
            };

            this.layoutDataList.push(o);

            let viewName = item.view || this.model.getFieldParam(name, 'view');

            if (!viewName) {
                const type = this.model.getFieldType(name) || 'base';

                viewName = this.getFieldManager().getViewName(type);
            }

            let mode = 'list';

            if (item.link) {
                mode = 'listLink';
            }

            this.createView(key, viewName, {
                model: this.model,
                name: name,
                mode: mode,
                readOnly: true,
                selector: '.field[data-name="'+name+'"]',
            });
        });

        if (!this.rowActionsDisabled) {
            const acl = {
                edit: this.getAcl().checkModel(this.model, 'edit'),
                delete: this.getAcl().checkModel(this.model, 'delete'),
            };

            this.createView('itemMenu', this.rowActionsView, {
                selector: '.item-menu-container',
                model: this.model,
                acl: acl,
                statusFieldIsEditable: this.options.statusFieldIsEditable,
                rowActionHandlers: this.options.rowActionHandlers || {},
                additionalActionList: this.options.additionalRowActionList,
                scope: this.options.scope,
            });
        }
    }
}

export default KanbanRecordItem;
