<template>
    <div :style="'padding-left:' + (this.counter + 0.5) + 'rem'">
        <div class="w-full pl-4 pr-2 border-b-2 py-2 text-base flex" v-if="!first">
            <i class="el-icon-arrow-down transform rotate-45 mr-1 mt-1"></i>
            <div class="w-full" @click="handleEditProductInputClick" v-if="!showEditProductInput">{{ node.name }}</div>
            <div class="w-11/12 el-input el-input--medium" v-if="showEditProductInput">
                <input ref="refEditProduct" type="text" placeholder="Please Input" class="el-input__inner focus:ring-0 "
                       v-model="form.name" @blur="handleEditProductInputBlur" @keyup.enter.stop="handleEditProductInputBlur"
                       style="border: 0 !important;">
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>
            <div class="pl-2 border-l-2 cursor-pointer" @click.stop="handleNewProductInputClick">
                <i class="collapse-item__icon el-icon-plus text-base"></i>
            </div>
        </div>
        <div class="w-full px-3 border-b-1 py-1 text-base flex" :style="'padding-left:' + (this.counter + 1) + 'rem'"
             v-show="showNewProductInput">
            <i class="el-icon-arrow-down transform rotate-45 mr-1 mt-2"></i>
            <div class="w-11/12 el-input el-input--medium">
                <input ref="refNewProduct" type="text" class="el-input__inner focus:ring-0"
                       v-model="form.name" @blur="handleNewProductInputBlur" @keyup.enter.stop="handleNewProductInputBlur"
                       style="border: 0 !important;">
            </div>
        </div>
        <template v-for="child in node.children" v-if="node.children && node.children.length">
            <node :node="child" :key="child.name + '-' + child.id" :counter="counter + 0.5"></node>
        </template>
    </div>
</template>

<script>
import JetInputError from "@/Jetstream/InputError";

export default {
    name: "node",
    components: {
        JetInputError
    },
    props: {
        node: Object,
        counter: {
            type: Number,
            default: -0.5
        },
        first: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            form: this.$inertia.form({
                name: '',
                product_id: this.node.id
            }),
            showNewProductInput: false,
            showEditProductInput: false
        }
    },
    methods: {
        handleNewProductInputClick() {
            this.showNewProductInput = true
            this.$nextTick(() => this.$refs.refNewProduct.focus());
        },
        handleEditProductInputClick() {
            this.showEditProductInput = true
            this.form.name = this.node.name;
            this.$nextTick(() => this.$refs.refEditProduct.focus());
        },
        handleNewProductInputBlur() {
            if (this.form.name && this.form.name !== this.node.name) {
                return this.storeNewProduct();
            }
            this.form.name = '';
            this.showNewProductInput = false;
        },
        handleEditProductInputBlur() {
            if (this.form.name && this.form.name !== this.node.name) {
                return this.updateProduct();
            }
            this.form.name = '';
            this.showEditProductInput = false;
        },
        storeNewProduct() {
            this.form.post(route('product.addProductToParent'), {
                errorBag: 'addProductToParent',
                preserveScroll: true,
                onSuccess: page => {
                    this.form.name = '';
                    this.showNewProductInput = false;
                },
            });
        },
        updateProduct() {
            this.form.put(route('product.updateProduct'), {
                errorBag: 'updateProduct',
                preserveScroll: true,
                onSuccess: page => {
                    this.form.name = '';
                    this.showEditProductInput = false;
                },
            });
        },
    },
};
</script>
