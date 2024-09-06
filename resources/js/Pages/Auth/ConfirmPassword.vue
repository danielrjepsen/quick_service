<template>
    <jet-authentication-card>
        <template #logo>
            <authentication-card-banner />
        </template>

        <div class="mb-4 text-sm text-gray-600">
            {{ $t('secure_area') }}
        </div>

        <jet-validation-errors class="mb-4" />

        <form @submit.prevent="submit">
            <div>
                <jet-label for="password" :value="$t('password')" />
                <el-input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="current-password" autofocus />
            </div>

            <div class="flex justify-end mt-4">
                <el-button round native-type="submit" type="primary" class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ $t('confirm') }}
                </el-button>
            </div>
        </form>
    </jet-authentication-card>
</template>

<script>
    import JetAuthenticationCard from '@/Jetstream/AuthenticationCard'
    import AuthenticationCardBanner from '@/Components/AuthenticationCardBanner'
    import JetLabel from '@/Jetstream/Label'
    import JetValidationErrors from '@/Jetstream/ValidationErrors'

    export default {
        components: {
            JetAuthenticationCard,
            AuthenticationCardBanner,
            JetLabel,
            JetValidationErrors
        },

        data() {
            return {
                form: this.$inertia.form({
                    password: '',
                })
            }
        },

        methods: {
            submit() {
                this.form.post(this.route('password.confirm'), {
                    onFinish: () => this.form.reset(),
                })
            }
        }
    }
</script>
