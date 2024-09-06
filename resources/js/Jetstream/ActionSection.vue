<template>
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="mt-5 md:mt-0 md:col-span-3">
            <div class="px-4 py-5 sm:p-6">
                <jet-section-title class="mb-6">
                    <template #title><slot name="title"></slot></template>
                    <template #description><slot name="description"></slot></template>
                </jet-section-title>
                <slot name="content"></slot>
            </div>
        </div>
    </div>
</template>

<script>
    import JetSectionTitle from './SectionTitle'

    export default {
        components: {
            JetSectionTitle,
        }
    }
</script>
