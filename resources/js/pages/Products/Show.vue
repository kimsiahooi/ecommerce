<script setup lang="ts">
import Card from '@/components/shared/Card.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { useDateFormat } from '@/composables/useDateFormat';
import AppLayout from '@/layouts/AppLayout.vue';
import AppMainLayout from '@/layouts/AppMainLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { Product } from '@/types/Product';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';

defineOptions({
    layout: AppMainLayout,
});

const props = defineProps<{
    product: Product;
}>();

const { formatDate } = useDateFormat();

const deleteProduct = reactive({
    dialogIsOpen: false,
    isDeleting: false,
});

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
        title: props.product.name,
        href: route('products.show', { product: props.product.id }),
    },
];

const setDeleteProduct = () => {
    deleteProduct.dialogIsOpen = true;
};

const resetDeleteProduct = () => {
    deleteProduct.dialogIsOpen = false;
};

const deleteProductHandler = () => {
    router.visit(route('products.destroy', { product: props.product.id }), {
        method: 'delete',
        onBefore: () => {
            deleteProduct.isDeleting = true;
        },
        onFinish: () => {
            deleteProduct.isDeleting = false;
        },
    });
};
</script>

<template>
    <Head :title="product.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-5">
                <div class="flex flex-wrap items-center justify-end gap-2">
                    <Link :href="route('products.edit', { product: product.id })" as-child>
                        <Button class="cursor-pointer">Edit Product</Button>
                    </Link>
                    <Button class="cursor-pointer" variant="destructive" @click="setDeleteProduct">Delete Product</Button>
                </div>
                <Card>
                    <div class="border-secondary border-b p-4">
                        <h6 class="font-semibold">Product Details</h6>
                    </div>
                    <div class="p-4">
                        <div class="space-y-5">
                            <div class="grid w-full items-center gap-2">
                                <Label>Name</Label>
                                <p>{{ product.name }}</p>
                            </div>
                            <div class="grid w-full items-center gap-2">
                                <Label>Description</Label>
                                <p>{{ product.description || '-' }}</p>
                            </div>
                            <div class="grid w-full items-center gap-2">
                                <Label>Slug</Label>
                                <p>{{ product.slug }}</p>
                            </div>
                            <div class="grid w-full items-center gap-2">
                                <Label>Price</Label>
                                <p>{{ product.price || '-' }}</p>
                            </div>
                            <div class="grid w-full items-center gap-2">
                                <Label>Feature Image</Label>
                                <div v-if="product.feature_image">
                                    <img class="max-h-60 max-w-60" :src="product.feature_image" :alt="product.name" />
                                </div>
                                <p v-else>-</p>
                            </div>
                            <div class="grid w-full items-center gap-2">
                                <Label>Created At</Label>
                                <p>{{ product.created_at ? formatDate(product.created_at) : '-' }}</p>
                            </div>
                            <div class="grid w-full items-center gap-2">
                                <Label>Updated At</Label>
                                <p>{{ product.updated_at ? formatDate(product.updated_at) : '-' }}</p>
                            </div>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
        <Dialog :open="deleteProduct.dialogIsOpen">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Delete {{ product.name }}</DialogTitle>
                    <DialogDescription> Are you sure you want to delete this product? </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button class="cursor-pointer" variant="secondary" @click="resetDeleteProduct">Cancel</Button>
                    <Button class="cursor-pointer" variant="destructive" :disabled="deleteProduct.isDeleting" @click="deleteProductHandler">
                        Delete
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
