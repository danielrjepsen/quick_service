<template>
    <jet-form-section @submitted="updateTeamName" id="updateTeamName">
        <template #title>
            {{ $t('teamUpdateNameForm.title') }}
        </template>

        <template #description>
            {{ $t('teamUpdateNameForm.description') }}
        </template>

        <template #form>
            <!-- Team Owner Information -->
            <div class="col-span-6">
                <jet-label :value="$t('team_owner')" />

                <div class="flex items-center mt-2">
                    <img class="w-12 h-12 rounded-full object-cover" :src="team.owner.profile_photo_url" :alt="team.owner.name">

                    <div class="ml-4 leading-tight">
                        <div>{{ team.owner.name }}</div>
                        <div class="text-gray-700 text-sm">{{ team.owner.email }}</div>
                    </div>
                </div>
            </div>

            <!-- Team Name -->
            <div class="col-span-6 lg:col-span-5">
                <jet-label for="name" :value="$t('team_name')" />

                <el-input id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            :disabled="! permissions.canUpdateTeam" />

                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>
        </template>

        <template #actions v-if="permissions.canUpdateTeam">
            <el-button round native-type="submit" type="primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ $t('save') }}
            </el-button>
            <jet-action-message :on="form.recentlySuccessful" class="ml-3">
                {{ $t('saved') }}
            </jet-action-message>
        </template>
    </jet-form-section>
</template>

<script>
    import JetActionMessage from '@/Jetstream/ActionMessage'
    import JetFormSection from '@/Jetstream/FormSection'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'

    export default {
        components: {
            JetActionMessage,
            JetFormSection,
            JetInputError,
            JetLabel,
        },

        props: ['team', 'permissions'],

        data() {
            return {
                form: this.$inertia.form({
                    name: this.team.name,
                })
            }
        },

        methods: {
            updateTeamName() {
                this.form.put(route('teams.update', this.team), {
                    errorBag: 'updateTeamName',
                    preserveScroll: true
                });
            },
        },
    }
</script>
