<template>
  <div v-if="show" class="modal-backdrop fade show" style="display: block; opacity: 1;">
    <div class="modal fade show" tabindex="-1" style="display: block; opacity: 1;">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEdit ? 'Edit Task' : 'Create New Task' }}</h5>
            <button type="button" class="btn-close" @click="$emit('close')" aria-label="Close"></button>
          </div>
          
          <form @submit.prevent="handleSubmit">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label" for="title">Title</label>
                <input
                  id="title"
                  v-model="form.title"
                  type="text"
                  class="form-control"
                  required
                />
              </div>

              <div class="mb-3">
                <label class="form-label" for="description">Description</label>
                <textarea
                  id="description"
                  v-model="form.description"
                  class="form-control"
                  rows="3"
                ></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label" for="status">Status</label>
                <select
                  id="status"
                  v-model="form.status"
                  class="form-select"
                  required
                >
                  <option value="pending">Pending</option>
                  <option value="in_progress">In Progress</option>
                  <option value="completed">Completed</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label" for="priority">
                  Priority ({{ form.priority }})
                </label>
                <input
                  id="priority"
                  v-model.number="form.priority"
                  type="range"
                  class="form-range"
                  min="0"
                  max="5"
                  step="1"
                />
                <div class="d-flex justify-content-between text-muted small">
                  <span>Low</span>
                  <span>High</span>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label" for="due_date">Due Date</label>
                <input
                  id="due_date"
                  v-model="form.due_date"
                  type="date"
                  class="form-control"
                />
              </div>
            </div>

            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                @click="$emit('close')"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="btn btn-primary"
              >
                {{ isEdit ? 'Update' : 'Create' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    required: true
  },
  task: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'submit'])

const isEdit = ref(false)
const form = ref({
  title: '',
  description: '',
  status: 'pending',
  priority: 0,
  due_date: null
})

watch(() => props.task, (newTask) => {
  if (newTask) {
    isEdit.value = true
    form.value = { ...newTask }
  } else {
    isEdit.value = false
    form.value = {
      title: '',
      description: '',
      status: 'pending',
      priority: 0,
      due_date: null
    }
  }
}, { immediate: true })

const handleSubmit = () => {
  emit('submit', form.value)
}
</script>

<style>
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1050;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.5);
  opacity: 1 !important;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1055;
  width: 100%;
  height: 100%;
  overflow-x: hidden;
  overflow-y: auto;
  outline: 0;
  opacity: 1 !important;
}

.modal.show {
  display: block !important;
  opacity: 1 !important;
}

.modal-dialog {
  transform: none !important;
  margin: 1.75rem auto;
}

.modal-content {
  position: relative;
  display: flex;
  flex-direction: column;
  width: 100%;
  pointer-events: auto;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid rgba(0, 0, 0, 0.2);
  border-radius: 0.3rem;
  outline: 0;
}
</style> 