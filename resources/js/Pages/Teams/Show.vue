<template>
        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <update-team-name-form :team="team" :permissions="permissions" />

                <team-member-manager class="mt-10 sm:mt-0"
                            :team="team"
                            :available-roles="availableRoles"
                            :user-permissions="permissions"
                            :organisation="organisation" />

                <template v-if="permissions.canDeleteTeam && ! team.personal_team">
                    <jet-section-border />

                    <delete-team-form class="mt-10 sm:mt-0" :team="team" />
                </template>
            </div>
        </div>
</template>

<script>
    import TeamMemberManager from './TeamMemberManager'
    import TenantLayout from '@/Layouts/TenantLayout'
    import DeleteTeamForm from './DeleteTeamForm'
    import JetSectionBorder from '@/Jetstream/SectionBorder'
    import UpdateTeamNameForm from './UpdateTeamNameForm'

    export default {
        props: [
            'team',
            'availableRoles',
            'permissions',
            'organisation'
        ],

        components: {
            TenantLayout,
            DeleteTeamForm,
            JetSectionBorder,
            TeamMemberManager,
            UpdateTeamNameForm,
        },
    }
</script>
