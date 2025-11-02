<script setup lang="ts">
import { ref, watch, onMounted, nextTick } from 'vue'
import { Input } from '@/components/ui/input'
import { LoaderCircle, Search } from 'lucide-vue-next'
import {
  Popover,
  PopoverTrigger,
  PopoverContent,
} from '@/components/ui/popover'
import { Button } from '@/components/ui/button'

const props = defineProps<{
  label: string
  placeholder?: string
  fetchUrl: string
  modelValue: number | null | undefined
  error?: string
  disabled?: boolean
}>()

const emit = defineEmits(['update:modelValue'])

const search = ref('')
const open = ref(false)
const items = ref<{ id: number; name: string }[]>([])
const nextPage = ref<string | null>(null)
const loading = ref(false)
const selectedLabel = ref('')
let debounceTimer: ReturnType<typeof setTimeout> | null = null
const searchInputRef = ref<HTMLInputElement | null>(null)

// --- Fetch paginated data ---
const load = async (url?: string) => {
  if (loading.value) return
  loading.value = true
  const endpoint = url || `${props.fetchUrl}?search=${encodeURIComponent(search.value)}`
  const res = await fetch(endpoint)
  const data = await res.json()
  if (url) {
    items.value.push(...data.data)
  } else {
    items.value = data.data
  }
  nextPage.value = data.next_page_url
  loading.value = false
}

// --- Fetch a specific item by ID (for edit mode prefill) ---
const loadSelectedItem = async (id: number) => {
  try {
    const res = await fetch(`${props.fetchUrl}/${id}`)
    if (!res.ok) return
    const data = await res.json()
    const existing = items.value.find(i => i.id === data.id)
    if (!existing) {
      items.value.unshift(data)
    }
    selectedLabel.value = data.name
  } catch (e) {
    console.warn('Failed to preload selected item', e)
  }
}

const loadMore = async () => {
  if (nextPage.value) await load(nextPage.value)
}

const handleScroll = (e: Event) => {
  const target = e.target as HTMLElement
  const bottom = target.scrollTop + target.clientHeight >= target.scrollHeight - 20
  if (bottom && nextPage.value) loadMore()
}

// --- Debounce search ---
watch(search, () => {
  if (debounceTimer) clearTimeout(debounceTimer)
  debounceTimer = setTimeout(async () => {
    await load()
    await nextTick()
    searchInputRef.value?.focus()
  }, 600)
})

// --- Sync selected label ---
watch(() => props.modelValue, async (val) => {
  if (!val) {
    selectedLabel.value = ''
    return
  }
  const found = items.value.find(i => i.id === val)
  if (found) {
    selectedLabel.value = found.name
  } else {
    await loadSelectedItem(val)
  }
})

onMounted(async () => {
  await load()
  if (props.modelValue) {
    await loadSelectedItem(props.modelValue)
  }
})
</script>

<template>
  <div class="grid gap-2">
    <label class="text-sm font-medium">{{ props.label }}</label>
    <Popover v-model:open="open">
      <PopoverTrigger as-child>
        <Button variant="outline" class="w-full justify-between truncate" :disabled="props.disabled">
          <span>{{ selectedLabel || props.placeholder || 'Select...' }}</span>
          <Search class="w-4 h-4 opacity-50" />
        </Button>
      </PopoverTrigger>
      <PopoverContent class="w-100 p-0">
        <div class="p-2 border-b sticky top-0 bg-background z-10">
          <Input ref="searchInputRef" v-model="search" placeholder="Search..." class="text-sm" :disabled="loading" />
        </div>

        <div class="max-h-60 overflow-y-auto" @scroll.passive="handleScroll">
          <div v-for="item in items" :key="item.id" class="px-3 py-2 border-b cursor-pointer hover:bg-accent text-sm"
            @click="() => {
              emit('update:modelValue', item.id)
              selectedLabel = item.name
              open = false
            }">
            {{ item.name }}
          </div>

          <div v-if="loading" class="flex items-center justify-center py-2">
            <LoaderCircle class="w-4 h-4 animate-spin" />
          </div>

          <div v-if="!loading && items.length === 0" class="text-center text-sm text-muted-foreground py-2">
            No results found
          </div>
        </div>
      </PopoverContent>
    </Popover>
    <p v-if="props.error" class="text-xs text-red-500">{{ props.error }}</p>
  </div>
</template>
