<template>
    <div>
        <br>
        <input type="text" v-model="searchTerm" placeholder="Поиск..." />

        <ul>
            <li v-for="equipment in filteredEquipment" :key="equipment.id">
                <div v-if="editMode === equipment.id">
                    <input type="text" v-model="equipment.name" placeholder="Название оборудования" />
                    <input type="text" v-model="equipment.serial_number" placeholder="Серийный номер" />
                    <input type="text" v-model="equipment.description" placeholder="Примечание" />

                    <button @click="saveEquipment(equipment)">Сохранить</button>
                    <button @click="cancelEdit">Отмена</button>
                </div>
                <div class="eq" v-else>
                    <span>Тип оборудования: {{ equipment.equipment_type.name }}</span>
                    <br>
                    <span>Серийный номер: {{ equipment.serial_number }}</span>
                    <br>
                    <span>Примечание: {{ equipment.description }}</span>
                    <br>
                    <button @click="editEquipment(equipment.id)">Редактировать</button>
                    <button @click="deleteEquipment(equipment.id)">Удалить</button>
                </div>
            </li>
        </ul>

        <div v-if="pagination.total > 0" class="pagination">
            <button @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page <= 1">Предыдущая</button>
            <span>{{ pagination.current_page }} из {{ pagination.last_page }}</span>
            <button @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page >= pagination.last_page">Следующая</button>
        </div>
    </div>
</template>

<script>

import axios from 'axios';

export default {
    data() {
        return {
            equipmentList: [],  
            searchTerm: "",      
            loading: true,       
            editMode: null,      
            pagination: {       
                current_page: 1,
                last_page: 1,
                total: 0,
            },
        };
    },
    computed: {
        filteredEquipment() {
            if (this.loading) return [];
            const searchTerm = this.searchTerm.toLowerCase();
            return this.equipmentList.filter(equipment =>
            (equipment.equipment_type?.name?.toLowerCase().includes(searchTerm) ||
                equipment.serial_number?.toLowerCase().includes(searchTerm) ||
                equipment.description?.toLowerCase().includes(searchTerm))
            );
        },
    },
    methods: {
        fetchEquipment(page = 1) {
            this.loading = true;
            axios.get('/api/equipment', { params: { page, q: this.searchTerm } })
                .then(response => {
                    if (Array.isArray(response.data.data)) {
                        this.equipmentList = response.data.data;
                        this.pagination = response.data.meta;
                    } else {
                        console.error("Полученные данные не являются массивом:", response.data);
                        this.equipmentList = [];
                    }
                    this.loading = false;
                })
                .catch(error => {
                    console.error("Ошибка при получении оборудования:", error);
                    this.equipmentList = [];
                    this.loading = false;
                });
        },
        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.pagination.current_page = page;
                this.fetchEquipment(page);
            }
        },
        editEquipment(id) {
            this.editMode = id;
        },
        cancelEdit() {
            this.editMode = null;
            this.fetchEquipment(this.pagination.current_page);
        },
        saveEquipment(equipment) {
            axios.put(`/api/equipment/${equipment.id}`, equipment)
                .then(() => {
                    this.editMode = null;
                    this.fetchEquipment(this.pagination.current_page); 
                })
                .catch(error => {
                    console.error("Ошибка при сохранении оборудования:", error);
                });
        },
        deleteEquipment(id) {
            if (confirm("Вы уверены, что хотите удалить это оборудование?")) {
                axios.delete(`/api/equipment/${id}`)
                    .then(() => {
                        this.equipmentList = this.equipmentList.filter(equipment => equipment.id !== id);
                    })
                    .catch(error => {
                        console.error("Ошибка при удалении оборудования:", error);
                    });
            }
        },
    },
    mounted() {
        this.fetchEquipment(); 
    },
};
</script>

<style scoped>
.pagination {
    margin-top: 20px;
    text-align: center;
}

.pagination button {
    padding: 5px 10px;
    margin: 0 5px;
    cursor: pointer;
}

.pagination button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}
</style>