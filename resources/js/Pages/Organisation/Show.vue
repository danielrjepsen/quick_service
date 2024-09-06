<template>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <update-organisation-form :organisation="organisation" />
        </div>
</template>

<script>

import TenantLayout from '@/Layouts/TenantLayout'
import UpdateOrganisationForm from "./UpdateOrganisationForm";

export default {
    components: {
        TenantLayout,
        UpdateOrganisationForm
    },
    props: ['organisation']
}
</script>
