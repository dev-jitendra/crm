

import VarcharFieldView from 'views/fields/varchar';

class ColorpickerFieldView extends VarcharFieldView {

    type = 'varchar'

    detailTemplate = 'fields/colorpicker/detail'
    listTemplate = 'fields/colorpicker/detail'
    editTemplate = 'fields/colorpicker/edit'

    setup() {
        super.setup();

        this.wait(Espo.loader.requirePromise('lib!bootstrap-colorpicker'));
    }

    afterRender() {
        super.afterRender();

        if (this.isEditMode()) {
            let isModal = !!this.$el.closest('.modal').length;

            
            this.$element.parent().colorpicker({
                format: 'hex',
                container: isModal ? this.$el : false,
            });

            if (isModal) {
                this.$el.find('.colorpicker')
                    .css('position', 'relative')
                    .addClass('pull-right');
            }

            this.$element.on('change', () => {
                if (this.$element.val() === '') {
                    this.$el.find('.input-group-addon > i').css('background-color', 'transparent');
                }
            });
        }
    }
}

export default ColorpickerFieldView;
