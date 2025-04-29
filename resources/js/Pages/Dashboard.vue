<template>
  <div class="container py-5">
    <!-- Header -->
    <div class="row mb-4 align-items-center">
      <div class="col">
        <h1 class="display-5 fw-bold text-primary mb-0">Task Dashboard</h1>
      </div>
      <div class="col-auto">
        <button
          @click="showCreateModal = true"
          class="btn btn-primary"
        >
          <i class="bi bi-plus-lg me-2"></i>Create New Task
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-md-4">
        <label class="form-label">Status</label>
        <select
          v-model="filters.status"
          class="form-select"
        >
          <option value="">All Status</option>
          <option value="pending">Pending</option>
          <option value="in_progress">In Progress</option>
          <option value="completed">Completed</option>
        </select>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Priority</label>
        <select
          v-model="filters.priority"
          class="form-select"
        >
          <option value="">All Priorities</option>
          <option v-for="n in 6" :key="n-1" :value="n-1">Priority {{ n-1 }}</option>
        </select>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Sort By</label>
        <select
          v-model="sortBy"
          class="form-select"
        >
          <option value="created_at">Created Date</option>
          <option value="due_date">Due Date</option>
          <option value="priority">Priority</option>
          <option value="status">Status</option>
        </select>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary loading-spinner" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="alert alert-danger" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>
      {{ error }}
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredTasks.length === 0" class="text-center py-5">
      <div class="display-1 text-muted mb-4">
        <i class="bi bi-inbox"></i>
      </div>
      <h3 class="h4 mb-3">No tasks found</h3>
      <p class="text-muted">
        {{ filters.status || filters.priority ? 'Try changing your filters or' : 'Get started by' }} creating a new task.
      </p>
    </div>

    <!-- Tasks Grid -->
    <div v-else class="row g-4">
      <div
        v-for="task in filteredTasks"
        :key="task.id"
        class="col-12 col-md-6 col-lg-4"
      >
        <div class="card h-100 task-card shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <h3 class="h5 card-title mb-0">{{ task.title }}</h3>
              <div 
                class="priority-badge"
                :class="{
                  'bg-danger text-white': task.priority >= 4,
                  'bg-warning text-dark': task.priority === 3,
                  'bg-info text-white': task.priority === 2,
                  'bg-secondary text-white': task.priority <= 1
                }"
              >
                {{ task.priority }}
              </div>
            </div>
            
            <p class="card-text text-muted mb-3">{{ task.description }}</p>
            
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span 
                class="status-badge"
                :class="{
                  'bg-warning bg-opacity-10 text-warning': task.status === 'pending',
                  'bg-primary bg-opacity-10 text-primary': task.status === 'in_progress',
                  'bg-success bg-opacity-10 text-success': task.status === 'completed'
                }"
              >
                {{ task.status }}
              </span>
              <small class="text-muted" v-if="task.due_date">
                Due: {{ new Date(task.due_date).toLocaleDateString() }}
              </small>
            </div>

            <div class="d-flex gap-2 justify-content-end">
              <button
                @click="editTask(task)"
                class="btn btn-outline-primary btn-sm"
              >
                <i class="bi bi-pencil me-1"></i>Edit
              </button>
              <button
                @click="deleteTask(task.id)"
                class="btn btn-outline-danger btn-sm"
              >
                <i class="bi bi-trash me-1"></i>Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <TaskModal
      :show="showCreateModal || showEditModal"
      :task="selectedTask"
      @close="closeModal"
      @submit="handleSubmit"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import TaskModal from '@/Components/TaskModal.vue'
import axios from 'axios'

const props = defineProps({
    tasks: {
        type: Array,
        required: true
    }
});

const tasks = ref(props.tasks);
const loading = ref(true)
const error = ref(null)
const showCreateModal = ref(false)
const showEditModal = ref(false)
const selectedTask = ref(null)
const filters = ref({
  status: '',
  priority: ''
})
const sortBy = ref('created_at')

const filteredTasks = computed(() => {
  let result = [...tasks.value]

  // Apply filters
  if (filters.value.status) {
    result = result.filter(task => task.status === filters.value.status)
  }
  if (filters.value.priority !== '') {
    result = result.filter(task => task.priority === parseInt(filters.value.priority))
  }

  // Apply sorting
  result.sort((a, b) => {
    switch (sortBy.value) {
      case 'due_date':
        return new Date(a.due_date || '9999-12-31') - new Date(b.due_date || '9999-12-31')
      case 'priority':
        return b.priority - a.priority
      case 'status':
        return a.status.localeCompare(b.status)
      default:
        return new Date(b.created_at) - new Date(a.created_at)
    }
  })

  return result
})

const fetchTasks = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await axios.get('/api/tasks')
    tasks.value = response.data
  } catch (err) {
    error.value = 'Failed to load tasks. Please try again later.'
    console.error('Error fetching tasks:', err)
  } finally {
    loading.value = false
  }
}

