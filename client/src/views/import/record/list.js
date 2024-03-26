

import ListRecordView from 'views/record/list';

class ImportListRecordView extends ListRecordView {

    quickDetailDisabled = true
    quickEditDisabled = true
    checkAllResultDisabled = true
    massActionList = ['remove']
    rowActionsView = 'views/record/row-actions/remove-only'
}

export default ImportListRecordView;
