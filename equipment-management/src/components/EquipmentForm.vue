<template>
    <div>
        <div>
            <ul>
                <li v-for="type in equipmentTypes.data" :key="type.id">{{ type.name }}</li>

            </ul>
        </div>
        <form @submit.prevent="submitForm">
            <div>
                <label for="equipment_type_id">Тип оборудования:</label>
                <select id="equipment_type_id" v-model="equipment.equipment_type_id" required>
                    <option v-for="type in equipmentTypes" :key="type.id" :value="type.id">
                        {{ type.name }}
                    </option>
                </select>
            </div>
            <div>
                <label for="serial_number">Серийные номера:</label>
                <input type="text" id="serial_number" v-model="equipment.serial_number" required />
            </div>
            <div>
                <label for="description">Примечание:</label>
                <textarea id="description" v-model="equipment.description"></textarea>
            </div>
            
            <button type="submit">{{ isEdit ? 'Сохранить' : 'Добавить' }}</button>
        </form>
    </div>
</template>

<script>
export default {
    data() {
        return {
            isEdit: false,
            equipment: {
                equipment_type_id: null,
                serial_number: '',
                description: '',
            },
            equipmentTypes: [],
        };

    },
    methods: {
        async loadEquipmentTypes() {
            try {
                const response = await fetch('http://localhost:8000/api/equipment-types');
                if (!response.ok) {
                    throw new Error(`Ошибка: ${response.status} ${response.statusText}`);
                }
                const data = await response.json();
                console.log('Типы оборудования:', data); 
                this.equipmentTypes = data.data; 
            } catch (error) {
                console.error('Ошибка при загрузке типов оборудования:', error);
            }
        },
        async submitForm() {
            try {
                const url = this.isEdit
                    ? `http://localhost:8000/api/equipment/${this.equipment.id}`
                    : 'http://localhost:8000/api/equipment';

                const method = this.isEdit ? 'PUT' : 'POST';

                console.log('Данные, отправляемые на сервер:', this.equipment); 

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(this.equipment),
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    console.error('Ошибка при сохранении оборудования:', errorData);
                    throw new Error(`Ошибка: ${response.status} ${response.statusText}`);
                }

                this.$router.push('/');
            } catch (error) {
                console.error('Ошибка при сохранении оборудования:', error);
            }
        },
        loadEquipment(id) {
            if (id) {
                this.isEdit = true;
                fetch(`http://localhost:8000/api/equipment/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        this.equipment = data;
                    });
            }
        },

    },
    created() {
        const id = this.$route.params.id;
        if (id) {
            this.loadEquipment(id);
        }
        this.loadEquipmentTypes();
    },
};
</script>
