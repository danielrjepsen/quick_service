<template>
    <tenant-layout>
        <div class="md:grid md:grid-cols-5">
            <div class="md:col-span-5">
                <el-tabs type="card" @tab-click="handleClick">
                    <el-tab-pane :label="$t('organisation')">
                        <div class="md:grid md:grid-cols-5">
                            <div class="md:col-span-1">
                                <scrollactive class="hidden md:inline" :highlightFirstItem="true">
                                    <ul class="py-10 px-5 sticky top-0">
                                        <li class="mb-2"><a href="#organisation" class="scrollactive-item text-black hover:text-blue-800">{{ $t('organisation') }}</a></li>
                                        <li class="mb-2"><a href="#createTeam" class="scrollactive-item text-black hover:text-blue-800">{{ $t('team_create') }}</a></li>
                                    </ul>
                                </scrollactive>
                            </div>
                            <div class="md:col-span-4">
                                <organisation-show :organisation="organisation" id="organisation"/>
                                <jet-section-border />
                                <teams-create id="createTeam"/>
                            </div>
                        </div>
                    </el-tab-pane>
                    <el-tab-pane :label="$t('team')">
                        <div class="md:grid md:grid-cols-5">
                            <div class="md:col-span-1">
                                <scrollactive class="hidden md:inline" :highlightFirstItem="true">
                                    <ul class="py-10 px-5 sticky top-0">
                                        <li class="mb-2"><a href="#updateTeamName" class="scrollactive-item text-black hover:text-blue-800">{{ $t('team_update_info_title') }}</a></li>
                                        <li class="mb-2"><a href="#addTeamMember" class="scrollactive-item text-black hover:text-blue-800">{{ $t('team_member_manager.add_team_member.title') }}</a></li>
                                        <li class="mb-2"><a href="#pendingTeamInvitations" class="scrollactive-item text-black hover:text-blue-800">{{ $t('team_pending_invitations_title') }}</a></li>
                                        <li class="mb-2" v-if="team.users.length > 0"><a href="#manageTeamMembers" class="scrollactive-item text-black hover:text-blue-800">{{ $t('team_manage_team_members') }}</a></li>
                                    </ul>
                                </scrollactive>
                            </div>
                            <div class="md:col-span-4">
                                <teams-show :team="team" :availableRoles="availableRoles" :permissions="permissions" :organisation="organisation" />
                            </div>
                        </div>
                    </el-tab-pane>
                    <el-tab-pane :label="$t('members_list')" name="members" lazy>
                        <div class="md:grid md:grid-cols-5">
                            <div class="md:col-span-1">
                                <scrollactive class="hidden md:inline" :highlightFirstItem="true">
                                    <ul class="py-10 px-5 sticky top-0">
                                        <li class="mb-2"><a href="#security" class="scrollactive-item text-black hover:text-blue-800">{{ $t('security') }}</a></li>
                                        <li class="mb-2"><a href="#MembersList" class="scrollactive-item text-black hover:text-blue-800">{{ $t('members_list') }}</a></li>
                                    </ul>
                                </scrollactive>
                            </div>
                            <div class="md:col-span-4">
                                <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                                    <!-- Security-->
                                    <jet-action-section class="mt-10 sm:mt-0" id="security">
                                        <template #title>
                                            {{ $t('security') }}
                                        </template>

                                        <template #description>
                                            {{ $t('security_two_factor') }}
                                        </template>

                                        <template #content>
                                            <div v-if="permissions.canAddTeamMembers" class="md:col-span-1 mb-6">
                                                <el-button @click="update2FA()">{{ requireFA }}</el-button>
                                            </div>
                                        </template>
                                    </jet-action-section>

                                    <!-- Members list -->
                                    <members-list id="MembersList" :users="allOrganisationUsers"
                                                  :organisation="organisation" />
                                </div>
                            </div>
                        </div>
                    </el-tab-pane>
                </el-tabs>
            </div>
        </div>
    </tenant-layout>
</template>

<script>

import TenantLayout from "@/Layouts/TenantLayout"
import TeamsShow from '@/Pages/Teams/Show'
import TeamsCreate from '@/Pages/Teams/Create'
import OrganisationShow from '@/Pages/Organisation/Show'
import JetSectionBorder from '@/Jetstream/SectionBorder'
import JetActionSection from '@/Jetstream/ActionSection'
import MembersList from "@/Components/OrganisationMembers";

export default {
    name: "Settings",
    components: {MembersList, TenantLayout, TeamsShow, TeamsCreate, OrganisationShow, JetSectionBorder, JetActionSection},
    props: [
        'team',
        'availableRoles',
        'permissions',
        'organisation',
        'allOrganisationUsers'
    ],

    data() {
        return {
            update2FAForm: this.$inertia.form(),
        }
    },

    methods: {
        update2FA() {
            this.update2FAForm.put(route('settings.organisation.updateTwoFactor', {require2FA: !this.organisation.require2FA}), {
                errorBag: 'updateTwoFactor',
                preserveScroll: true,
                only: ['organisation']
            })
        },

        handleClick(tab, event) {
            if (tab.name === 'members') {
                this.$inertia.reload({only: ['allOrganisationUsers']})
            }
        }
    },
    computed: {
        requireFA () {
            return this.organisation.require2FA ? this.$t('disable') : this.$t('enable');
        }
    }
}
</script>

<style scroped>
.is-active {
    --tw-text-opacity: 1;
    color: rgba(59, 130, 246, var(--tw-text-opacity));
}
</style>
