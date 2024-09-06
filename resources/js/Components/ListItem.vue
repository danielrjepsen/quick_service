<template>
  <inertia-link :href="route(route_name)" class="w-auto md:w-4/5 mt-2">
    <li class="w-full rounded-full py-2 px-5 hover:bg-gray-200">
      {{link_text}}
    </li>
  </inertia-link>
</template>
<script>
export default {
  name: 'ListItem',
  props: {
    link_text: '',
    route_name: ''
  }
}
</script>
