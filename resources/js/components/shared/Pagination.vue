<script setup lang="ts" generic="T">
import { Button } from '@/components/ui/button';
import { Pagination, PaginationContent } from '@/components/ui/pagination';
import type { PaginateData } from '@/types/PaginateData';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        siblingCount?: number;
        paginateData: PaginateData<T>;
    }>(),
    {
        siblingCount: 1,
    },
);

const paginateLinks = computed(() => props.paginateData.links);
</script>

<template>
    <Pagination :items-per-page="paginateData.per_page" :total="paginateData.total" :sibling-count="props.siblingCount" show-edges>
        <PaginationContent class="flex items-center gap-1">
            <template v-for="(link, index) in paginateLinks" :key="index">
                <Link v-if="link.url" :href="link.url" as-child>
                    <Button :variant="link.active ? 'default' : 'outline'" class="cursor-pointer">
                        <span v-html="link.label"></span>
                    </Button>
                </Link>
                <Button v-else :variant="link.label === '...' ? 'ghost' : 'outline'" :disabled="!link.url">
                    <span v-html="link.label"></span>
                </Button>
            </template>
        </PaginationContent>
    </Pagination>
</template>
