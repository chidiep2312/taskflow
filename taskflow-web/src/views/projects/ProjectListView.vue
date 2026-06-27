<template>
  <div class="container mt-4">
    <div class="hero-panel">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <h1 class="page-title">Projects ✿</h1>
          <p class="page-subtitle mb-0">
            Build, organize, and finish your work with style.
          </p>
        </div>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProjectModal">
          + New Project
        </button>
      </div>
    </div>

    <div class="card p-3 mb-4">
      <div class="row g-2">
        <div class="col-md-8">
          <input
            v-model="filters.search"
            type="text"
            class="form-control"
            placeholder="Search project..."
            @keyup.enter="loadProjects"
          />
        </div>

        <div class="col-md-3">
          <select v-model="filters.status" class="form-select">
            <option value="">All status</option>
            <option value="active">Active</option>
            <option value="completed">Completed</option>
            <option value="archived">Archived</option>
          </select>
        </div>

        <div class="col-md-1">
          <button class="btn btn-dark w-100" @click="loadProjects">
            Go
          </button>
        </div>
      </div>
    </div>

    <div v-if="projectStore.error" class="alert alert-danger">
      {{ projectStore.error }}
    </div>

    <div v-if="projectStore.loading" class="alert alert-info">
      Loading projects...
    </div>

    <div v-if="!projectStore.loading && projectStore.projects.length === 0" class="alert alert-warning">
      No projects found.
    </div>

    <div class="row">
      <div
        v-for="project in projectStore.projects"
        :key="project.id"
        class="col-md-4 mb-4"
      >
        <div class="card project-card h-100 p-4">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <h5 class="project-name">{{ project.name }}</h5>

            <span
              class="badge"
              :class="project.status === 'active' ? 'badge-pink' : 'badge-yellow'"
            >
              {{ project.status }}
            </span>
          </div>

          <p class="page-subtitle project-desc">
            {{ project.description || 'No description' }}
          </p>

          <div class="project-meta mb-4">
            <span>✦ Tasks: {{ project.tasks_count ?? 0 }}</span>
            <span>✿ {{ project.created_at }}</span>
          </div>

          <div class="d-flex gap-2 mt-auto">
            <router-link :to="`/projects/${project.id}`" class="btn btn-primary flex-fill">
              View
            </router-link>

            <button class="btn btn-outline-danger" @click="remove(project.id)">
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="createProjectModal" tabindex="-1">
      <div class="modal-dialog">
        <form class="modal-content" @submit.prevent="create">
          <div class="modal-header">
            <h5 class="modal-title">Create Project ✦</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <div class="mb-3">
              <label>Name</label>
              <input v-model="form.name" class="form-control" required />
            </div>

            <div class="mb-3">
              <label>Description</label>
              <textarea v-model="form.description" class="form-control"></textarea>
            </div>

            <div class="mb-3">
              <label>Status</label>
              <select v-model="form.status" class="form-select">
                <option value="active">Active</option>
                <option value="completed">Completed</option>
                <option value="archived">Archived</option>
              </select>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
              Cancel
            </button>
            <button class="btn btn-primary" :disabled="projectStore.loading">
              Save Project
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive } from 'vue'
import { useProjectStore } from '../../stores/projectStore'

const projectStore = useProjectStore()

const filters = reactive({
  search: '',
  status: ''
})

const form = reactive({
  name: '',
  description: '',
  status: 'active'
})

onMounted(() => {
  loadProjects()
})

function loadProjects() {
  projectStore.fetchProjects(filters)
}

async function create() {
  await projectStore.createProject(form)

  form.name = ''
  form.description = ''
  form.status = 'active'

  await loadProjects()

  document.querySelector('#createProjectModal .btn-close')?.click()
}

async function remove(id) {
  if (!confirm('Delete this project?')) return

  await projectStore.deleteProject(id)
}
</script>