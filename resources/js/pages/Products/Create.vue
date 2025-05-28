<script setup lang="ts">
import Card from '@/components/shared/Card.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import slug from 'slug';
import { watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
    {
        title: 'Products',
        href: route('products.index'),
    },
    {
        title: 'Create',
        href: route('products.create'),
    },
];

const form = useForm<{
    name: string;
    description: string;
    slug: string;
    price: string;
    feature_image: File | null;
}>({
    name: '',
    description: '',
    slug: '',
    price: '',
    feature_image: null,
});

const imageHandler = (event: Event) => {
    const files = (<HTMLInputElement>event.target).files;

    if (files?.length) {
        form.feature_image = files[0];
    }
};

const submit = () =>
    form.post(route('products.store'), {
        onSuccess: () => {
            form.reset();
        },
    });

watch(
    () => form.name,
    (newName) => {
        form.slug = slug(newName);
    },
);
</script>

<template>
    <Head title="Create Product" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-5">
                <Card>
                    <div class="border-secondary border-b p-4">
                        <h6 class="font-semibold">Create Product</h6>
                    </div>
                    <div class="p-4">
                        <form @submit.prevent="submit">
                            <div class="space-y-5">
                                <div class="grid w-full items-center gap-2">
                                    <Label>Name</Label>
                                    <Input type="text" placeholder="Enter Name" v-model="form.name" />
                                    <p v-if="form.errors.name" class="text-destructive">{{ form.errors.name }}</p>
                                </div>
                                <div class="grid w-full items-center gap-2">
                                    <Label>Description</Label>
                                    <Textarea placeholder="Enter Description" v-model="form.description" />
                                    <p v-if="form.errors.description" class="text-destructive">{{ form.errors.description }}</p>
                                </div>
                                <div class="grid w-full items-center gap-2">
                                    <Label>Slug</Label>
                                    <Input type="text" placeholder="Enter Slug" v-model="form.slug" />
                                    <p v-if="form.errors.slug" class="text-destructive">{{ form.errors.slug }}</p>
                                </div>
                                <div class="grid w-full items-center gap-2">
                                    <Label>Price</Label>
                                    <Input type="number" min="0.01" step="0.01" placeholder="Enter Price" v-model="form.price" />
                                    <p v-if="form.errors.price" class="text-destructive">{{ form.errors.price }}</p>
                                </div>
                                <div class="grid w-full items-center gap-2">
                                    <Label>Feature Image</Label>
                                    <Input type="file" accept="image/*" @change="imageHandler" />
                                    <p v-if="form.errors.feature_image" class="text-destructive">{{ form.errors.feature_image }}</p>
                                </div>
                                <div>
                                    <Button type="submit" class="cursor-pointer" :disabled="form.processing">Create</Button>
                                </div>
                            </div>
                        </form>
                    </div>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
