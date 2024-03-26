

import ArrayFieldView from 'views/fields/array';

class ArrayIntFieldView extends ArrayFieldView {

    type = 'arrayInt'

    fetchFromDom() {
        let selected = [];

        this.$el.find('.list-group .list-group-item').each((i, el) => {
            let value = $(el).data('value');

            if (typeof value === 'string' || value instanceof String) {
                value = parseInt($(el).data('value'));
            }

            selected.push(value);
        });

        this.selected = selected;
    }

    addValue(value) {
        value = parseInt(value);

        if (isNaN(value)) {
            return;
        }

        super.addValue(value);
    }

    removeValue(value) {
        value = parseInt(value);

        if (isNaN(value)) {
            return;
        }

        let valueInternal = value.toString().replace(/"/g, '\\"');

        this.$list.children('[data-value="' + valueInternal + '"]').remove();

        let index = this.selected.indexOf(value);

        this.selected.splice(index, 1);
        this.trigger('change');
    }
}

export default ArrayIntFieldView;
