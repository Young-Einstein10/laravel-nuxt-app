<script lang="ts" setup>
const emit = defineEmits(["refresh-data", "update:open"]);
const props = defineProps<{ open: boolean; book?: BookProps }>();

const refreshData = () => emit("refresh-data");
const closeDialog = () => emit("update:open", false);

const deleteBook = async () => {
  await $fetch(`${API_BASE_URL}/api/books/${props?.book?.id}`, {
    method: "DELETE",
  });
  closeDialog();
  refreshData();
};
</script>

<template>
  <AlertDialog
    :open="open"
    @update:open="(openState) => $emit('update:open', openState)"
  >
    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
        <AlertDialogDescription>
          This action cannot be undone. This will permanently delete your book
          and remove your data from the server.
        </AlertDialogDescription>
      </AlertDialogHeader>
      <AlertDialogFooter>
        <AlertDialogCancel @click="$emit('update:open', false)"
          >Cancel</AlertDialogCancel
        >
        <Button @click="deleteBook"> Continue </Button>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>
