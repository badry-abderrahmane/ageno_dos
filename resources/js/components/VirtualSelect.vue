<script setup lang="ts">
import { ref, watch, onMounted, nextTick } from 'vue'
import { LoaderCircle, Search, Check, X } from 'lucide-vue-next'
import {
  Combobox,
  ComboboxAnchor,
  ComboboxEmpty,
  ComboboxGroup,
  ComboboxInput,
  ComboboxTrigger,
  ComboboxItem,
  ComboboxItemIndicator,
  ComboboxList,
} from '@/components/ui/combobox'

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
const items = ref<{ id: number; name: string }[]>([])
const nextPage = ref<string | null>(null)
const loading = ref(false)
const selected = ref()
let debounceTimer: ReturnType<typeof setTimeout> | null = null

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
    selected.value = data
  } catch (e) {
    console.warn('Failed to preload selected item', e)
  }
}

// --- Debounce search ---
watch(search, () => {
  if (debounceTimer) clearTimeout(debounceTimer)
  debounceTimer = setTimeout(async () => {
    await load()
    await nextTick()
    //searchInputRef.value?.focus()
  }, 600)
})

// --- Sync selected label ---
watch(() => props.modelValue, async (val) => {
  if (!val) {
    selected.value = undefined
    return
  }
  const found = items.value.find(i => i.id === val)
  if (found) {
    selected.value = found
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
    <label class="text-sm font-bold">{{ props.label }}</label>
    <Combobox v-model="selected" @update:model-value="(val: any) => emit('update:modelValue', val?.id || '')">
      <ComboboxAnchor class="w-auto">
        <ComboboxTrigger as-child>
          <div class="relative w-full items-center">
            <ComboboxInput v-model="search" class="pl-9 pr-8 truncate" :display-value="(val) => val?.name ?? ''"
              placeholder="Select product..." />
            <span class="absolute start-0 inset-y-0 flex items-center justify-center px-3">
              <Search class="size-4 text-muted-foreground" />
            </span>
            <span v-if="selected || search" @click="emit('update:modelValue', undefined); search = ''"
              class="absolute end-0 inset-y-0 flex items-center justify-center px-3">
              <X class="size-4 text-muted-foreground" />
            </span>
          </div>
        </ComboboxTrigger>

      </ComboboxAnchor>
      <ComboboxList class="w-[270px]">
        <ComboboxEmpty>
          <div v-if="loading" class="flex items-center justify-center py-2">
            <LoaderCircle class="w-4 h-4 animate-spin" />
          </div>

          <div v-if="!loading && items.length === 0" class="text-center text-sm text-muted-foreground py-2">
            No results found
          </div>
        </ComboboxEmpty>
        <ComboboxGroup class="max-h-50 overflow-y-auto">
          <ComboboxItem v-for="item in items" :key="item.name" :value="item" class="border-b">
            {{ item.name }}
            <ComboboxItemIndicator>
              <Check class="ml-auto size-4" />
            </ComboboxItemIndicator>
          </ComboboxItem>
        </ComboboxGroup>
      </ComboboxList>
    </Combobox>

    <!-- <Popover v-model:open="open">
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
    </Popover> -->
    <p v-if="props.error" class="text-xs text-red-500">{{ props.error }}</p>
  </div>
</template>
