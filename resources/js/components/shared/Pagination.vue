<script setup lang="ts" generic="T">
import PaginationButton from '@/components/shared/PaginationButton.vue';
import { Button } from '@/components/ui/button';
import { Pagination, PaginationContent, PaginationEllipsis } from '@/components/ui/pagination';
import type { PaginateData } from '@/types/PaginateData';
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-vue-next';

withDefaults(
    defineProps<{
        siblingCount?: number;
        paginateData: PaginateData<T>;
    }>(),
    {
        siblingCount: 1,
    },
);
</script>

<template>
    <Pagination
        :items-per-page="paginateData.per_page"
        :total="paginateData.total"
        :sibling-count="1"
        :default-page="paginateData.current_page"
        show-edges
        v-slot="{ page }"
    >
        <PaginationContent class="flex items-center gap-1">
            <PaginationContent v-slot="{ items }">
                <Link :href="paginateData.first_page_url" as-child>
                    <Button size="icon" variant="outline" class="cursor-pointer">
                        <ChevronsLeft />
                    </Button>
                </Link>
                <Link v-if="paginateData.prev_page_url" :href="paginateData.prev_page_url" as-child>
                    <Button size="icon" variant="outline" class="cursor-pointer">
                        <ChevronLeft />
                    </Button>
                </Link>
                <Button v-else size="icon" variant="outline" class="cursor-pointer" disabled>
                    <ChevronLeft />
                </Button>
                <template v-for="(item, index) in items" :key="index">
                    <template v-if="item.type === 'page'">
                        <PaginationButton :value="item.value" :is-active="item.value === page" :links="paginateData.links" />
                    </template>
                    <PaginationEllipsis v-else />
                </template>
                <Link v-if="paginateData.next_page_url" :href="paginateData.next_page_url" as-child>
                    <Button size="icon" variant="outline" class="cursor-pointer">
                        <ChevronRight />
                    </Button>
                </Link>
                <Button v-else size="icon" variant="outline" class="cursor-pointer" disabled>
                    <ChevronRight />
                </Button>
                <Link :href="paginateData.last_page_url" as-child>
                    <Button size="icon" variant="outline" class="cursor-pointer">
                        <ChevronsRight />
                    </Button>
                </Link>
            </PaginationContent>
        </PaginationContent>
    </Pagination>
</template>
