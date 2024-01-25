<script lang="ts" setup>
import { useAttrs } from 'vue'
import { useFormField } from './useFormField'
import { cn } from '@/lib/utils'

defineOptions({
  inheritAttrs: false,
})

const { formDescriptionId } = useFormField()
const { class: className, ...rest } = useAttrs()
</script>

<template>
  <p
    :id="formDescriptionId"
    :class="cn('text-sm text-slate-500 dark:text-slate-400', className ?? '')"
    v-bind="rest"
  >
    <slot />
  </p>
</template>
