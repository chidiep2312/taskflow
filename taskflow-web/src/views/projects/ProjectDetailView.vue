<template>
  <div class="container mt-4">
    <div v-if="projectStore.loading" class="alert alert-info">
      Loading project...
    </div>

    <div v-if="projectStore.error" class="alert alert-danger">
      {{ projectStore.error }}
    </div>

    <div v-if="projectStore.project">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <div class="d-flex align-items-center gap-2">
            <h3 class="mb-0">{{ projectStore.project.name }}</h3>

            <span class="badge" :class="{
              'badge-pink': myRole === 'owner',
              'badge-yellow': myRole === 'member',
              'badge-secondary': myRole === 'viewer'
            }">
              {{ myRole }}
            </span>
          </div>

          <p class="text-muted mb-0 mt-2">
            {{ projectStore.project.description || 'No description' }}
          </p>
          <div v-if="myRole === 'viewer'" class="alert alert-warning mt-3">
            You are viewing this project in read-only mode.
          </div>
        </div>

        <router-link to="/projects" class="btn btn-outline-secondary">
          Back
        </router-link>
      </div>

      <div v-if="isOwner" class="card mb-4">
        <div class="card-body">
          <form @submit.prevent="updateProject">
            <div class="row g-2">
              <div class="col-md-4">
                <label>Name</label>
                <input v-model="projectForm.name" class="form-control" />
              </div>

              <div class="col-md-5">
                <label>Description</label>
                <input v-model="projectForm.description" class="form-control" />
              </div>

              <div class="col-md-3">
                <label>Status</label>
                <select v-model="projectForm.status" class="form-select">
                  <option value="active">Active</option>
                  <option value="completed">Completed</option>
                  <option value="archived">Archived</option>
                </select>
              </div>
            </div>

            <button class="btn btn-primary mt-3">
              Update Project
            </button>
          </form>
        </div>
      </div>
      <div class="card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="mb-1">Project Members ✦</h5>
            <p class="page-subtitle mb-0">
              Manage people who can access this project.
            </p>
          </div>
        </div>

        <div v-if="memberStore.error" class="alert alert-danger">
          {{ memberStore.error }}
        </div>

        <form v-if="isOwner" class="row g-2 mb-3" @submit.prevent="addMember">
          <div class="col-md-7">
            <input v-model="memberForm.email" type="email" class="form-control" placeholder="Member email..."
              required />
          </div>

          <div class="col-md-3">
            <select v-model="memberForm.role" class="form-select">
              <option value="member">Member</option>
              <option value="viewer">Viewer</option>
            </select>
          </div>

          <div class="col-md-2">
            <button class="btn btn-primary w-100">
              Add
            </button>
          </div>
        </form>

        <div v-if="memberStore.loading" class="page-subtitle">
          Loading members...
        </div>

        <div v-for="member in memberStore.members" :key="member.id" class="member-item">
          <div>
            <strong>{{ member.name }}</strong>
            <div class="page-subtitle small">
              {{ member.email }}
            </div>
          </div>

          <div class="d-flex align-items-center gap-2">
            <span v-if="member.role === 'owner'" class="badge badge-pink">
              owner
            </span>

            <select v-if="isOwner && member.role !== 'owner'" class="form-select form-select-sm" :value="member.role"
              @change="updateMemberRole(member, $event.target.value)">
              <option value="member">member</option>
              <option value="viewer">viewer</option>
            </select>

            <button v-if="isOwner && member.role !== 'owner'" type="button" class="btn btn-sm btn-outline-danger"
              @click="removeMember(member)">
              Remove
            </button>
            <span v-if="!isOwner || member.role === 'owner'" class="badge badge-yellow">
              {{ member.role }}
            </span>
          </div>
        </div>
      </div>
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Tasks</h4>

        <button v-if="canManageTasks" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaskModal">
          + New Task
        </button>
      </div>

      <div class="card mb-3">
        <div class="card-body row g-2">
          <div class="col-md-6">
            <input v-model="filters.search" class="form-control" placeholder="Search task..."
              @keyup.enter="loadTasks" />
          </div>

          <div class="col-md-3">
            <select v-model="filters.status" class="form-select">
              <option value="">All status</option>
              <option value="todo">Todo</option>
              <option value="in_progress">In Progress</option>
              <option value="done">Done</option>
            </select>
          </div>

          <div class="col-md-2">
            <select v-model="filters.priority" class="form-select">
              <option value="">All priority</option>
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
            </select>
          </div>

          <div class="col-md-1">
            <button class="btn btn-dark w-100" @click="loadTasks">
              Go
            </button>
          </div>
        </div>
      </div>

      <div v-if="taskStore.error" class="alert alert-danger">
        {{ taskStore.error }}
      </div>

      <div v-if="taskStore.loading" class="alert alert-info">
        Loading tasks...
      </div>

      <div class="row">
        <div class="col-md-4">
          <h5 class="column-title pink">To Do</h5>
          <TaskColumn :can-manage="canManageTasks" :tasks="todoTasks" @edit="openEdit" @delete="removeTask"
            @change-status="changeStatus" />
        </div>

        <div class="col-md-4">
          <h5 class="column-title orange">In Progress</h5>
          <TaskColumn :can-manage="canManageTasks" :tasks="inProgressTasks" @edit="openEdit" @delete="removeTask"
            @change-status="changeStatus" />
        </div>

        <div class="col-md-4">
          <h5 class="column-title yellow">Done</h5>
          <TaskColumn :can-manage="canManageTasks" :tasks="doneTasks" @edit="openEdit" @delete="removeTask"
            @change-status="changeStatus" />
        </div>
      </div>

      <!-- Create Task Modal -->
      <div class="modal fade" id="createTaskModal" tabindex="-1">
        <div class="modal-dialog">
          <form class="modal-content" @submit.prevent="createTask">
            <div class="modal-header">
              <h5 class="modal-title">Create Task</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
              <div class="mb-3">
                <label>Title</label>
                <input v-model="taskForm.title" class="form-control" required />
              </div>

              <div class="mb-3">
                <label>Description</label>
                <textarea v-model="taskForm.description" class="form-control"></textarea>
              </div>

              <div class="mb-3">
                <label>Status</label>
                <select v-model="taskForm.status" class="form-select">
                  <option value="todo">Todo</option>
                  <option value="in_progress">In Progress</option>
                  <option value="done">Done</option>
                </select>
              </div>

              <div class="mb-3">
                <label>Priority</label>
                <select v-model="taskForm.priority" class="form-select">
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                </select>
              </div>

              <div class="mb-3">
                <label>Due Date</label>
                <input v-model="taskForm.due_date" type="date" class="form-control" />
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                Cancel
              </button>
              <button class="btn btn-primary">
                Save
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Edit Task Modal -->
      <div class="modal fade" id="editTaskModal" tabindex="-1">
        <div class="modal-dialog">
          <form class="modal-content" @submit.prevent="updateTask">
            <div class="modal-header">
              <h5 class="modal-title">Edit Task</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
              <div class="mb-3">
                <label>Title</label>
                <input v-model="editForm.title" class="form-control" required />
              </div>

              <div class="mb-3">
                <label>Description</label>
                <textarea v-model="editForm.description" class="form-control"></textarea>
              </div>

              <div class="mb-3">
                <label>Status</label>
                <select v-model="editForm.status" class="form-select">
                  <option value="todo">Todo</option>
                  <option value="in_progress">In Progress</option>
                  <option value="done">Done</option>
                </select>
              </div>

              <div class="mb-3">
                <label>Priority</label>
                <select v-model="editForm.priority" class="form-select">
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                </select>
              </div>

              <div class="mb-3">
                <label>Due Date</label>
                <input v-model="editForm.due_date" type="date" class="form-control" />
              </div>
            </div>

            <hr />

            <div class="mb-3 m-3">
              <h6>Comments</h6>

              <div v-if="commentStore.loading" class="page-subtitle">
                Loading comments...
              </div>

              <div v-if="commentStore.comments.length === 0" class="page-subtitle">
                No comments yet.
              </div>

              <div v-for="comment in commentStore.comments" :key="comment.id" class="comment-item">
                <div class="d-flex justify-content-between">
                  <strong>{{ comment.user?.name }}</strong>

                  <button type="button" class="btn btn-sm btn-outline-danger" @click="deleteComment(comment.id)">
                    Delete
                  </button>
                </div>

                <p class="mb-1">{{ comment.content }}</p>

                <small class="page-subtitle">
                  {{ comment.created_at }}
                </small>
              </div>

              <div class="mt-3">
                <textarea v-model="commentForm.content" class="form-control"
                  placeholder="Write a comment..."></textarea>

                <button v-if="canManageTasks" type="button" class="btn btn-primary mt-2" @click="createComment">
                  Add Comment
                </button>
              </div>
            </div>
            <hr />

            <div class="mb-3 m-3">
              <h6>Attachments</h6>

              <div v-if="attachmentStore.loading" class="page-subtitle">
                Loading attachments...
              </div>

              <div v-if="attachmentStore.attachments.length === 0" class="page-subtitle">
                No attachments yet.
              </div>

              <div v-for="attachment in attachmentStore.attachments" :key="attachment.id" class="attachment-item">
                <div>
                  <a :href="`http://127.0.0.1:8000${attachment.url}`" target="_blank" class="attachment-link">
                    {{ attachment.file_name }}
                  </a>

                  <div class="page-subtitle small">
                    {{ formatFileSize(attachment.file_size) }} · {{ attachment.created_at }}
                  </div>
                </div>

                <button type="button" class="btn btn-sm btn-outline-danger" @click="deleteAttachment(attachment.id)">
                  Delete
                </button>
              </div>

              <div v-if="canManageTasks" class="mt-3">
                <input type="file" class="form-control" @change="handleFileChange" />

                <button type="button" class="btn btn-primary mt-2" @click="uploadAttachment">
                  Upload File
                </button>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                Cancel
              </button>
              <button class="btn btn-primary">
                Update
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCommentStore } from '../../stores/commentStore'
import { computed, onMounted, reactive, watch } from 'vue'
import { useRoute } from 'vue-router'
import { Modal } from 'bootstrap'
import { useProjectStore } from '../../stores/projectStore'
import { useTaskStore } from '../../stores/taskStore'
import TaskColumn from '../../components/TaskColumn.vue'
import { useAttachmentStore } from '../../stores/attachmentStore'
import { useMemberStore } from '../../stores/memberStore'
const route = useRoute()
const projectStore = useProjectStore()
const taskStore = useTaskStore()
const memberStore = useMemberStore()
const myRole = computed(() => projectStore.project?.my_role)

