

import BaseRecordView from 'views/record/base';

class EntityManagerEditFormulaRecordView extends BaseRecordView {

    template = 'admin/entity-manager/record/edit-formula'

    data() {
        return {
            field: this.field,
            fieldKey: this.field + 'Field',
        };
    }

    setup() {
        super.setup();

        this.field = this.options.type;

        let additionalFunctionDataList = null;

        if (this.options.type === 'beforeSaveApiScript') {
            additionalFunctionDataList = this.getRecordServiceFunctionDataList();
        }

        this.createField(
            this.field,
            'views/fields/formula',
            {
                targetEntityType: this.options.targetEntityType,
                height: 500,
            },
            'edit',
            false,
            {additionalFunctionDataList: additionalFunctionDataList}
        );
    }

    getRecordServiceFunctionDataList() {
        return [
            {
                name: 'recordService\\skipDuplicateCheck',
                insertText: 'recordService\\skipDuplicateCheck()',
                returnType: 'bool'
            },
            {
                name: 'recordService\\throwDuplicateConflict',
                insertText: 'recordService\\throwDuplicateConflict(RECORD_ID)',
            },
            {
                name: 'recordService\\throwBadRequest',
                insertText: 'recordService\\throwBadRequest(MESSAGE)',
            },
            {
                name: 'recordService\\throwForbidden',
                insertText: 'recordService\\throwForbidden(MESSAGE)',
            },
            {
                name: 'recordService\\throwConflict',
                insertText: 'recordService\\throwConflict(MESSAGE)',
            },
        ];
    }
}

export default EntityManagerEditFormulaRecordView;
