import Vue from 'vue';

import AdminLogin from '../components/admin/AdminLogin';
import Datatable from '../components/admin/Datatable';
import Alert from '../components/common/Alert';
import PhotoModal from '../components/common/PhotoModal';
import DeleteModal from '../components/common/DeleteModal';
import Logout from '../components/auth/Logout';

new Vue({
    el: "#app",
    components: {
        AdminLogin,
        Datatable,
        Alert,
        PhotoModal,
        DeleteModal,
        Logout,
    },
});