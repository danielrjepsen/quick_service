<template>
    <div>
        <!-- Manage Team Members -->
        <jet-action-section class="mt-10 sm:mt-0">
            <template #title>
                {{ $t('team_member_manager.team_members_list.title') }}
            </template>

            <template #description>
                {{ $t('team_member_manager.team_members_list.description') }}
            </template>

            <!-- Team Member List -->
            <template #content>
                <el-table
                    :data="team.users"
                    stripe
                    style="width: 100%">
                    <el-table-column
                        prop="name"
                        :label="$t('name')">
                    </el-table-column>
                    <el-table-column
                        prop="email"
                        :label="$t('email')">
                    </el-table-column>
                    <el-table-column
                        prop="role"
                        :label="$t('role')">
                    </el-table-column>
                    <el-table-column
                        :label="$t('signin')">
                        <template slot-scope="scope">
                            <template v-if="scope.row.two_factor_enabled === 1">
                                {{ $t('email_and_two_factor') }}
                            </template>
                            <template v-else>
                                {{ $t('email') }}
                            </template>
                        </template>
                    </el-table-column>
                    <el-table-column
                        :label="$t('status')" width="120"
                        v-if="organisation.require2FA === 1">
                        <template slot-scope="scope">
                            <i class="el-icon-warning" style="color: red; font-size: 1.5rem" v-if="scope.row.two_factor_enabled === false"></i>
                            <i class="el-icon-success" style="color: green; font-size: 1.5rem" v-if="scope.row.two_factor_enabled === true"></i>
                        </template>
                    </el-table-column>
                    <el-table-column
                        :label="$t('Operations')"
                        width="200">
                        <template slot-scope="scope">
                            <div class="flex items-center">
                                <!-- Manage Team Member Role -->
                                <button class="ml-2 text-sm text-gray-400 underline"
                                        @click="manageRole(scope.row)"
                                        v-if="userPermissions.canAddTeamMembers && availableRoles.length">
                                    {{ displayableRole(scope.row.role) }}
                                </button>

                                <div class="ml-2 text-sm text-gray-400" v-else-if="availableRoles.length">
                                    {{ displayableRole(scope.row.role) }}
                                </div>

                                <!-- Leave Team -->
                                <button class="cursor-pointer ml-6 text-sm text-red-500"
                                        @click="confirmLeavingTeam"
                                        v-if="$page.props.user.id === scope.row.id">
                                    {{ $t('leave') }}
                                </button>

                                <!-- Remove Team Member -->
                                <button class="cursor-pointer ml-6 text-sm text-red-500"
                                        @click="confirmTeamMemberRemoval(scope.row)"
                                        v-else-if="userPermissions.canRemoveTeamMembers">
                                    {{ $t('remove') }}
                                </button>
                            </div>
                        </template>
                    </el-table-column>
                </el-table>

            </template>
        </jet-action-section>

        <!-- Role Management Modal -->
        <jet-dialog-modal :show="currentlyManagingRole" @close="currentlyManagingRole = false">
            <template #title>
                {{ $t('team_manage_role') }}
            </template>

            <template #content>
                <div v-if="managingRoleFor">
                    <div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer">
                        <button type="button" class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue"
                                :class="{'border-t border-gray-200 rounded-t-none': i > 0, 'rounded-b-none': i !== Object.keys(availableRoles).length - 1}"
                                @click="updateRoleForm.role = role.key"
                                v-for="(role, i) in availableRoles"
                                :key="role.key">
                            <div :class="{'opacity-50': updateRoleForm.role && updateRoleForm.role !== role.key}">
                                <!-- Role Name -->
                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600" :class="{'font-semibold': updateRoleForm.role === role.key}">
                                        {{ role.name }}
                                    </div>

                                    <svg v-if="updateRoleForm.role === role.key" class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
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

            <template #footer>
                <el-button round native-type="button" @click.native="currentlyManagingRole = false">
                    {{ $t('nevermind') }}
                </el-button>

                <el-button round native-type="submit" type="primary" class="ml-2" @click.native="updateRole" :class="{ 'opacity-25': updateRoleForm.processing }" :disabled="updateRoleForm.processing">
                    {{ $t('save') }}
                </el-button>
            </template>
        </jet-dialog-modal>

        <!-- Leave Team Confirmation Modal -->
        <jet-confirmation-modal :show="confirmingLeavingTeam" @close="confirmingLeavingTeam = false">
            <template #title>
                {{ $t('team_member_manager.leave_team_modal.title') }}
            </template>

            <template #content>
                {{ $t('team_member_manager.leave_team_modal.content') }}
            </template>

            <template #footer>
                <el-button round native-type="button" @click.native="confirmingLeavingTeam = false">
                    {{ $t('nevermind') }}
                </el-button>

                <el-button round native-type="button" type="danger" class="ml-2" @click.native="leaveTeam" :class="{ 'opacity-25': leaveTeamForm.processing }" :disabled="leaveTeamForm.processing">
                    {{ $t('leave') }}
                </el-button>
            </template>
        </jet-confirmation-modal>

        <!-- Remove Team Member Confirmation Modal -->
        <jet-confirmation-modal :show="teamMemberBeingRemoved" @close="teamMemberBeingRemoved = null">
            <template #title>
                {{ $t('team_member_manager.remove_member_modal.title') }}
            </template>

            <template #content>
                {{ $t('team_member_manager.remove_member_modal.content') }}
            </template>

            <template #footer>
                <el-button round native-type="button" @click.native="teamMemberBeingRemoved = null">
                    {{ $t('nevermind') }}
                </el-button>

                <el-button round native-type="button" type="danger" class="ml-2" @click.native="removeTeamMember" :class="{ 'opacity-25': removeTeamMemberForm.processing }" :disabled="removeTeamMemberForm.processing">
                    {{ $t('remove') }}
                </el-button>
            </template>
        </jet-confirmation-modal>
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

export default {
    components: {
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
        manageRole(teamMember) {
            this.managingRoleFor = teamMember
            this.updateRoleForm.role = teamMember.role
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
