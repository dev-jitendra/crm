

import LinkMultipleFieldView from 'views/fields/link-multiple';

class LinkMultipleWithStatusFieldView extends LinkMultipleFieldView {

    setup() {
        super.setup();

        this.columnsName = this.name + 'Columns';
        this.columns = Espo.Utils.cloneDeep(this.model.get(this.columnsName) || {});

        this.listenTo(this.model, 'change:' + this.columnsName, () => {
            this.columns = Espo.Utils.cloneDeep(this.model.get(this.columnsName) || {});
        });

        this.statusField = this.getMetadata()
            .get(['entityDefs', this.model.entityType,  'fields', this.name, 'columns', 'status']);

        this.styleMap = this.getMetadata()
            .get(['entityDefs', this.foreignScope, 'fields', this.statusField, 'style']) || {};
    }

    getAttributeList() {
        const list = super.getAttributeList();

        list.push(this.name + 'Columns');

        return list;
    }

    getDetailLinkHtml(id, name) {
        let status = (this.columns[id] || {}).status;

        if (!status) {
            return super.getDetailLinkHtml(id, name);
        }

        let style = this.styleMap[status];

        let targetStyleList = ['success', 'danger'];

        if (!style || !~targetStyleList.indexOf(style)) {
            return super.getDetailLinkHtml(id, name);
        }

        let iconStyle = '';

        if (style === 'success') {
            iconStyle = 'fas fa-check text-success small';
        }
        else if (style === 'danger') {
            iconStyle = 'fas fa-times text-danger small';
        }

        return '<span class="' + iconStyle + '"></span> ' +
            super.getDetailLinkHtml(id, name);
    }
}


export default LinkMultipleWithStatusFieldView;
