<template>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" :class="{'mt-4' : !finished}">
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-white">
                                <tr>
                                    <th scope="col" colspan="3"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 whitespace-nowrap text-sm max-w-lg" :class="{ 'bg-green-200 bg-opacity-20' : finished }">
                                        <div class="py-3 px-6 text-sm font-bold truncate text-center">{{ assetData.name }}</div>
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium w-4" :class="{ 'bg-green-200 bg-opacity-20' : finished }">
                                            <el-switch v-model="assetSwitch" active-color="#13ce66" @change="assetCompleteAllSwitch" :key="'asset-' + assetData.id"></el-switch>
                                    </th>
                                    <th v-if="!finished" scope="col"
                                        class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium w-4 border-l-2 cursor-pointer" @click="showAddNewServiceForm">
                                        <i class="el-icon-plus"></i>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" v-if="!finished">
                                <tr v-for="service in assetData.services" :key="service.id" :class="{ 'bg-green-100' : service.completed_at }">
                                    <td class="px-6 py-4 whitespace-nowrap flex items-center">
                                        <i class="el-icon-arrow-left transform -rotate-45 mr-2"></i>
                                        <div :class="{'text-red-600': !service.completed_at }" class="relative">
                                            <div @click="selectNewPlannedDay(service.id)" class="cursor-pointer">
                                                <el-date-picker
                                                    v-model="newDateToServiceForm.planned_date"
                                                    type="datetime"
                                                    align="center"
                                                    class="invisible"
                                                    style="position: absolute; top: 0;"
                                                    :ref="'plannedDayInput-' + service.id"
                                                    @change="updateDate(service.id)"
                                                    :picker-options="datePickerOptions"
                                                >
                                                </el-date-picker>
                                                {{ $t('planned_date') }} {{ service.planned_date | formatToLocalString }}</div>
                                            <div @click="selectNewDueDay(service.id)" class="cursor-pointer">
                                                <el-date-picker
                                                    v-model="newDateToServiceForm.due_date"
                                                    type="datetime"
                                                    align="center"
                                                    class="invisible"
                                                    style="position: absolute; top: 0;"
                                                    :ref="'dueDayInput-' + service.id"
                                                    @change="updateDate(service.id)"
                                                    :picker-options="datePickerOptions"
                                                >
                                                </el-date-picker>
                                                {{ $t('due_date') }} {{ service.due_date | formatToLocalString }}</div>
                                        </div>
                                    </td>
                                    <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="ml-auto bg-blue-50 rounded-full py-3 px-6 text-sm text-blue-400 truncate w-64 max-w-lg">
                                            {{ service.product.name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <el-switch v-model="service.completed_at" active-color="#13ce66" @change="toggleService(service.id)" :key="'service-' + service.id"></el-switch>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium border-l-2 cursor-pointer" @click="showAddNewServiceForm(assetData.id)">
                                        <i class="el-icon-plus"></i>
                                    </td>
                                </tr>
                                <tr v-if="showNewServicesInput">
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <form @submit.prevent="addNewService">
                                            <div class="lg:flex flex-nowrap lg:flex-wrap p-4">
                                                <div class="mt-2 lg:mt-0 lg:w-full xl:p-0 xl:w-auto xl:flex-grow xl:mr-2">
                                                    <el-select
                                                        class="w-full"
                                                        v-model="newServiceFormData.products"
                                                        multiple
                                                        filterable
                                                        allow-create
                                                        remote
                                                        reserve-keyword
                                                        :placeholder="$t('product_choose')"
                                                        :remote-method="remoteMethod"
                                                        :loading="loading">
                                                        <el-option
                                                            v-for="item in options"
                                                            :key="item.id"
                                                            :label="item.name"
                                                            :value="item.id">
                                                        </el-option>
                                                    </el-select>
                                                    <jet-input-error :message="newServiceFormData.errors.products" class="mt-2"/>
                                                </div>
                                                <div class="mt-2 lg:pr-2 lg:w-1/2 xl:p-0 xl:w-auto xl:mt-0 xl:mr-2">

                                                    <el-date-picker
                                                        v-model="newServiceFormData.planned_date"
                                                        type="datetime"
                                                        :placeholder="$t('pick_a_date')"
                                                        style="width: 100% !important;"
                                                        :picker-options="datePickerOptions"
                                                        :default-value="Date.now()">
                                                    </el-date-picker>

                                                    <jet-input-error :message="newServiceFormData.errors.planned_date" class="mt-2"/>

                                                    <p>{{ $t('planned_date') }}</p>
                                                </div>
                                                <div class="mt-2 lg:pl-2 lg:w-1/2 xl:p-0 xl:w-auto xl:mr-2 xl:mt-0">
                                                    <el-date-picker
                                                        v-model="newServiceFormData.due_date"
                                                        type="datetime"
                                                        :placeholder="$t('pick_a_date')"
                                                        style="width: 100% !important;"
                                                        :picker-options="datePickerOptions">
                                                    </el-date-picker>

                                                    <jet-input-error :message="newServiceFormData.errors.due_date" class="mt-2"/>

                                                    <p>{{ $t('due_date') }}</p>
                                                </div>
                                            </div>
                                            <div class="flex justify-end pr-4 pb-4">
                                                <el-button round native-type="submit" type="primary"
                                                           :class="{ 'opacity-25': newServiceFormData.processing }" :disabled="newServiceFormData.processing">
                                                    {{ $t('service_add') }}
                                                </el-button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import JetInputError from "@/Jetstream/InputError";

export default {
    name: 'assetsTable',
    components: {JetInputError},
    props: {
        asset: {
            type: Object,
            required: true
        },
        finished: {
            type: Boolean,
            default: false
        },
    },

    created() {
        this.assetData = this.asset;
        this.assetData.services.forEach(function(item) {
            if (item.completed_at !== null) {
                item.completed_at = true;
            }
        })
    },

    data() {
        return {
            assetData: null,
            assetSwitch: this.finished,
            showNewServicesInput: false,
            newServiceFormData: this.$inertia.form({
                asset_id: '',
                products: '',
                due_date: '',
                planned_date: '',
            }),
            options: [],
            loading: false,
            newDateToServiceForm: this.$inertia.form({
                service_id: '',
                due_date: '',
                planned_date: '',
            }),
            datePickerOptions: {
                disabledDate (time) {
                    return time.getTime() < Date.now() - 8.64e7;
                }
            }
        }
    },

    methods: {
        addNewService() {
            this.newServiceFormData.post(route('service.addNewService'), {
                errorBag: 'addNewService',
                preserveScroll: true,
                onFinish: () => {
                    if (Object.keys(this.newServiceFormData.errors).length === 0) {
                        this.showNewServicesInput = false;
                        this.newServiceFormData.reset();
                        this.assetData = this.asset;
                    }
                }
            });
        },
        showAddNewServiceForm(asset_id) {
            this.newServiceFormData.reset()
            this.showNewServicesInput = !this.showNewServicesInput;
            this.newServiceFormData.asset_id = asset_id;
        },
        toggleService(service_id) {
            this.$inertia.form({service: service_id}).post(route('service.toggleService'), {
                preserveScroll: true
            });
        },
        assetCompleteAllSwitch(newValue) {
            this.$inertia.form({asset_id: this.assetData.id, completed: newValue}).post(route('service.toggleCompleteAllAsset'), {
                preserveScroll: true
            });
        },
        remoteMethod(query) {
            if (query !== '') {
                this.loading = true;
                axios.post(route('service.getProductsNotOnAsset', {asset: this.assetData.id }), {query: query})
                    .then(response => {
                        this.options = response.data[0];
                        this.loading = false
                    })
            } else {
                this.options = [];
            }
        },
        selectNewPlannedDay(service_id) {
            this.$refs['plannedDayInput-' + service_id][0].pickerVisible = true;
        },
        selectNewDueDay(service_id) {
            this.$refs['dueDayInput-' + service_id][0].pickerVisible = true;
        },
        updateDate(service_id) {
            this.newDateToServiceForm.service_id = service_id
            this.newDateToServiceForm.post(route('service.updateDate'), {
                errorBag: 'updateDate',
                preserveScroll: true,
                onFinish: () => {
                    if (Object.keys(this.newDateToServiceForm.errors).length === 0) {
                        this.newDateToServiceForm.reset();
                        this.assetData = this.asset;
                    }
                }
            });
        }
    },
    filters: {
        formatToLocalString: function (value) {
            if (!value) return ''
            return $DateTime.fromISO(value).toLocaleString()
        }
    }
}
</script>