const isOwner = computed(() => myRole.value === 'owner')

const canManageTasks = computed(() =>
  ['owner', 'member'].includes(myRole.value)
)
const projectForm = reactive({
  name: '',
  description: '',
  status: 'active'
})
const memberForm = reactive({
  email: '',
  role: 'member'
})
const taskForm = reactive({
  title: '',
  description: '',
  status: 'todo',
  priority: 'medium',
  due_date: ''
})
const attachmentStore = useAttachmentStore()
const selectedFile = reactive({
  file: null
})
const editForm = reactive({
  id: null,
  title: '',
  description: '',
  status: 'todo',
  priority: 'medium',
  due_date: ''
})

const filters = reactive({
  search: '',
  status: '',
  priority: ''
})

const todoTasks = computed(() =>
  taskStore.tasks.filter(task => task.status === 'todo')
)

const inProgressTasks = computed(() =>
  taskStore.tasks.filter(task => task.status === 'in_progress')
)

const doneTasks = computed(() =>
  taskStore.tasks.filter(task => task.status === 'done')
)
const commentStore = useCommentStore()
const commentForm = reactive({
  content: ''
})

onMounted(async () => {
  await projectStore.fetchProject(route.params.id)
  await loadTasks()
  await memberStore.fetchMembers(route.params.id)
})

