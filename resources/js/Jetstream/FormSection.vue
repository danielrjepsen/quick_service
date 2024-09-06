<template>
        <div class="mt-5 md:mt-0 md:col-span-2">
            <jet-section-title class="pl-6">
                <template #title><slot name="title"></slot></template>
                <template #description><slot name="description"></slot></template>
            </jet-section-title>
            <form @submit.prevent="$emit('submitted')">
                <div class="overflow-hidden">
                    <div class="py-4 py-5 sm:p-6">
                        <div class="grid grid-cols-6 gap-6">
                            <slot name="form"></slot>
                        </div>
                    </div>

                    <div class="flex items-center px-4 py-3 text-right sm:px-6" v-if="hasActions">
                        <slot name="actions"></slot>
                    </div>
                </div>
            </form>
        </div>
</template>

<script>
    import JetSectionTitle from './SectionTitle'

    export default {
        components: {
            JetSectionTitle,
        },

        computed: {
            hasActions() {
                return !! this.$slots.actions
            }
        }
    }
</script>
