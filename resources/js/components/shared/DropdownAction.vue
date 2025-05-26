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

interface Action {
    name: string;
    url: string;
    method?: 'get' | 'post' | 'put' | 'patch' | 'delete';
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
        <DropdownMenuContent class="w-56">
            <DropdownMenuLabel>Action</DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuGroup v-for="action in actions" :key="action.name">
                <Link :href="action.url" :method="action.method" as-child class="w-full">
                    <DropdownMenuItem>
                        <span>{{ action.name }}</span>
                    </DropdownMenuItem>
                </Link>
            </DropdownMenuGroup>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
