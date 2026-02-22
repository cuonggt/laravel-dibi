import IconChevronLeft from './components/icons/ChevronLeft.vue';
import IconChevronRight from './components/icons/ChevronRight.vue';
import IconCog from './components/icons/Cog.vue';
import IconDatabase from './components/icons/Database.vue';
import IconEye from './components/icons/Eye.vue';
import IconLoader from './components/icons/Loader.vue';
import IconTable from './components/icons/Table.vue';

import XButton from './components/Button.vue';
import XDialogModal from './components/DialogModal.vue';
import XInput from './components/Input.vue';
import XLabel from './components/Label.vue';
import XLoader from './components/Loader.vue';
import XSecondaryButton from './components/SecondaryButton.vue';

import SideNavigation from './components/SideNavigation.vue';
import TableStructure from './components/TableStructure.vue';
import DataTable from './components/DataTable.vue';
import DataCell from './components/DataCell.vue';
import SqlEditor from './components/SqlEditor.vue';

export function registerComponents(app) {
    // Icons
    app.component('IconChevronLeft', IconChevronLeft);
    app.component('IconChevronRight', IconChevronRight);
    app.component('IconCog', IconCog);
    app.component('IconDatabase', IconDatabase);
    app.component('IconEye', IconEye);
    app.component('IconLoader', IconLoader);
    app.component('IconTable', IconTable);

    // Components
    app.component('XButton', XButton);
    app.component('XDialogModal', XDialogModal);
    app.component('XInput', XInput);
    app.component('XLabel', XLabel);
    app.component('XLoader', XLoader);
    app.component('XSecondaryButton', XSecondaryButton);

    app.component('SideNavigation', SideNavigation);
    app.component('TableStructure', TableStructure);
    app.component('DataTable', DataTable);
    app.component('DataCell', DataCell);
    app.component('SqlEditor', SqlEditor);
}
