<template>
    <div class="min-h-screen w-full flex">
        <div class="hidden lg:block w-1/3">
            <slot name="logo" />
        </div>

        <div class="flex-grow flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-white">
            <div class="w-full sm:w-2/4 mt-6 px-6 py-4 overflow-hidden sm:rounded-lg">
                <slot />
            </div>
        </div>
    </div>
</template>
