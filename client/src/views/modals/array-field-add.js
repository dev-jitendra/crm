

import ModalView from 'views/modal';

class ArrayFieldAddModalView extends ModalView {

    template = 'modals/array-field-add'

    cssName = 'add-modal'
    backdrop = true

    data() {
        return {
            optionList: this.optionList,
            translatedOptions: this.translations,
        };
    }

    events = {
        
        'click .add': function (e) {
            const value = $(e.currentTarget).attr('data-value');

            this.trigger('add', value);
        },
        
        'click input[type="checkbox"]': function (e) {
            const value = $(e.currentTarget).attr('data-value');

            if (e.target.checked) {
                this.checkedList.push(value);
            } else {
                const index = this.checkedList.indexOf(value);

                if (index !== -1) {
                    this.checkedList.splice(index, 1);
                }
            }

            this.checkedList.length ?
                this.enableButton('select') :
                this.disableButton('select');
        },
        
        'keyup input[data-name="quick-search"]': function (e) {
            this.processQuickSearch(e.currentTarget.value);
        },
    }

    setup() {
        this.headerText = this.translate('Add Item');
        this.checkedList = [];
        this.translations = Espo.Utils.clone(this.options.translatedOptions || {});
        this.optionList = this.options.options || [];

        this.optionList.forEach(item => {
            if (item in this.translations) {
                return;
            }

            this.translations[item] = item;
        });

        this.buttonList = [
            {
                name: 'select',
                style: 'danger',
                label: 'Select',
                disabled: true,
                onClick: () => {
                    this.trigger('add-mass', this.checkedList);
                },
            },
            {
                name: 'cancel',
                label: 'Cancel',
            },
        ];
    }

    afterRender() {
        this.$noData = this.$el.find('.no-data');

        setTimeout(() => {
            this.$el.find('input[data-name="quick-search"]').focus();
        }, 100);
    }

    processQuickSearch(text) {
        text = text.trim();

        const $noData = this.$noData;

        $noData.addClass('hidden');

        if (!text) {
            this.$el.find('ul .list-group-item').removeClass('hidden');

            return;
        }

        const matchedList = [];

        const lowerCaseText = text.toLowerCase();

        this.optionList.forEach(item => {
            const label = this.translations[item].toLowerCase();

            for (const word of label.split(' ')) {
                const matched = word.indexOf(lowerCaseText) === 0;

                if (matched) {
                    matchedList.push(item);

                    return;
                }
            }
        });

        if (matchedList.length === 0) {
            this.$el.find('ul .list-group-item').addClass('hidden');

            $noData.removeClass('hidden');

            return;
        }

        this.optionList.forEach(item => {
            const $row = this.$el.find(`ul .list-group-item[data-name="${item}"]`);

            if (!~matchedList.indexOf(item)) {
                $row.addClass('hidden');

                return;
            }

            $row.removeClass('hidden');
        });
    }
}

export default ArrayFieldAddModalView;
