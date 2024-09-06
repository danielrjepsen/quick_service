<template>
    <jet-action-section class="mt-10 sm:mt-0">
        <template #title>
            {{ $t('organisation_members.title') }}
        </template>

        <template #description>
            {{ $t('organisation_members.description') }}
        </template>

        <!-- Organisation Members List -->
        <template #content>
            <el-table
                :data="users"
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
            </el-table>

        </template>
    </jet-action-section>
</template>

<script>
import JetActionSection from "@/Jetstream/ActionSection"

export default {
    components: {JetActionSection},
    props: [
        'users',
        'organisation'
    ],
}
</script>
