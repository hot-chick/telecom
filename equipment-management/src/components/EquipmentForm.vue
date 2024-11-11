<template>
    <div>
        <div v-for="(item, index) in equipment" :key="index">
            <label for="equipment_type_id">Тип оборудования:</label>
            <select v-model="item.equipment_type_id" required>
                <option v-for="type in equipmentTypes" :key="type.id" :value="type.id">
                    {{ type.name }}
                </option>
            </select>
            <br>
            <label for="serial_number">Серийный номер:</label>
            <input type="text" v-model="item.serial_number" required />

            <label for="description">Примечание:</label>
            <textarea v-model="item.description"></textarea>
            <br>
            <button type="button" @click="removeEquipment(index)">Удалить</button>
            <br>
            <br>
        </div>

        <button type="button" @click="addEquipment">Добавить ещё</button>
        <button type="button" @click="submitForm">Сохранить</button>
    </div>
</template>

<script>
export default {
    data() {
        return {
            equipment: [
                { equipment_type_id: null, serial_number: '', description: '' }
            ],
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
                this.equipmentTypes = data.data;
            } catch (error) {
                console.error('Ошибка при загрузке типов оборудования:', error);
            }
        },
        addEquipment() {
            this.equipment.push({ equipment_type_id: null, serial_number: '', description: '' });
        },
        removeEquipment(index) {
            this.equipment.splice(index, 1);
        },
        async submitForm() {
            try {
                const response = await fetch('http://localhost:8000/api/equipment', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ equipment: this.equipment }),
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    console.error('Ошибка при массовом сохранении оборудования:', errorData);
                    throw new Error(`Ошибка: ${response.status} ${response.statusText}`);
                }

                this.$router.push('/');
            } catch (error) {
                console.error('Ошибка при сохранении оборудования:', error);
            }
        }
    },
    created() {
        this.loadEquipmentTypes();
    },
};
</script>
