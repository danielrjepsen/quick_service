<template>
    <tenant-layout>
        <template #header>
            <h2 class="font-bold text-xl text-gray-800 leading-tight pl-4 pt-4">
                {{ $t('service') }}
            </h2>
        </template>

        <div class="py-2">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Service create form -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="createService">
                        <div class="lg:flex flex-nowrap lg:flex-wrap p-4">
                            <div class="flex-grow lg:pr-2 lg:w-1/2 xl:p-0 xl:w-auto xl:mr-2">
                                <el-input id="serviceName"
                                          type="text"
                                          :placeholder="$t('service')"
                                          v-model="form.name"/>

                                <jet-input-error :message="form.errors.name" class="mt-2"/>
                            </div>
                            <div class="mt-2 lg:pl-2 lg:mt-0 lg:w-1/2 xl:p-0 xl:w-auto xl:mr-2">
                                <el-select
                                    class="w-full"
                                    v-model="form.products"
                                    multiple
                                    filterable
                                    allow-create
                                    default-first-option
                                    :placeholder="$t('product_choose')">
                                    <el-option
                                        v-for="item in options"
                                        :key="item.id"
                                        :label="item.name"
                                        :value="item.id">
                                    </el-option>
                                </el-select>

                                <jet-input-error :message="form.errors.products" class="mt-2"/>
                            </div>
                            <div class=" mt-2 lg:pr-2 lg:w-1/2 xl:p-0 xl:w-auto xl:mt-0 xl:mr-2">
                                <el-date-picker
                                    v-model="form.planned_date"
                                    type="datetime"
                                    :placeholder="$t('pick_a_date')"
                                    style="width: 100% !important;"
                                    :picker-options="datePickerOptions">
                                </el-date-picker>

                                <jet-input-error :message="form.errors.planned_date" class="mt-2"/>

                                <p>{{ $t('planned_date') }}</p>
                            </div>
                            <div class="mt-2 lg:pl-2 lg:w-1/2 xl:p-0 xl:w-auto xl:mr-2 xl:mt-0">
                                <el-date-picker
                                    v-model="form.due_date"
                                    type="datetime"
                                    :placeholder="$t('pick_a_date')"
                                    style="width: 100% !important;"
                                    :picker-options="datePickerOptions">
                                </el-date-picker>

                                <jet-input-error :message="form.errors.due_date" class="mt-2"/>

                                <p>{{ $t('due_date') }}</p>
                            </div>
                        </div>
                        <div class="flex justify-end pr-4 pb-4">
                            <el-button round native-type="submit" type="primary"
                                       :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                {{ $t('service_create') }}
                            </el-button>
                        </div>
                    </form>
                </div>

                <!-- Assets/service table unfinished header -->
                <div class="mt-6 flex" v-if="unfinishedAssets.data.length > 0">
                    <span class="bg-red-500 text-white rounded-full h-6 w-6 flex items-center justify-center">{{ unfinishedAssets.total }}</span>
                    <h3 class="text-xl ml-2">{{ $t('service_unfinished_planned') }}</h3>
                </div>
                <!-- Assets/service unfinished table -->
                <assetsTable v-if="unfinishedAssets.data.length > 0" :asset="asset" v-for="(asset, index) in unfinishedAssets.data" :key="'unfinished-' + asset.id" />

                <el-pagination background layout="prev, pager, next" :pageSize="unfinishedAssets.per_page"
                               :total="unfinishedAssets.total" @current-change="pageChange" :current-page="checkPageNumber"
                               :hide-on-single-page="true"></el-pagination>

                <!-- Assets/service table finished header -->
                <div class="mt-10 flex" v-if="finishedAssets.length > 0">
                    <span class="bg-green-500 text-white rounded-full h-6 w-6 flex items-center justify-center">{{ finishedAssets.length }}</span>
                    <h3 class="text-xl ml-2">{{ $t('service_finished') }}</h3>
                </div>
                <!-- Assets/service finished table -->
                <assetsTable v-if="finishedAssets.length > 0" :asset="asset" v-for="(asset, index) in finishedAssets" :key="'finished-' + asset.id" finished />
            </div>
        </div>
    </tenant-layout>
</template>

<script>
import TenantLayout from '@/Layouts/TenantLayout'
import JetInputError from "@/Jetstream/InputError";
import AssetsTable from "@/Components/Service/AssetsTable";


export default {
    components: {
        AssetsTable,
        TenantLayout, JetInputError
    },
    props: ['options', 'unfinishedAssets', 'finishedAssets'],
    data() {
        return {
            form: this.$inertia.form({
                name: '',
                products: [],
                planned_date: '',
                due_date: ''
            }),
            datePickerOptions: {
                disabledDate (time) {
                    return time.getTime() < Date.now() - 8.64e7;
                }
            }

        }
    },
    methods: {
        createService() {
            this.form.post(route('service.store'), {
                errorBag: 'createService',
                preserveScroll: true,
                onSuccess: page => {
                    this.form.reset();
                },
            });
        },
        pageChange(newCurrentPage) {
            this.$inertia.get(this.unfinishedAssets.links[newCurrentPage].url, {}, {
                preserveState: true,
                preserveScroll: true,
            })
        }
    },

    computed: {
        checkPageNumber() {
            if (this.unfinishedAssets.current_page > this.unfinishedAssets.last_page) {
                return this.pageChange(this.unfinishedAssets.last_page)
            }
            return this.unfinishedAssets.current_page
        }
    }
}
</script>
