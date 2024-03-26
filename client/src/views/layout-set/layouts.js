

import LayoutIndexView from 'views/admin/layouts/index';

class LayoutsView extends LayoutIndexView {

    setup() {
        let setId = this.setId = this.options.layoutSetId;
        this.baseUrl = '#LayoutSet/editLayouts/id=' + setId;

        super.setup();

        this.wait(
            this.getModelFactory()
                .create('LayoutSet')
                .then(m => {
                    this.sModel = m;
                    m.id = setId;

                    return m.fetch();
                })
        );
    }

    getLayoutScopeDataList() {
        let dataList = [];
        let list = this.sModel.get('layoutList') || [];

        let scopeList = [];

        list.forEach(item => {
            let arr = item.split('.');
            let scope = arr[0];

            if (~scopeList.indexOf(scope)) {
                return;
            }

            scopeList.push(scope);
        });

        scopeList.forEach(scope => {
            let o = {};

            o.scope = scope;
            o.url = this.baseUrl + '&scope=' + scope;
            o.typeDataList = [];

            let typeList = [];

            list.forEach(item => {
                let [scope, type] = item.split('.');

                if (scope !== o.scope) {
                    return;
                }

                typeList.push(type);
            });

            typeList.forEach(type => {
                o.typeDataList.push({
                    type: type,
                    url: this.baseUrl + '&scope=' + scope + '&type=' + type,
                    label: this.translateLayoutName(type, scope),
                });
            });

            o.typeList = typeList;

            dataList.push(o);
        });

        return dataList;
    }

    getHeaderHtml() {
        const separatorHtml = ' <span class="breadcrumb-separator"><span class="chevron-right"></span></span> ';

        return $('<span>')
            .append(
                $('<a>')
                    .attr('href', '#LayoutSet')
                    .text(this.translate('LayoutSet', 'scopeNamesPlural')),
                separatorHtml,
                $('<a>')
                    .attr('href', '#LayoutSet/view/' + this.sModel.id)
                    .text(this.sModel.get('name')),
                separatorHtml,
                $('<span>')
                    .text(this.translate('Edit Layouts', 'labels', 'LayoutSet'))
            )
            .get(0).outerHTML;
    }

    navigate(scope, type) {
        let url = '#LayoutSet/editLayouts/id=' + this.setId + '&scope=' + scope + '&type=' + type;

        this.getRouter().navigate(url, {trigger: false});
    }
}

export default LayoutsView;