const editTask = (task) => {
  selectedTask.value = task
  showEditModal.value = true
}

const deleteTask = async (taskId) => {
  if (confirm('Are you sure you want to delete this task?')) {
    try {
      await axios.delete(`/api/tasks/${taskId}`)
      await fetchTasks()
    } catch (err) {
      error.value = 'Failed to delete task. Please try again later.'
      console.error('Error deleting task:', err)
    }
  }
}

const closeModal = () => {
  showCreateModal.value = false
  showEditModal.value = false
  selectedTask.value = null
}

const handleSubmit = async (formData) => {
  try {
    if (selectedTask.value) {
      await axios.put(`/api/tasks/${selectedTask.value.id}`, formData)
    } else {
      await axios.post('/api/tasks', formData)
    }
    await fetchTasks()
    closeModal()
  } catch (err) {
    error.value = 'Failed to save task. Please try again later.'
    console.error('Error saving task:', err)
  }
}

// Listen for real-time updates
onMounted(() => {
    fetchTasks();
    
    // Enable pusher logging
    window.Pusher.logToConsole = true;
    console.log('🔌 Initializing Pusher connection...');

    // Log Pusher connection status
    window.Echo.connector.pusher.connection.bind('connected', () => {
        console.log('✅ Pusher Connection Status:', {
            status: 'connected',
            socket_id: window.Echo.connector.pusher.connection.socket_id,
            timestamp: new Date().toISOString()
        });
    });

    window.Echo.connector.pusher.connection.bind('disconnected', () => {
        console.log('❌ Pusher Connection Status:', {
            status: 'disconnected',
            timestamp: new Date().toISOString()
        });
    });

    // Subscribe to the channel
    const channel = window.Echo.channel('tasks');
    console.log('📡 Subscribing to tasks channel...');
    
    // Handle task created event
    channel.listen('.task-created', (data) => {
        console.log('📥 Received task-created event:', {
            task_id: data.task.id,
            task_title: data.task.title,
            task_status: data.task.status,
            task_priority: data.task.priority,
            task_due_date: data.task.due_date,
            timestamp: new Date().toISOString(),
            full_data: data
        });
        
        // Add new task to the beginning of the list
        tasks.value.unshift(data.task);
        console.log('📊 Tasks list updated with new task');
        
        // Show success message
        showNotification('Task created successfully!', 'success');
    });

    // Handle task updated event
    channel.listen('.task-updated', (data) => {
        console.log('📥 Received task-updated event:', {
            task_id: data.task.id,
            task_title: data.task.title,
            task_status: data.task.status,
            task_priority: data.task.priority,
            task_due_date: data.task.due_date,
            timestamp: new Date().toISOString(),
            full_data: data
        });
        
        // Update existing task in the list
        const index = tasks.value.findIndex(t => t.id === data.task.id);
        if (index !== -1) {
            tasks.value[index] = data.task;
            console.log('📊 Tasks list updated with modified task');
            
            // Show success message
            showNotification('Task updated successfully!', 'success');
        } else {
            console.warn('⚠️ Task not found in local list:', data.task.id);
            // Add task if not found (might have been created in another session)
            tasks.value.unshift(data.task);
            showNotification('Task added to your list!', 'info');
        }
    });

    // Handle task deleted event
    channel.listen('.task-deleted', (data) => {
        console.log('📥 Received task-deleted event:', {
            task_id: data.task.id,
            task_title: data.task.title,
            task_status: data.task.status,
            task_priority: data.task.priority,
            task_due_date: data.task.due_date,
            timestamp: new Date().toISOString(),
            full_data: data
        });
        
        // Remove task from the list
        const initialLength = tasks.value.length;
        tasks.value = tasks.value.filter(t => t.id !== data.task.id);
        console.log('📊 Tasks list updated:', {
            initial_count: initialLength,
            final_count: tasks.value.length,
            removed_task_id: data.task.id
        });
        
        // Show success message
        showNotification('Task deleted successfully!', 'success');
    });

    // Log subscription status
    window.Echo.connector.pusher.connection.bind('subscription_succeeded', (data) => {
        console.log('✅ Channel Subscription Status:', {
            channel: data.channel,
            timestamp: new Date().toISOString()
        });
    });

    // Log any errors
    window.Echo.connector.pusher.connection.bind('error', (error) => {
        console.error('❌ Pusher Error:', {
            error: error,
            timestamp: new Date().toISOString()
        });
        showNotification('Connection error occurred!', 'error');
    });

    // Log connection state changes
    window.Echo.connector.pusher.connection.bind('state_change', (states) => {
        console.log('🔄 Connection State Change:', {
            previous: states.previous,
            current: states.current,
            timestamp: new Date().toISOString()
        });
    });
});

// Add notification function
const showNotification = (message, type = 'info') => {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
    notification.style.zIndex = '9999';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    // Add to DOM
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
};
</script> 