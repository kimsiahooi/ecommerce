<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Link } from '@inertiajs/vue3';
import { Ellipsis } from 'lucide-vue-next';
import type { HTMLAttributes } from 'vue';

interface Action {
    name: string;
    link?: {
        url: string;
        method?: 'get' | 'post' | 'put' | 'patch' | 'delete';
    };
    itemClass?: HTMLAttributes['class'];
    onClick?: (...args: unknown[]) => unknown;
}

defineProps<{
    actions: Action[];
}>();
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" size="icon" class="cursor-pointer"> <Ellipsis /> </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent class="w-40">
            <DropdownMenuLabel class="font-semibold">Actions</DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuGroup v-for="action in actions" :key="action.name">
                <Link v-if="action.link" :href="action.link.url" :method="action.link.method" as-child class="w-full">
                    <DropdownMenuItem :class="action.itemClass">
                        <span>{{ action.name }}</span>
                    </DropdownMenuItem>
                </Link>
                <DropdownMenuItem v-else @click="action.onClick" :class="action.itemClass">
                    <span>{{ action.name }}</span>
                </DropdownMenuItem>
            </DropdownMenuGroup>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
