import { createRouter, createWebHistory } from 'vue-router';
import EquipmentList from '../components/EquipmentList.vue';
import EquipmentForm from '../components/EquipmentForm.vue';

const routes = [
  {
    path: '/',
    name: 'equipment-list',
    component: EquipmentList
  },
  {
    path: '/equipment/new',
    name: 'equipment-form',
    component: EquipmentForm
  },
  
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;
