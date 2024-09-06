<template>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <create-team-form />
        </div>
</template>

<script>
    import TenantLayout from '@/Layouts/TenantLayout'
    import CreateTeamForm from './CreateTeamForm'

    export default {
        components: {
            TenantLayout,
            CreateTeamForm,
        },
    }
</script>
