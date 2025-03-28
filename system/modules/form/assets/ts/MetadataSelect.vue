<script setup>
    import { defineProps, ref, onMounted } from 'vue'
    const props = defineProps(['defaultValue'])

    const is_object_map = ref("0");
    const defaultValue = ref(props.defaultValue);
    const user_rows = ref([]);

    onMounted(() => {
        const rows = defaultValue.value.find(x => x.meta_key === "user_rows");
        if (rows) {
            user_rows.value = rows.meta_value;
        }

        const is_map = defaultValue.value.find(x => x.meta_key === "is_object_map");
        if (is_map) {
            is_object_map.value = is_map.meta_value;
        }
    })

    const showObjectMap = () => is_object_map.value == "1";
    const getDefaultValue = (key, default_return) => {
        if (defaultValue !== undefined) {
            const data = defaultValue.value.find(x => x.meta_key === key);
            if (data) return data.meta_value ?? default_return;
        }

        return default_return;
    }
    const addRow = () => user_rows.value = [...user_rows.value, {key: '', value: ''}];
    const getRowFieldName = (type, index) => "user_rows[" + index + "][" + type + "]";
    const removeRow = (index) => user_rows.value.splice(index, 1);
</script>
<template>
    <div class="container-fluid vue-metadata-select__container">
        <label class="row">Additional Details</label>
        <div class="row vue-metadata-select__radio-header">
            <div class="col-12 col-md-6">
                <div class="form-check">
                    <input class="form-check-input" id="object_map_yes" type="radio" name="is_object_map" value="1" v-model="is_object_map" />
                    <label class="form-check-label" for="object_map_yes">Object Map</label>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-check">
                    <input class="form-check-input" id="object_map_no" type="radio" name="is_object_map" value="0" v-model="is_object_map" />
                    <label class="form-check-label" for="object_map_no">User defined options</label>
                </div>
            </div>
        </div>
        <div class="row vue-metadata-select__content-container" v-if="showObjectMap()">
            <div class="col-12">
                <label class="form-label" for="vue-metadata-select__object-type">Object</label>
                <input class="form-control" type="text" name="object_type" :value="getDefaultValue('object_type','')" id="vue-metadata-select__object-type" />
            </div>
            <div class="col-12">
                <label class="form-label" for="vue-metadata-select__object-filter">Filter</label>
                <input class="form-control" type="text" name="object_filter" :value="getDefaultValue('object_filter','')" id="vue-metadata-select__object-filter" />
            </div>
            <div class="col-12">
                <label class="form-label" for="vue-metadata-select__options">Options</label>
                <input class="form-control" type="text" name="options" :value="getDefaultValue('options','')" id="vue-metadata-select__options" />
            </div>
        </div>
        <div v-else class="row vue-metadata-select__content-container">
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-sm btn-secondary col-12 col-sm-2" @click="addRow()">Add row</button>
                </div>
            </div>
            <div class="row" v-for="(row, index) in user_rows">
                <div class="col-12 col-md-5">
                    <label class="form-label">Key</label>
                    <input class="form-control form-control-sm" type="text" :name="getRowFieldName('key', index)" v-model="user_rows[index].key" />
                </div>
                <div class="col-12 col-md-5">
                    <label class="form-label">Value</label>
                    <input class="form-control form-control-sm" type="text" :name="getRowFieldName('value', index)" v-model="user_rows[index].value" />
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-danger vue-metadata-select__button" @click="removeRow(index)">Delete</button>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>

.vue-metadata-select__container {
	display: block; 
	padding-bottom: 20px;
    border-radius: 5px;
}

.vue-metadata-select__radio-header {
	background-color: #efefef; 
	padding: 10px; 
	border: 1px solid #ccc;
}

/* .vue-metadata-select__radio-header input[type="radio"] {
	margin-bottom: 0px;
} */

.vue-metadata-select__content-container {
	padding: 10px 10px 20px;
	border-left: 1px solid #ccc;
	border-right: 1px solid #ccc;
	border-bottom: 1px solid #ccc;
}

.vue-metadata-select__content-container input[type="text"] {
	width: 100%;
}

.vue-metadata-select__button {
	margin-top: 46px !important;
}
</style>