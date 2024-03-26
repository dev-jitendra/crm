

import MultiEnumFieldView from 'views/fields/multi-enum';
import ForeignArrayFieldView from 'views/fields/foreign-array';

class ForeignMultiEnumFieldView extends MultiEnumFieldView {

    type = 'foreign'

    setupOptions() {
        ForeignArrayFieldView.prototype.setupOptions.call(this);
    }
}

export default ForeignMultiEnumFieldView;
