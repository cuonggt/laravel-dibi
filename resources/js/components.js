import Vue from 'vue';

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

// Icons
Vue.component('IconChevronLeft', IconChevronLeft);
Vue.component('IconChevronRight', IconChevronRight);
Vue.component('IconCog', IconCog);
Vue.component('IconDatabase', IconDatabase);
Vue.component('IconEye', IconEye);
Vue.component('IconLoader', IconLoader);
Vue.component('IconTable', IconTable);

// Components
Vue.component('XButton', XButton);
Vue.component('XDialogModal', XDialogModal);
Vue.component('XInput', XInput);
Vue.component('XLabel', XLabel);
Vue.component('XLoader', XLoader);
Vue.component('XSecondaryButton', XSecondaryButton);

Vue.component('SideNavigation', SideNavigation);
Vue.component('TableStructure', TableStructure);
Vue.component('DataTable', DataTable);
Vue.component('DataCell', DataCell);
Vue.component('SqlEditor', SqlEditor);
