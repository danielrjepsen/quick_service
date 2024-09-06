<template>
    <jet-authentication-card>
        <template #logo>
            <authentication-card-banner />
        </template>

        <h1 class="text-4xl font-bold mb-3">{{ $t('register') }} <template v-if="organisationName">on {{ organisationName }}</template></h1>

        <jet-validation-errors class="mb-4" />

        <form @submit.prevent="submit">
            <div>
                <jet-label for="name" :value="$t('name')" />
                <el-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <jet-label for="email" :value="$t('email')" />
                <el-input v-if="invitation === undefined" id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                <el-input v-else type="email" class="mt-1 block w-full" v-model="form.email" required disabled />
            </div>

            <div class="mt-4" v-if="invitation === undefined">
                <jet-label for="organisation" :value="$t('organisation')" />
                <el-input id="organisation" type="text" class="mt-1 block w-full" v-model="form.organisation" required />
            </div>

            <input v-else type="hidden" name="invitation" :value="invitation.id">

            <div class="mt-4">
                <jet-label for="password" :value="$t('password')" />
                <el-input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <jet-label for="password_confirmation" :value="$t('confirmPassword')" />
                <el-input id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="mt-4" v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature">
                <jet-label for="terms">
                    <div class="flex items-center">
                        <jet-checkbox name="terms" id="terms" v-model="form.terms" />

                        <div class="ml-2" v-html="$t('agree_to_the_terms_and_privacy_police', {terms_of_service, privacy_policy})">
                        </div>
                    </div>
                </jet-label>
            </div>

            <div class="flex items-center justify-start mt-4">
                <el-button round native-type="submit" type="primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ $t('register') }}
                </el-button>

                <inertia-link :href="route('login')" class="ml-4 underline text-sm text-gray-600 hover:text-gray-900">
                    {{ $t('already_registered') }}
                </inertia-link>
            </div>
        </form>
    </jet-authentication-card>
</template>

<script>
    import JetAuthenticationCard from '@/Jetstream/AuthenticationCard'
    import AuthenticationCardBanner from '@/Components/AuthenticationCardBanner'
    import JetCheckbox from "@/Jetstream/Checkbox";
    import JetLabel from '@/Jetstream/Label'
    import JetValidationErrors from '@/Jetstream/ValidationErrors'

    export default {
        components: {
            JetAuthenticationCard,
            AuthenticationCardBanner,
            JetCheckbox,
            JetLabel,
            JetValidationErrors
        },

        props: {
            invitation: Object,
            organisation: {
                type: String,
                default: null
            }
        },

        data() {
            return {
                form: this.$inertia.form({
                    name: '',
                    email: this.invitation ? this.invitation.email : '',
                    organisation: '',
                    password: '',
                    invitation: this.invitation ? this.invitation.id : null,
                    password_confirmation: '',
                    terms: false,
                }),
                organisationName: this.organisation ? this.organisation : '',
                terms_of_service: route('terms.show'),
                privacy_policy: route('policy.show')
            }
        },

        methods: {
            submit() {
                this.form.post(this.route('register'), {
                    onFinish: () => this.form.reset('password', 'password_confirmation'),
                })
            }
        }
    }
</script>
