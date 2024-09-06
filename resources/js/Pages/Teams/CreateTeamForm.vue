<template>
    <jet-form-section @submitted="createTeam">
        <template #title>
            {{ $t('team_create') }}
        </template>

        <template #description>
            {{ $t('team_member_manager.team_details.description') }}
        </template>

        <template #form>
            <div class="col-span-6">
                <jet-label :value="$t('team_owner')" />

                <div class="flex items-center mt-2">
                    <img class="w-12 h-12 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name">

                    <div class="ml-4 leading-tight">
                        <div>{{ $page.props.user.name }}</div>
                        <div class="text-gray-700 text-sm">{{ $page.props.user.email }}</div>
                    </div>
                </div>
            </div>

            <div class="col-span-6 lg:col-span-5">
                <jet-label for="name" :value="$t('team_name')" />
                <el-input id="name" type="text" class="mt-1 block" v-model="form.name" autofocus />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <el-button round native-type="submit" type="primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ $t('create') }}
            </el-button>
        </template>
    </jet-form-section>
</template>

<script>
    import JetFormSection from '@/Jetstream/FormSection'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'

    export default {
        components: {
            JetFormSection,
            JetInputError,
            JetLabel,
        },

        data() {
            return {
                form: this.$inertia.form({
                    name: '',
                })
            }
        },

        methods: {
            createTeam() {
                this.form.post(route('teams.store'), {
                    errorBag: 'createTeam',
                    preserveScroll: true
                });
            },
        },
    }
</script>
