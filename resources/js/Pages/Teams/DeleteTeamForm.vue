<template>
    <jet-action-section>
        <template #title>
            {{ $t('team_member_manager.team_delete.title') }}
        </template>

        <template #description>
            {{ $t('team_member_manager.team_delete.description') }}
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                {{ $t('team_member_manager.team_delete.content') }}
            </div>

            <div class="mt-5">
                <el-button round native-type="button" type="danger" @click.native="confirmTeamDeletion">
                    {{ $t('team_member_manager.team_delete.btn_delete') }}
                </el-button>
            </div>

            <!-- Delete Team Confirmation Modal -->
            <jet-confirmation-modal :show="confirmingTeamDeletion" @close="confirmingTeamDeletion = false">
                <template #title>
                    {{ $t('team_member_manager.team_delete.title') }}
                </template>

                <template #content>
                    {{ $t('team_member_manager.team_delete.modal_content') }}
                </template>

                <template #footer>
                    <el-button round native-type="button" @click.native="confirmingTeamDeletion = false">
                        {{ $t('nevermind') }}
                    </el-button>

                    <el-button round native-type="button" type="danger" class="ml-2" @click.native="deleteTeam" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        {{ $t('team_member_manager.team_delete.btn_delete') }}
                    </el-button>
                </template>
            </jet-confirmation-modal>
        </template>
    </jet-action-section>
</template>

<script>
    import JetActionSection from '@/Jetstream/ActionSection'
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal'

    export default {
        props: ['team'],

        components: {
            JetActionSection,
            JetConfirmationModal,
        },

        data() {
            return {
                confirmingTeamDeletion: false,
                deleting: false,

                form: this.$inertia.form()
            }
        },

        methods: {
            confirmTeamDeletion() {
                this.confirmingTeamDeletion = true
            },

            deleteTeam() {
                this.form.delete(route('teams.destroy', this.team), {
                    errorBag: 'deleteTeam'
                });
            },
        },
    }
</script>
