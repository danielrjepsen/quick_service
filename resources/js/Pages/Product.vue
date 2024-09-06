<template>
    <tenant-layout>
        <template #header>
            <h2 class="font-bold text-xl text-gray-800 leading-tight pl-4 pt-4">
                {{ $t('product') }}
            </h2>
        </template>

        <div class="py-2">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="createProduct">
                        <div class="lg:flex flex-nowrap lg:flex-wrap p-4">
                            <div class="lg:pr-2 lg:w-1/2 xl:p-0 xl:w-auto xl:flex-grow xl:mr-2">
                                <el-input id="productName"
                                          type="text"
                                          :placeholder="$t('product')"
                                          v-model="form.name" />

                                <jet-input-error :message="form.errors.name" class="mt-2" />
                            </div>
                            <div class="mt-2 lg:pl-2 lg:mt-0 lg:w-1/2 xl:p-0 xl:w-auto xl:flex-grow xl:mr-2">
                                <el-select
                                    class="w-full"
                                    v-model="form.product_id"
                                    filterable
                                    allow-create
                                    clearable
                                    default-first-option
                                    :placeholder="$t('product_choose_parent')">
                                    <el-option
                                        v-for="item in options"
                                        :key="item.id"
                                        :label="item.name"
                                        :value="item.id">
                                    </el-option>
                                </el-select>

                                <jet-input-error :message="form.errors.product_id" class="mt-2" />
                            </div>
                            <div class="ml-auto mt-2 xl:mt-0 flex justify-end">
                                <jet-action-message :on="form.recentlySuccessful" class="mr-3 self-center">
                                    {{ $t('saved') }}
                                </jet-action-message>
                                <el-button round native-type="submit" type="primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    {{ $t('product_create') }}
                                </el-button>
                            </div>
                        </div>

                    </form>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mt-4">
                    <el-collapse v-model="activeName" accordion>
                        <collapse-item v-for="(option, index) in options" :key="option.id" :title="option.name" :name="index" :count="option.services_count" :product_id="option.id" :disabled="option.children.length === 0">
                            <node-tree :node="option" :first="true"></node-tree>
                        </collapse-item>
                    </el-collapse>
                </div>
            </div>
        </div>
    </tenant-layout>
</template>

<script>
import TenantLayout from '@/Layouts/TenantLayout'
import JetInputError from "@/Jetstream/InputError";
import JetActionMessage from "@/Jetstream/ActionMessage"
import CollapseItem from "@/Components/collapse-item"
import NodeTree from "@/Components/NodeTree";


export default {
    components: {
        TenantLayout, JetInputError, JetActionMessage, CollapseItem, NodeTree
    },
    props: ['options'],
    data() {
        return {
            form: this.$inertia.form({
                name: '',
                product_id: ''
            }),
            activeName: '0'
        }
    },
    methods: {
        createProduct() {
            this.form.post(route('product.store'), {
                errorBag: 'createProduct',
                preserveScroll: true,
                onFinish: () => this.form.reset('name', 'product_id')
            });
        }
    },
}
</script>
