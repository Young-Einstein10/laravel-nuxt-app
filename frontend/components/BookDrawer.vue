<script setup lang="ts">
import type { BookProps } from "@/utils/types";
import { API_BASE_URL } from "@/utils/constants";

type PickedProps = "title" | "author" | "isbn" | "published_date";
interface CustomBookProps extends Pick<BookProps, PickedProps> {
  cover_image?: string;
}

const props = defineProps<{ open: boolean; book?: BookProps }>();
const emit = defineEmits(["update:open", "refresh-data"]);

let form = reactive<CustomBookProps>({
  title: "",
  author: "",
  isbn: "",
  published_date: "",
  cover_image: undefined,
});

const onFileChange = async (e: any) => {
  form.cover_image = e.target.files[0];
};

const isSubmitting = ref(false);

const addNewBook = async (data: any) => {
  const response = await $fetch(`${API_BASE_URL}/api/books`, {
    method: "POST",
    body: data,
    headers: {
      Accept: "application/json",
    },
  });
  return response;
};

onMounted(() => {
  if (props.book) {
    form = props.book;
  }
});

const editBook = async () => {
  try {
    isSubmitting.value = true;

    const resp = await $fetch(`${API_BASE_URL}/api/books/${props?.book?.id}`, {
      method: "PUT",
      body: form,
    });
    closeDrawer(false);
    emit("refresh-data");
  } catch (error) {
    console.log(error);
  } finally {
    isSubmitting.value = false;
  }
};

const onSubmit = async () => {
  try {
    const formData = new FormData();
    Object.keys(form).forEach((key) => {
      // @ts-ignore
      if (form[key]) {
        formData.append(key, form[key as never]);
      }
    });
    isSubmitting.value = true;
    const data = await addNewBook(formData);
    closeDrawer(false);
    emit("refresh-data");
  } catch (error) {
    console.log(error);
  } finally {
    isSubmitting.value = false;
  }
};

const closeDrawer = (openState: boolean) => emit("update:open", openState);
</script>

<template>
  <Sheet :open="open" @update:open="closeDrawer">
    <SheetContent class="w-full bg-white">
      <SheetHeader>
        <SheetTitle>
          <template v-if="book"> Edit Book </template>
          <template v-else>Add New Book</template>
        </SheetTitle>
        <SheetDescription>
          Make changes to your profile here. Click save when you're done.
        </SheetDescription>
      </SheetHeader>
      <div class="grid gap-4 py-4">
        <div class="grid grid-cols-4 items-center gap-4">
          <Label for="bookTitle" class="text-right"> Book Title </Label>
          <Input
            v-model:model-value="form.title"
            type="text"
            id="bookTitle"
            class="col-span-3"
            required
          />
        </div>
        <div class="grid grid-cols-4 items-center gap-4">
          <Label for="author" class="text-right"> Author </Label>
          <Input
            v-model:model-value="form.author"
            type="text"
            id="author"
            class="col-span-3"
            required
          />
        </div>
        <div class="grid grid-cols-4 items-center gap-4">
          <Label for="isbn" class="text-right"> ISBN </Label>
          <Input
            v-model:model-value="form.isbn"
            type="text"
            id="isbn"
            class="col-span-3"
            required
          />
        </div>
        <div class="grid grid-cols-4 items-center gap-4">
          <Label for="published_date" class="text-right">
            Published Date
          </Label>
          <Input
            v-model:model-value="form.published_date"
            type="date"
            id="published_date"
            class="col-span-3"
            required
          />
        </div>
        <div class="grid grid-cols-4 items-center gap-4">
          <Label for="cover_image" class="text-right"> Book Cover </Label>

          <Input
            type="file"
            @change="onFileChange"
            id="cover_image"
            class="col-span-3"
            required
          />
        </div>
      </div>
      <SheetFooter>
        <Button
          type="submit"
          @click="() => (book?.id ? editBook() : onSubmit())"
        >
          <template v-if="isSubmitting">Saving...</template>
          <template v-else> Save changes </template>
        </Button>
      </SheetFooter>
    </SheetContent>
  </Sheet>
</template>
