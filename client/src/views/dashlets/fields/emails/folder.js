

import EnumFieldView from 'views/fields/enum';

class EmailFolderDashletFieldView extends EnumFieldView {

    
    folderDataList

    setup() {
        super.setup();

        let userId = this.dataObject.userId ?? this.getUser().id;

        this.wait(
            Espo.Ajax.getRequest('EmailFolder/action/listAll', {userId: userId})
                .then(data => this.folderDataList = data.list)
                .then(() => this.setupOptions())
        );

        this.setupOptions();
    }

    setupOptions() {
        if (!this.folderDataList) {
            return;
        }

        this.params.options = this.folderDataList
            .map(item => item.id)
            .filter(item => item !== 'inbox' && item !== 'trash');

        this.params.options.unshift('');

        this.translatedOptions = {'': this.translate('inbox', 'presetFilters', 'Email')};

        this.folderDataList.forEach(item => {
            this.translatedOptions[item.id] = item.name;
        });
    }
}

export default EmailFolderDashletFieldView;
