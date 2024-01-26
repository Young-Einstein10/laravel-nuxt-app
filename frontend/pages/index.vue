<template>
  <main class="bg-white p-10 min-h-screen">
    <div class="max-w-4xl mx-auto">
      <header class="flex justify-between items-center mb-20">
        <h1 class="font-semibold text-4xl">My Library</h1>
        <Button @click="toggleAddBookDrawer">Add New Book</Button>
      </header>

      <div v-if="isLoading">
        <p class="italic text-2xl font-medium text-center">Loading...</p>
      </div>
      <div class="mt-8">
        <div class="flex flex-col gap-8">
          <div class="flex gap-4" v-for="book in books">
            <div
              v-if="book.cover_image"
              class="rounded-lg w-32 h-44 flex items-center justify-center"
            >
              <NuxtImg
                :src="`${API_BASE_URL}/images/${book.cover_image}`"
                :alt="book.title"
                class="w-full h-auto"
              />
            </div>
            <div v-else class="bg-slate-300 rounded-lg w-32 h-44"></div>
            <div class="flex items-center justify-between flex-1">
              <div>
                <h3 class="font-medium text-xl mb-4">{{ book.title }}</h3>
                <p class="mb-2">
                  <span>by:</span>
                  <span class="italic"> {{ book.author }} </span>
                </p>
                <p>
                  <span>Published:</span>
                  {{ book.published_date }}
                </p>
              </div>
              <div class="actions flex gap-4">
                <Button @click="toggleEditBookDrawer(book)">Edit</Button>
                <Button @click="toggleDeleteDialog(book)" variant="destructive">
                  Delete
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script lang="ts" setup>
import { API_BASE_URL } from "@/utils/constants";
import type { BookProps } from "@/utils/types";

const isLoading = ref(false);
const books = ref<BookProps[]>([]);
const currentBook = ref<BookProps>();

const isBookDrawerOpen = ref(false);
const isDeleteBookDialogOpen = ref(false);

const toggleAddBookDrawer = () => {
  isBookDrawerOpen.value = !isBookDrawerOpen.value;
};

const toggleEditBookDrawer = (book: BookProps) => {
  currentBook.value = book;
  isBookDrawerOpen.value = !isBookDrawerOpen.value;
};

const toggleDeleteDialog = (book: BookProps) => {
  currentBook.value = book;
  isDeleteBookDialogOpen.value = !isDeleteBookDialogOpen.value;
};

const fetchBooks = async () => {
  try {
    isLoading.value = true;
    const response = await $fetch<{ books: BookProps[] }>(
      `${API_BASE_URL}/api/books`
    );

    books.value = response.books.sort(
      (a, b) =>
        Number(new Date(b.created_at as string)) -
        Number(new Date(a.created_at as string))
    );
  } catch (error) {
    console.log(error);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchBooks();
});
</script>