watch(
  () => projectStore.project,
  (project) => {
    if (project) {
      projectForm.name = project.name
      projectForm.description = project.description
      projectForm.status = project.status
    }
  },
  { immediate: true }
)
async function addMember() {
  if (!memberForm.email.trim()) return

  await memberStore.addMember(route.params.id, memberForm)

  memberForm.email = ''
  memberForm.role = 'member'

  await memberStore.fetchMembers(route.params.id)
}

async function updateMemberRole(member, role) {
  await memberStore.updateRole(route.params.id, member.id, role)
  await memberStore.fetchMembers(route.params.id)
}

async function removeMember(member) {
  if (!confirm(`Remove ${member.name} from this project?`)) return

  await memberStore.removeMember(route.params.id, member.id)
}
async function updateProject() {
  await projectStore.updateProject(route.params.id, projectForm)
  await projectStore.fetchProject(route.params.id)
  alert('Project updated successfully')
}

async function loadTasks() {
  await taskStore.fetchTasks(route.params.id, filters)
}

async function createTask() {
  await taskStore.createTask(route.params.id, taskForm)

  taskForm.title = ''
  taskForm.description = ''
  taskForm.status = 'todo'
  taskForm.priority = 'medium'
  taskForm.due_date = ''

  await loadTasks()

  document.querySelector('#createTaskModal .btn-close')?.click()
}

