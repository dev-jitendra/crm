



import DetailSideRecordView from 'views/record/detail-side';

class EditSideRecordView extends DetailSideRecordView {

    
    mode = 'edit'

    
    defaultPanelDefs = {
        name: 'default',
        label: false,
        view: 'views/record/panels/side',
        isForm: true,
        options: {
            fieldList: [
                {
                    name: ':assignedUser'
                },
                {
                    name: 'teams',
                    view: 'views/fields/teams'
                }
            ]
        }
    }
}

export default EditSideRecordView;
