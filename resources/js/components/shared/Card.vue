<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';

defineProps<{
    contentClass?: string;
}>();
</script>

<template>
    <Card class="py-0">
        <CardContent class="px-0" :class="contentClass">
            <slot />
        </CardContent>
    </Card>
</template>
