<template>
  <div>
    <div v-if="tasks.length === 0" class="empty-task">
      No tasks here ✦
    </div>

    <div v-for="task in tasks" :key="task.id" class="task-card" :class="task.priority">
      <div class="d-flex justify-content-between align-items-start">
        <h6 class="task-title">{{ task.title }}</h6>

        <div class="d-flex gap-2">
          <span v-if="task.is_overdue" class="badge overdue-badge">
            Overdue
          </span>

          <span class="badge" :class="task.priority === 'high' ? 'badge-pink' : 'badge-yellow'">
            {{ task.priority }}
          </span>
        </div>
      </div>

      <p class="page-subtitle small mb-3">
        {{ task.description || 'No description' }}
      </p>

      <div class="task-info">
        <span>Due: {{ task.due_date || 'No due date' }}</span>
      </div>

      <select v-if="canManage" class="form-select form-select-sm my-3" :value="task.status"
        @change="$emit('change-status', task, $event.target.value)">
        <option value="todo">Todo</option>
        <option value="in_progress">In Progress</option>
        <option value="done">Done</option>
      </select>
      <div v-else class="page-subtitle small my-3">
        View only
      </div>
      <div v-if="canManage" class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-primary flex-fill" @click="$emit('edit', task)">
          Edit
        </button>

        <button class="btn btn-sm btn-outline-danger" @click="$emit('delete', task.id)">
          Delete
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  tasks: {
    type: Array,
    default: () => []
  },
  canManage: {
    type: Boolean,
    default: false
  }
})

defineEmits(['edit', 'delete', 'change-status'])
</script>