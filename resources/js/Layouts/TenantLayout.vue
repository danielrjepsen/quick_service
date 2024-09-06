<template>
    <div class="min-h-screen">

        <nav>
            <!-- Primary Navigation Menu -->
            <div class="container mx-auto px-4 sm:px-6 xl:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">

                        <!-- Navigation Links -->
                        <div class="space-x-8 sm:-my-px sm:ml-2 sm:flex">
                            <jet-nav-link :href="route('dashboard')" :active="route().current('dashboard')">
                                <span class="2xl:font-black">{{ $t('site_name') }}</span>
                            </jet-nav-link>
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ml-6">

                        <div v-if="$page.props.trial" class="bg-indigo-100 rounded-lg border-0 flex flex-col ml-3 my-1 py-2 px-3 text-gray-900">
                            <span class="text-xs text-center">{{ $tc('trial_days_left', $page.props.trial_days) }}</span>
                            <inertia-link :href="route('billing')" class="text-xs cursor-pointer text-blue-500">{{ $t('add_billing_info') }}</inertia-link>
                        </div>


                        <!-- Settings Dropdown -->
                        <div class="ml-3 relative">
                            <jet-dropdown align="right" width="48">
                                <template #trigger>
                                    <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out">
                                        <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name" />
                                    </button>

                                    <span v-else class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                                {{ $page.props.user.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                </template>

                                <template #content>
                                    <!-- Account Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ $t('menu_manage_account_label') }}
                                    </div>

                                    <jet-dropdown-link :href="route('profile.show')">
                                        {{ $t('menu_profile') }}
                                    </jet-dropdown-link>

                                    <jet-dropdown-link :href="route('api-tokens.index')" v-if="$page.props.jetstream.hasApiFeatures">
                                        {{ $t('menu_api_tokens') }}
                                    </jet-dropdown-link>

                                    <div class="border-t border-gray-100"></div>

                                    <!-- Team Management -->
                                    <template v-if="$page.props.jetstream.hasTeamFeatures">
                                        <!-- Team Switcher -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ $t('menu_team_switch_label') }}
                                        </div>

                                        <template v-for="team in $page.props.user.all_teams">
                                            <form @submit.prevent="switchToTeam(team)" :key="team.id">
                                                <jet-dropdown-link as="button">
                                                    <div class="flex items-center">
                                                        <svg v-if="team.id === $page.props.user.current_team_id" class="mr-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        <div>{{ team.name }}</div>
                                                    </div>
                                                </jet-dropdown-link>
                                            </form>
                                        </template>
                                    </template>

                                    <div class="border-t border-gray-200"></div>

                                    <!-- Authentication -->
                                    <form @submit.prevent="logout">
                                        <jet-dropdown-link as="button">
                                            {{ $t('logout') }}
                                        </jet-dropdown-link>
                                    </form>
                                </template>
                            </jet-dropdown>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-mr-2 flex items-center sm:hidden">

                        <div class="mr-6 lg:mr-12">{{ $page.props.user.name }}</div>

                        <button @click="showingNavigationDropdown = ! showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-full text-gray-400 border hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <jet-responsive-nav-link :href="route('dashboard')" :active="route().current('dashboard')">
                        {{ $t('dashboard') }}
                    </jet-responsive-nav-link>
                </div>

                <!-- Responsive Settings Options -->
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="space-y-1">
                        <jet-responsive-nav-link :href="route('service.index')" :active="route().current('serviceReport.show')">
                            {{ $t('menu_service') }}
                        </jet-responsive-nav-link>

                        <jet-responsive-nav-link :href="route('product.index')" :active="route().current('units.show')">
                            {{ $t('menu_units') }}
                        </jet-responsive-nav-link>

                        <jet-responsive-nav-link :href="route('dashboard')" :active="route().current('users.show')">
                            {{ $t('menu_users') }}
                        </jet-responsive-nav-link>

                        <jet-responsive-nav-link :href="route('settings')" :active="route().current('settings')">
                            {{ $t('menu_settings') }}
                        </jet-responsive-nav-link>

                        <div class="border-t border-gray-200"></div>

                        <div class="flex items-center px-4 pt-4">
                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex-shrink-0 mr-3" >
                                <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name" />
                            </div>

                            <div>
                                <div class="font-medium text-base text-gray-800">{{ $page.props.user.name }}</div>
                                <div class="font-medium text-sm text-gray-500">{{ $page.props.user.email }}</div>
                            </div>
                        </div>

                        <jet-responsive-nav-link :href="route('profile.show')" :active="route().current('profile.show')">
                            {{ $t('profile') }}
                        </jet-responsive-nav-link>

                        <jet-responsive-nav-link :href="route('api-tokens.index')" :active="route().current('api-tokens.index')" v-if="$page.props.jetstream.hasApiFeatures">
                            {{ $t('menu_api_tokens') }}
                        </jet-responsive-nav-link>

                        <!-- Team Management -->
                        <template v-if="$page.props.jetstream.hasTeamFeatures">
                            <div class="border-t border-gray-200"></div>

                            <!-- Team Switcher -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ $t('menu_team_switch_label') }}
                            </div>

                            <template v-for="team in $page.props.user.all_teams">
                                <form @submit.prevent="switchToTeam(team)" :key="team.id">
                                    <jet-responsive-nav-link as="button">
                                        <div class="flex items-center">
                                            <svg v-if="team.id === $page.props.user.current_team_id" class="mr-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <div>{{ team.name }}</div>
                                        </div>
                                    </jet-responsive-nav-link>
                                </form>
                            </template>
                        </template>

                        <div class="border-t border-gray-200"></div>

                        <!-- Authentication -->
                        <form method="POST" @submit.prevent="logout">
                            <jet-responsive-nav-link as="button">
                                {{ $t('logout') }}
                            </jet-responsive-nav-link>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container mx-auto lg:flex lg:flex-nowrap">
            <ul class="h-auto hidden whitespace-nowrap items-center text-center md:flex lg:text-left lg:flex-wrap lg:min-h-full lg:w-60 lg:flex-nowrap lg:flex-col lg:mt-10" :class="{'lg:border-none': showingNavigationDropdown }">
                <ListItem :link_text="$t('menu_service')" route_name="service.index"/>
                <ListItem :link_text="$t('menu_units')" route_name="product.index"/>
                <ListItem :link_text="$t('menu_users')" route_name="dashboard"/>

                <inertia-link :href="route('settings')" class="w-auto md:w-4/5 mt-2 lg:mt-40">
                    <li class="w-full rounded-full py-2 px-5 text-black lg:text-white lg:bg-gray-600 lg:text-center hover:bg-gray-500">
                        {{ $t('menu_settings') }}
                    </li>
                </inertia-link>
            </ul>

            <!-- Page Content -->
            <main class="mt-6 rounded-tl-3xl rounded-tr-3xl py-3 px-6 bg-gradient-to-b from-gray-100 w-full">

                <!-- Page Heading -->
                <header>
                    <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
                        <slot name="header"></slot>
                    </div>
                </header>

                <!-- Content -->
                <slot></slot>
            </main>
        </div>

        <!-- Modal Portal -->
        <portal-target name="modal" multiple>
        </portal-target>

    </div>
</template>

<script>
import JetApplicationMark from '@/Jetstream/ApplicationMark'
import JetNavLink from '@/Jetstream/NavLink'
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import Button from "@/Jetstream/Button";
import ListItem from "@/Components/ListItem";
import JetDropdown from '@/Jetstream/Dropdown'
import JetDropdownLink from '@/Jetstream/DropdownLink'

export default {
    name: "TenantLayout",
    components: {
        ListItem,
        Button,
        JetApplicationMark,
        JetNavLink,
        JetResponsiveNavLink,
        JetDropdown,
        JetDropdownLink
    },

    data() {
        return {
            showingNavigationDropdown: false,
        }
    },

    methods: {
        switchToTeam(team) {
            this.$inertia.put(route('current-team.update'), {
                'team_id': team.id
            }, {
                preserveState: false
            })
        },

        logout() {
            this.$inertia.post(route('logout'));
        },
    }
}
</script>

