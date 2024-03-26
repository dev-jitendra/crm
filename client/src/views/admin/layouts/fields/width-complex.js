

import BaseFieldView from 'views/fields/base';
import EnumFieldView from 'views/fields/enum';
import Model from 'model';
import FloatFieldView from 'views/fields/float';

class LayoutWidthComplexFieldView extends BaseFieldView {

    editTemplateContent = `
        <div class="row">
            <div data-name="value" class="col-sm-6">{{{value}}}</div>
            <div data-name="unit" class="col-sm-6">{{{unit}}}</div>
        </div>

    `
    getAttributeList() {
        return ['width', 'widthPx'];
    }

    setup() {
        this.auxModel = new Model();

        this.syncAuxModel();
        this.listenTo(this.model, 'change', () => this.syncAuxModel());

        const unitView = new EnumFieldView({
            name: 'unit',
            mode: 'edit',
            model: this.auxModel,
            params: {
                options: [
                    '%',
                    'px',
                ],
            },
        });

        const valueView = new FloatFieldView({
            name: 'value',
            mode: 'edit',
            model: this.auxModel,
        });

        this.assignView('unit', unitView, '[data-name="unit"]');
        this.assignView('value', valueView, '[data-name="value"]');

        this.listenTo(this.auxModel, 'change', (m, o) => {
            if (!o.ui) {
                return;
            }

            this.model.set(this.fetch(), {ui: true});
        });
    }

    fetch() {
        if (this.auxModel.get('unit') === 'px') {
            return {
                width: null,
                widthPx: this.auxModel.get('value'),
            }
        }

        return {
            width: this.auxModel.get('value'),
            widthPx: null,
        };
    }

    syncAuxModel() {
        const width = this.model.get('width');
        const widthPx = this.model.get('widthPx');

        const unit = width || !widthPx ? '%' : 'px';

        this.auxModel.set({
            unit: unit,
            value: unit === 'px' ? widthPx : width,
        });
    }
}


export default LayoutWidthComplexFieldView;