async function openEdit(task) {
  editForm.id = task.id
  editForm.title = task.title
  editForm.description = task.description
  editForm.status = task.status
  editForm.priority = task.priority
  editForm.due_date = task.due_date

  await commentStore.fetchComments(task.id)
  await attachmentStore.fetchAttachments(task.id)

  const modal = new Modal(document.getElementById('editTaskModal'))
  modal.show()
}
async function createComment() {
  if (!commentForm.content.trim()) return

  await commentStore.createComment(editForm.id, commentForm)

  commentForm.content = ''

  await commentStore.fetchComments(editForm.id)
}
function handleFileChange(event) {
  selectedFile.file = event.target.files[0]
}

async function uploadAttachment() {
  if (!selectedFile.file) return

  await attachmentStore.uploadAttachment(editForm.id, selectedFile.file)

  selectedFile.file = null

  await attachmentStore.fetchAttachments(editForm.id)
}

async function deleteAttachment(id) {
  if (!confirm('Delete this attachment?')) return

  await attachmentStore.deleteAttachment(id)
}

async function deleteComment(id) {
  if (!confirm('Delete this comment?')) return

  await commentStore.deleteComment(id)
}

async function updateTask() {
  await taskStore.updateTask(editForm.id, {
    title: editForm.title,
    description: editForm.description,
    status: editForm.status,
    priority: editForm.priority,
    due_date: editForm.due_date
  })

  await loadTasks()

  document.querySelector('#editTaskModal .btn-close')?.click()
}

async function changeStatus(task, status) {
  await taskStore.updateTask(task.id, {
    status
  })

  await loadTasks()
}

async function removeTask(id) {
  if (!confirm('Are you sure you want to delete this task?')) return

  await taskStore.deleteTask(id)
}
function formatFileSize(size) {
  if (!size) return '0 KB'

  if (size < 1024) {
    return `${size} B`
  }

  if (size < 1024 * 1024) {
    return `${(size / 1024).toFixed(1)} KB`
  }

  return `${(size / (1024 * 1024)).toFixed(1)} MB`
}
</script>