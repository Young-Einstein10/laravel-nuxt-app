<script setup lang="ts">
import {
  AlertDialogDescription,
  type AlertDialogDescriptionProps,
} from 'radix-vue'
import { cn } from '@/lib/utils'

const props = defineProps<AlertDialogDescriptionProps & { class?: string }>()
</script>

<template>
  <AlertDialogDescription
    :class="cn('text-slate-500 text-sm dark:text-slate-400', props.class)"
    :as-child="props.asChild"
  >
    <slot />
  </AlertDialogDescription>
</template>
