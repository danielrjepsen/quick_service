<template>
    <div class="el-collapse-item"
         :class="{'is-active': isActive, 'is-disabled' : disabled }">
        <div
            role="tab"
            class="border-b"
            :aria-expanded="isActive"
            :aria-controls="`el-collapse-content-${id}`"
            :aria-describedby="`el-collapse-content-${id}`"
        >
            <div
                class="el-collapse-item__header border-b-2 pt-4 pl-4 pb-4"
                @click="handleHeaderClick"
                role="button"
                :id="`el-collapse-head-${id}`"
                :tabindex="disabled ? undefined : 0"
                @keyup.enter.stop="handleEnterClick"
                :class="{
          'focusing': focusing,
          'is-active': isActive,
        }"
                @focus="handleFocus"
                @blur="focusing = false"
            >

                <div class="w-full flex items-center" v-show="!showEditProductInput">
                    <div @click.stop="handleEditProductInputClick">
                        <slot name="title">{{ title }}</slot>
                    </div>
                    <i class="el-icon-arrow-down ml-2" :class="{'transform rotate-180' : isActive}" v-show="!disabled"></i>
                </div>
                <div class="w-11/12 el-input el-input--medium" v-show="showEditProductInput">
                    <input ref="refEditProduct" type="text" class="el-input__inner focus:ring-0 "
                           v-model="form.name" @blur="handleEditProductInputBlur" @keyup.enter.stop="handleEditProductInputBlur"
                           style="border: 0 !important;">
                    <jet-input-error :message="form.errors.name" class="mt-2" />
                </div>

                <div class="flex" :class="{ 'text-black' : disabled}">
                    <div class="px-2 md:pr-8">
                        {{ count }}x
                    </div>
                    <div class="pl-2 border-l-2 cursor-pointer" @click.stop="handleNewProductInputClick">
                        <i class="collapse-item__icon el-icon-plus text-base"></i>
                    </div>
                </div>
            </div>
        </div>
        <el-collapse-transition>
            <div
                class="el-collapse-item__wrap"
                v-show="isActive"
                role="tabpanel"
                :aria-hidden="!isActive"
                :aria-labelledby="`el-collapse-head-${id}`"
                :id="`el-collapse-content-${id}`"
            >
                <div class="el-collapse-item__content pb-0">
                    <slot></slot>
                    <div class="w-full px-3 border-b-1 py-1 text-base flex" v-show="showNewProductInput">
                        <i class="el-icon-arrow-down transform rotate-45 mr-1 mt-2"></i>
                        <div class="w-11/12 el-input el-input--medium">
                            <input ref="refNewProduct" type="text" class="el-input__inner focus:ring-0 "
                                   v-model="form.name" @blur="handleNewProductInputBlur" @keyup.enter.stop="handleNewProductInputBlur"
                                   style="border: 0 !important;">
                        </div>
                    </div>
                </div>
            </div>
        </el-collapse-transition>
    </div>
</template>
<script>
import ElCollapseTransition from 'element-ui/src/transitions/collapse-transition';
import Emitter from 'element-ui/src/mixins/emitter';
import {generateId} from 'element-ui/src/utils/util';
import JetInputError from "@/Jetstream/InputError";

export default {
    name: 'CollapseItem',

    componentName: 'CollapseItem',

    mixins: [Emitter],

    components: {ElCollapseTransition, JetInputError},

    data() {
        return {
            contentWrapStyle: {
                height: 'auto',
                display: 'block'
            },
            contentHeight: 0,
            focusing: false,
            isClick: false,
            id: generateId(),
            showNewProductInput: false,
            showEditProductInput: false,
            form: this.$inertia.form({
                name: '',
                product_id: this.product_id
            }),
        };
    },

    inject: ['collapse'],

    props: {
        title: String,
        name: {
            type: [String, Number],
            default() {
                return this._uid;
            }
        },
        disabled: Boolean,
        count: {
            type: Number,
            default: 0
        },
        product_id: Number
    },

    computed: {
        isActive() {
            return this.collapse.activeNames.indexOf(this.name) > -1;
        }
    },

    methods: {
        handleFocus() {
            setTimeout(() => {
                if (!this.isClick) {
                    this.focusing = true;
                } else {
                    this.isClick = false;
                }
            }, 50);
        },
        handleHeaderClick() {
            if (this.disabled) return;
            this.dispatch('ElCollapse', 'item-click', this);
            this.focusing = false;
            this.isClick = true;
        },
        handleEnterClick() {
            this.dispatch('ElCollapse', 'item-click', this);
        },
        handleNewProductInputClick() {
            if (!this.isActive) {
                this.dispatch('ElCollapse', 'item-click', this);
            }
            this.showNewProductInput = true
            this.$nextTick(() => this.$refs.refNewProduct.focus());
        },
        handleEditProductInputClick() {
            this.showEditProductInput = true
            this.form.name = this.title;
            this.$nextTick(() => this.$refs.refEditProduct.focus());
        },
        handleNewProductInputBlur() {
            if (this.form.name) {
                this.storeNewProduct();
            }
            this.form.name = '';
            this.showNewProductInput = false;
        },
        handleEditProductInputBlur() {
            if (this.form.name && this.form.name !== this.title) {
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
    }
};
</script>

<style scoped>
.collapse-item__icon {
    margin: 0 8px 0 auto;
    transition: transform .3s;
    font-weight: 600;
}

.el-collapse-item__header {
    font-size: 15px;
    font-weight: 600;
}

.el-collapse-item__content {
    padding-bottom: 0;
}

.el-collapse-item.is-disabled .el-collapse-item__header {
    color: inherit;
    cursor: default;
}
</style>
