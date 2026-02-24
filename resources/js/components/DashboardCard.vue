<template>
  <div class="card" @click="$router.push(route)">
    <div class="icon">{{ icon }}</div>
    <h3>{{ title }}</h3>
  </div>
</template>

<script>
export default {
  props: {
    title: String,
    icon: String,
    route: String,
  },
};
</script>

<style scoped>
.card {
  background: white;
  padding: 30px;
  text-align: center;
  border-radius: 10px;
  cursor: pointer;
  transition: 0.2s;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.card:hover {
  transform: translateY(-5px);
}

.icon {
  font-size: 32px;
  margin-bottom: 10px;
}
</style>
