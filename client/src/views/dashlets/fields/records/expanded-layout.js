

import BaseFieldView from 'views/fields/base';
import MultiSelect from 'ui/multi-select';

class ExpandedLayoutDashletFieldView extends BaseFieldView {

    listTemplate = 'dashlets/fields/records/expanded-layout/edit'
    detailTemplate = 'dashlets/fields/records/expanded-layout/edit'
    editTemplate ='dashlets/fields/records/expanded-layout/edit'

    delimiter = ':,:'

    getRowHtml(row, i) {
        row = row || [];

        let list = [];

        row.forEach(item => {
            list.push(item.name);
        });

        return $('<div>')
            .append(
                $('<input>')
                    .attr('type', 'text')
                    .addClass('row-' + i.toString())
                    .attr('value', list.join(this.delimiter))
            )
            .get(0).outerHTML;
    }

    afterRender() {
        this.$container = this.$el.find('>.layout-container');

        let rowList = (this.model.get(this.name) || {}).rows || [];

        rowList = Espo.Utils.cloneDeep(rowList);

        rowList.push([]);

        let fieldDataList = this.getFieldDataList();

        rowList.forEach((row, i) => {
            let rowHtml = this.getRowHtml(row, i);
            let $row = $(rowHtml);

            this.$container.append($row);

            let $input = $row.find('input');

            
            let multiSelectOptions = {
                items: fieldDataList,
                delimiter: this.delimiter,
                matchAnyWord: this.matchAnyWord,
                draggable: true,
            };

            MultiSelect.init($input, multiSelectOptions);

            $input.on('change', () => {
                this.trigger('change');
                this.reRender();
            });
        });
    }

    getFieldDataList() {
        const scope = this.model.get('entityType') ||
            this.getMetadata().get(['dashlets', this.dataObject.dashletName, 'entityType']);

        if (!scope) {
            return [];
        }

        let fields = this.getMetadata().get(['entityDefs', scope, 'fields']) || {};

        let forbiddenFieldList = this.getAcl().getScopeForbiddenFieldList(scope);

        let fieldList = Object.keys(fields)
            .sort((v1, v2) => {
                 return this.translate(v1, 'fields', scope)
                     .localeCompare(this.translate(v2, 'fields', scope));
            })
            .filter(item => {
                if (
                    fields[item].disabled ||
                    fields[item].listLayoutDisabled ||
                    fields[item].utility
                ) {
                    return false;
                }

                if (
                    fields[item].layoutAvailabilityList &&
                    !fields[item].layoutAvailabilityList.includes('list')
                ) {
                    return false;
                }

                if (forbiddenFieldList.indexOf(item) !== -1) {
                    return false;
                }

                return true;
            });

        let dataList = [];

        fieldList.forEach(item => {
            dataList.push({
                value: item,
                text: this.translate(item, 'fields', scope),
            });
        });

        return dataList;
    }

    fetch() {
        var value = {
            rows: [],
        };

        this.$el.find('input').each((i, el) => {
            let row = [];
            let list = ($(el).val() || '').split(this.delimiter);

            if (list.length === 1 && list[0] === '') {
                list = [];
            }

            if (list.length === 0) {
                return;
            }

            list.forEach(item => {
                let o = {name: item};

                if (item === 'name') {
                    o.link = true;
                }

                row.push(o);
            });

            value.rows.push(row);
        });

        let data = {};

        data[this.name] = value;

        return data;
    }
}

export default ExpandedLayoutDashletFieldView;
