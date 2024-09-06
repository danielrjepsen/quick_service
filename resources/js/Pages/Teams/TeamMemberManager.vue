<template>
    <div>
        <div v-if="userPermissions.canAddTeamMembers">
            <jet-section-border />

            <!-- Add Team Member -->
            <jet-form-section @submitted="addTeamMember" id="addTeamMember">
                <template #title>
                    {{ $t('team_member_manager.add_team_member.title') }}
                </template>

                <template #description>
                    {{ $t('team_member_manager.add_team_member.description') }}
                </template>

                <template #form>
                    <div class="col-span-6">
                        <div class="max-w-xl text-sm text-gray-600">
                            {{ $t('team_member_manager.add_team_member.form') }}
                        </div>
                    </div>

                    <!-- Member Email -->
                    <div class="col-span-6 lg:col-span-5">
                        <jet-label for="email" :value="$t('email')" />
                        <el-input id="email" type="text" class="mt-1 block w-full" v-model="addTeamMemberForm.email" />
                        <jet-input-error :message="addTeamMemberForm.errors.email" class="mt-2" />
                    </div>

                    <!-- Role -->
                    <div class="col-span-6 lg:col-span-5" v-if="availableRoles.length > 0">
                        <jet-label for="roles" :value="$t('role')" />
                        <jet-input-error :message="addTeamMemberForm.errors.role" class="mt-2" />

                        <div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer">
                            <button type="button" class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue"
                                            :class="{'border-t border-gray-200 rounded-t-none': i > 0, 'rounded-b-none': i != Object.keys(availableRoles).length - 1}"
                                            @click="addTeamMemberForm.role = role.key"
                                            v-for="(role, i) in availableRoles"
                                            :key="role.key">
                                <div :class="{'opacity-50': addTeamMemberForm.role && addTeamMemberForm.role != role.key}">
                                    <!-- Role Name -->
                                    <div class="flex items-center">
                                        <div class="text-sm text-gray-600" :class="{'font-semibold': addTeamMemberForm.role == role.key}">
                                            {{ role.name }}
                                        </div>

                                        <svg v-if="addTeamMemberForm.role == role.key" class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>

                                    <!-- Role Description -->
                                    <div class="mt-2 text-xs text-gray-600">
                                        {{ role.description }}
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </template>

                <template #actions>
                    <el-button round native-type="submit" type="primary" :class="{ 'opacity-25': addTeamMemberForm.processing }" :disabled="addTeamMemberForm.processing">
                        {{ $t('add') }}
                    </el-button>
                    <jet-action-message :on="addTeamMemberForm.recentlySuccessful" class="ml-3">
                        {{ $t('added') }}
                    </jet-action-message>
                </template>
            </jet-form-section>
        </div>

        <div v-if="team.team_invitations.length > 0 && userPermissions.canAddTeamMembers" id="pendingTeamInvitations">
            <jet-section-border />

            <!-- Team Member Invitations -->
            <jet-action-section class="mt-10 sm:mt-0">
                <template #title>
                    {{ $t('team_member_manager.team_invitations.title') }}
                </template>

                <template #description>
                    {{ $t('team_member_manager.team_invitations.description') }}
                </template>

                <!-- Pending Team Member Invitation List -->
                <template #content>
                    <div class="space-y-6">
                        <div class="flex items-center justify-between" v-for="invitation in team.team_invitations" :key="invitation.id">
                            <div class="text-gray-600">{{ invitation.email }}</div>

                            <div class="flex items-center">
                                <!-- Cancel Team Invitation -->
                                <button class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none"
                                                    @click="cancelTeamInvitation(invitation)"
                                                    v-if="userPermissions.canRemoveTeamMembers">
                                    {{ $t('cancel') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </jet-action-section>
        </div>

        <div v-if="team.users.length > 0" id="manageTeamMembers">
            <jet-section-border />

            <!-- Manage Team Members -->
            <team-members-list :team="team" :available-roles="availableRoles"
                          :user-permissions="userPermissions" :organisation="organisation" />
        </div>
    </div>
</template>

<script>
    import JetActionMessage from '@/Jetstream/ActionMessage'
    import JetActionSection from '@/Jetstream/ActionSection'
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetFormSection from '@/Jetstream/FormSection'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'
    import JetSectionBorder from '@/Jetstream/SectionBorder'
    import TeamMembersList from "@/Components/TeamMembersList";

    export default {
        components: {
            TeamMembersList,
            JetActionMessage,
            JetActionSection,
            JetConfirmationModal,
            JetDialogModal,
            JetFormSection,
            JetInputError,
            JetLabel,
            JetSectionBorder,
        },

        props: [
            'team',
            'availableRoles',
            'userPermissions',
            'organisation'
        ],

        data() {
            return {
                addTeamMemberForm: this.$inertia.form({
                    email: '',
                    role: null,
                }),

                updateRoleForm: this.$inertia.form({
                    role: null,
                }),

                leaveTeamForm: this.$inertia.form(),
                removeTeamMemberForm: this.$inertia.form(),

                currentlyManagingRole: false,
                managingRoleFor: null,
                confirmingLeavingTeam: false,
                teamMemberBeingRemoved: null,
            }
        },

        methods: {
            addTeamMember() {
                this.addTeamMemberForm.post(route('team-members.store', this.team), {
                    errorBag: 'addTeamMember',
                    preserveScroll: true,
                    onSuccess: () => this.addTeamMemberForm.reset(),
                });
            },

            cancelTeamInvitation(invitation) {
                this.$inertia.delete(route('team-invitations.destroy', invitation), {
                    preserveScroll: true
                });
            },

            manageRole(teamMember) {
                this.managingRoleFor = teamMember
                this.updateRoleForm.role = teamMember.membership.role
                this.currentlyManagingRole = true
            },

            updateRole() {
                this.updateRoleForm.put(route('team-members.update', [this.team, this.managingRoleFor]), {
                    preserveScroll: true,
                    onSuccess: () => (this.currentlyManagingRole = false),
                })
            },

            confirmLeavingTeam() {
                this.confirmingLeavingTeam = true
            },

            leaveTeam() {
                this.leaveTeamForm.delete(route('team-members.destroy', [this.team, this.$page.props.user]))
            },

            confirmTeamMemberRemoval(teamMember) {
                this.teamMemberBeingRemoved = teamMember
            },

            removeTeamMember() {
                this.removeTeamMemberForm.delete(route('team-members.destroy', [this.team, this.teamMemberBeingRemoved]), {
                    errorBag: 'removeTeamMember',
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => (this.teamMemberBeingRemoved = null),
                })
            },

            displayableRole(role) {
                return this.availableRoles.find(r => r.key === role).name
            },
        },
    }
</script>
