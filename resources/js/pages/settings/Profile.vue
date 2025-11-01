<script setup lang="ts">
import { edit, update } from '@/routes/profile'
import organizationRoutes from '@/routes/organization'
import { send } from '@/routes/verification'
import { Form, Head, Link, usePage } from '@inertiajs/vue3'

import DeleteUser from '@/components/DeleteUser.vue'
import HeadingSmall from '@/components/HeadingSmall.vue'
import InputError from '@/components/InputError.vue'
import Textarea from '@/components/ui/textarea/Textarea.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import AppLayout from '@/layouts/AppLayout.vue'
import SettingsLayout from '@/layouts/settings/Layout.vue'
import { type BreadcrumbItem } from '@/types'

interface Props {
    mustVerifyEmail: boolean
    status?: string
}

defineProps<Props>()

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: edit().url,
    },
]

const page = usePage()
const user = page.props.auth.user
const isAdmin = user.role === 'admin'
const organization = user.organization || {}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Profile settings" />

        <SettingsLayout>
            <!-- ================= PROFILE INFO ================= -->
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Profile information" description="Update your name and email address" />

                <Form v-bind="update.form()" class="space-y-6" v-slot="{ errors, processing, recentlySuccessful }">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" class="mt-1 block w-full" name="name" :default-value="user.name" required
                            autocomplete="name" placeholder="Full name" />
                        <InputError class="mt-2" :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input id="email" type="email" class="mt-1 block w-full" name="email"
                            :default-value="user.email" required autocomplete="username" placeholder="Email address" />
                        <InputError class="mt-2" :message="errors.email" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link :href="send()" as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500">
                            Click here to resend the verification email.
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            A new verification link has been sent to your email
                            address.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="processing" data-test="update-profile-button">
                            Save
                        </Button>

                        <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                            <p v-show="recentlySuccessful" class="text-sm text-neutral-600">
                                Saved.
                            </p>
                        </Transition>
                    </div>
                </Form>
            </div>

            <!-- ================= ORGANIZATION INFO (Admin Only) ================= -->
            <div v-if="isAdmin" class="flex flex-col space-y-6 mt-10">
                <HeadingSmall title="Organization information"
                    description="Manage your organization details and settings" />

                <Form v-bind="organizationRoutes.update.form()" class="space-y-6"
                    v-slot="{ errors, processing, recentlySuccessful }">
                    <div class="grid gap-2">
                        <Label for="org_name">Organization Name</Label>
                        <Input id="org_name" name="org_name" :default-value="organization.org_name" required
                            placeholder="Organization name" />
                        <InputError class="mt-2" :message="errors.org_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="org_bank">Bank Information</Label>
                        <Input id="org_bank" name="org_bank" :default-value="organization.org_bank"
                            placeholder="Bank name / account info" />
                        <InputError class="mt-2" :message="errors.org_bank" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="org_modality">Payment Modality</Label>
                        <Input id="org_modality" name="org_modality" :default-value="organization.org_modality"
                            placeholder="e.g., Payment à 30 jours" />
                        <InputError class="mt-2" :message="errors.org_modality" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="org_footer">Footer Text</Label>
                        <Textarea id="org_footer" name="org_footer" :default-value="organization.org_footer"
                            placeholder="Footer text for invoices" rows="4" />
                        <InputError class="mt-2" :message="errors.org_footer" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="org_logo">Organization Logo</Label>
                        <Textarea id="org_logo" name="org_logo" :default-value="organization.org_logo"
                            placeholder="Logo BASE 64" rows="4" />
                        <InputError class="mt-2" :message="errors.org_logo" />
                        <!-- <div v-if="organization.org_logo" class="mt-2">
                            <img :src="`/storage/${organization.org_logo}`" alt="Organization logo"
                                class="h-12 rounded-md border" />
                        </div> -->
                    </div>

                    <div class="grid gap-2">
                        <Label for="org_color">Organization Color</Label>
                        <Input id="org_color" name="org_color" :default-value="organization.org_color"
                            placeholder="#ffff" rows="4" />
                        <InputError class="mt-2" :message="errors.org_color" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button type="submit" :disabled="processing" data-test="update-org-button">
                            Save Organization
                        </Button>
                        <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                            <p v-show="recentlySuccessful" class="text-sm text-neutral-600">
                                Saved.
                            </p>
                        </Transition>
                    </div>
                </Form>
            </div>

            <!-- ================= DELETE USER ================= -->
            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
